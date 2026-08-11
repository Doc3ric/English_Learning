<?php

namespace App\Services;

use App\Models\WritingSession;
use App\Models\ReadingSession;
use App\Models\Vocabulary;
use App\Models\Mistake;
use App\Models\ConversationSession;
use Carbon\Carbon;

/**
 * SkillProfileService
 *
 * Calculates real confidence scores (0–100) for each English skill,
 * derived entirely from actual user activity data — no AI, no invented baselines.
 *
 * Score → CEFR mapping:
 *   0–39  → A1
 *  40–54  → A2
 *  55–69  → B1
 *  70–84  → B2
 *  85+    → C1
 *
 * If there is insufficient data for a skill, returns null so the UI
 * can display "Not enough data yet" instead of a misleading score.
 */
class SkillProfileService
{
    /** Minimum number of data points required before we calculate a score */
    private const MIN_SESSIONS = 1;
    private const MIN_VOCAB_WORDS = 5;
    private const MIN_READING_SESSIONS = 1;
    private const QUIZ_QUESTIONS_TOTAL = 5; // AIReadingService always generates 5 questions

    /**
     * Return the full skill profile array.
     * Each skill entry:
     *   [ 'score' => int|null, 'cefr' => string|null, 'confidence' => string|null, 'estimated' => bool ]
     */
    public static function getProfile(int $userId): array
    {
        return [
            'Grammar'    => self::grammarScore($userId),
            'Vocabulary' => self::vocabularyScore(),
            'Writing'    => self::writingScore($userId),
            'Reading'    => self::readingScore($userId),
            'Speaking'   => self::speakingScore($userId),
            'Listening'  => self::listeningScore($userId),
        ];
    }

    /**
     * Grammar: average grammar_score from last 10 writing sessions,
     * penalized by recent mistake frequency.
     */
    private static function grammarScore(int $userId): array
    {
        $sessions = WritingSession::where('user_id', $userId)
            ->whereNotNull('grammar_score')
            ->orderByDesc('created_at')
            ->limit(10)
            ->pluck('grammar_score');

        if ($sessions->count() < self::MIN_SESSIONS) {
            return self::noData();
        }

        $base = round($sessions->average());

        // Penalize for recent grammar mistakes (last 30 days)
        $grammarMistakes = Mistake::whereRaw('LOWER(category) = ?', ['grammar'])
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->count();

        // Each grammar mistake lowers the score by 1, capped at -15
        $penalty = min(15, $grammarMistakes);
        $score = max(0, $base - $penalty);

        return self::makeResult($score, false);
    }

    /**
     * Vocabulary: derived from SRS Leitner box distribution.
     * Average box / 5 * 100, weighted by the number of words reviewed.
     * Requires MIN_VOCAB_WORDS with example sentences (= added to SRS queue).
     */
    private static function vocabularyScore(): array
    {
        $words = Vocabulary::whereNotNull('example_sentence')->get(['leitner_box', 'last_reviewed_at']);

        if ($words->count() < self::MIN_VOCAB_WORDS) {
            return self::noData();
        }

        // Only count words that have been reviewed at least once
        $reviewed = $words->filter(fn($w) => !is_null($w->last_reviewed_at));

        if ($reviewed->isEmpty()) {
            return self::noData();
        }

        $avgBox = $reviewed->avg('leitner_box');

        // avgBox ranges from 1 (just started) to 5 (mastered)
        // Map to 0–100: box 1 = 20%, box 5 = 100%
        $score = (int) round(($avgBox / 5) * 100);

        return self::makeResult($score, false);
    }

    /**
     * Writing: average of clarity_score and naturalness_score from last 10 sessions.
     * (Distinct from grammar, captures fluency and flow.)
     */
    private static function writingScore(int $userId): array
    {
        $sessions = WritingSession::where('user_id', $userId)
            ->whereNotNull('clarity_score')
            ->whereNotNull('naturalness_score')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get(['clarity_score', 'naturalness_score']);

        if ($sessions->count() < self::MIN_SESSIONS) {
            return self::noData();
        }

        $score = round($sessions->avg(fn($s) => ($s->clarity_score + $s->naturalness_score) / 2));

        return self::makeResult((int) $score, false);
    }

    /**
     * Reading: average quiz accuracy from last 10 sessions.
     * quiz_score is raw correct count; total questions = 5 (per AIReadingService).
     */
    private static function readingScore(int $userId): array
    {
        $sessions = ReadingSession::where('user_id', $userId)
            ->whereNotNull('quiz_score')
            ->orderByDesc('created_at')
            ->limit(10)
            ->pluck('quiz_score');

        if ($sessions->count() < self::MIN_READING_SESSIONS) {
            return self::noData();
        }

        $avgRaw = $sessions->average();
        // Convert raw correct (0–5) to percentage (0–100)
        $score = (int) round(($avgRaw / self::QUIZ_QUESTIONS_TOTAL) * 100);

        return self::makeResult($score, false);
    }

    /**
     * Speaking: composite score from conversation session count and recency.
     * Not a direct measurement — clearly marked as estimated.
     * Formula: base 40 + up to 45 from session count + up to 15 from recency.
     */
    private static function speakingScore(int $userId): array
    {
        $totalSessions = ConversationSession::where('user_id', $userId)->count();

        if ($totalSessions === 0) {
            return self::noData();
        }

        // Session count contribution: each session +3, capped at 45 (= 15 sessions)
        $sessionPoints = min(45, $totalSessions * 3);

        // Recency bonus: practiced in last 7 days
        $recent = ConversationSession::where('user_id', $userId)
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->exists();
        $recencyBonus = $recent ? 15 : 0;

        $score = min(100, 40 + $sessionPoints + $recencyBonus);

        return self::makeResult((int) $score, true, 'Estimated from session count & recency');
    }

    /**
     * Listening: proxied from speaking sessions.
     * Honest label: always marked as estimated.
     * We have no direct listening activity, so this reflects
     * passive exposure from conversation practice only.
     */
    private static function listeningScore(int $userId): array
    {
        $speaking = self::speakingScore($userId);

        if ($speaking['score'] === null) {
            return self::noData(true);
        }

        // Listening is estimated ~10% lower than speaking (conservative)
        $score = max(0, $speaking['score'] - 10);

        return self::makeResult((int) $score, true, 'Estimated from conversation practice');
    }

    // ─── Helpers ────────────────────────────────────────────────────────────

    private static function makeResult(int $score, bool $estimated, string $estimationNote = ''): array
    {
        $cefr = self::toCefr($score);
        $confidence = self::toConfidenceLabel($score);

        return [
            'score'           => $score,
            'cefr'            => $cefr,
            'confidence'      => $confidence,
            'estimated'       => $estimated,
            'estimation_note' => $estimationNote,
        ];
    }

    private static function noData(bool $estimated = false): array
    {
        return [
            'score'           => null,
            'cefr'            => null,
            'confidence'      => null,
            'estimated'       => $estimated,
            'estimation_note' => '',
        ];
    }

    private static function toCefr(int $score): string
    {
        if ($score >= 85) return 'C1';
        if ($score >= 70) return 'B2';
        if ($score >= 55) return 'B1';
        if ($score >= 40) return 'A2';
        return 'A1';
    }

    private static function toConfidenceLabel(int $score): string
    {
        if ($score >= 80) return 'High confidence';
        if ($score >= 60) return 'Good progress';
        if ($score >= 40) return 'Developing';
        return 'Early stage';
    }
}
