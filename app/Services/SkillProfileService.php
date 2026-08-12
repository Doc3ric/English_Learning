<?php

namespace App\Services;

use App\Models\WritingSession;
use App\Models\ReadingSession;
use App\Models\Vocabulary;
use App\Models\Mistake;
use App\Models\ConversationSession;
use App\Models\ConversationMessage;
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
 *
 * DATA HONESTY POLICY:
 * - Speaking: derived from grammar correction rate in conversation sessions.
 *   Corrections are stored on the AI assistant message that follows each user turn.
 *   "Accuracy" = turns where the AI found zero grammar errors / total user turns.
 *   Minimum: 5 user turns before a score is shown.
 * - Listening: cannot be measured from existing activities. Always returns null.
 *   Displayed as "Cannot be measured yet."
 */
class SkillProfileService
{
    private const MIN_WRITING_SESSIONS   = 1;
    private const MIN_VOCAB_WORDS        = 5;
    private const MIN_READING_SESSIONS   = 1;
    private const QUIZ_QUESTIONS_TOTAL   = 5;  // AIReadingService always generates 5 questions
    private const MIN_SPEAKING_TURNS     = 5;  // Minimum user turns before Speaking score is shown

    /**
     * Return the full skill profile array.
     * Each entry: [ 'score' => int|null, 'cefr' => string|null,
     *               'confidence' => string|null, 'estimated' => bool,
     *               'estimation_note' => string ]
     */
    public static function getProfile(int $userId): array
    {
        return [
            'Grammar'    => self::grammarScore($userId),
            'Vocabulary' => self::vocabularyScore(),
            'Writing'    => self::writingScore($userId),
            'Reading'    => self::readingScore($userId),
            'Speaking'   => self::speakingScore($userId),
            'Listening'  => self::listeningScore(),
        ];
    }

    /**
     * Grammar: average grammar_score from last 10 writing sessions,
     * penalized by recent grammar mistake frequency.
     */
    private static function grammarScore(int $userId): array
    {
        $sessions = WritingSession::where('user_id', $userId)
            ->whereNotNull('grammar_score')
            ->orderByDesc('created_at')
            ->limit(10)
            ->pluck('grammar_score');

        if ($sessions->count() < self::MIN_WRITING_SESSIONS) {
            return self::noData();
        }

        $base = round($sessions->average());

        // Penalize for recent grammar mistakes (last 30 days), capped at -15
        $grammarMistakes = Mistake::whereRaw('LOWER(category) = ?', ['grammar'])
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->count();

        $score = max(0, $base - min(15, $grammarMistakes));

        return self::makeResult((int) $score, false);
    }

    /**
     * Vocabulary: derived from SRS Leitner box distribution.
     * Only counts words that have been reviewed at least once.
     * Box 1 (just started) → 20%, Box 5 (mastered) → 100%.
     */
    private static function vocabularyScore(): array
    {
        $reviewed = Vocabulary::whereNotNull('example_sentence')
            ->whereNotNull('last_reviewed_at')
            ->get(['leitner_box']);

        if ($reviewed->count() < self::MIN_VOCAB_WORDS) {
            return self::noData();
        }

        $avgBox = $reviewed->avg('leitner_box');
        $score  = (int) round(($avgBox / 5) * 100);

        return self::makeResult($score, false);
    }

    /**
     * Writing: average of clarity_score and naturalness_score from last 10 sessions.
     * Distinct from Grammar — captures fluency and flow, not just rule accuracy.
     */
    private static function writingScore(int $userId): array
    {
        $sessions = WritingSession::where('user_id', $userId)
            ->whereNotNull('clarity_score')
            ->whereNotNull('naturalness_score')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get(['clarity_score', 'naturalness_score']);

        if ($sessions->count() < self::MIN_WRITING_SESSIONS) {
            return self::noData();
        }

        $score = (int) round($sessions->avg(fn($s) => ($s->clarity_score + $s->naturalness_score) / 2));

        return self::makeResult($score, false);
    }

    /**
     * Reading: average quiz accuracy across last 10 sessions.
     * quiz_score is the raw correct count (0–5); total is always 5.
     */
    private static function readingScore(int $userId): array
    {
        $scores = ReadingSession::where('user_id', $userId)
            ->whereNotNull('quiz_score')
            ->orderByDesc('created_at')
            ->limit(10)
            ->pluck('quiz_score');

        if ($scores->count() < self::MIN_READING_SESSIONS) {
            return self::noData();
        }

        $score = (int) round(($scores->average() / self::QUIZ_QUESTIONS_TOTAL) * 100);

        return self::makeResult($score, false);
    }

    /**
     * Speaking: calculated from the grammar correction rate across ALL conversation turns.
     *
     * How it works:
     * - Corrections are stored on the AI *assistant* message that follows each user turn.
     * - We count the total number of assistant messages that have ≥1 correction (= a user
     *   turn that contained a grammar error) vs. total assistant messages with corrections
     *   data (= turns the AI reviewed for grammar).
     * - Accuracy = turns with ZERO corrections / total reviewed turns.
     * - This is a direct measure of grammar accuracy in spoken production.
     *
     * Minimum: MIN_SPEAKING_TURNS assistant messages with corrections data.
     * NOT estimated — based on real correction data.
     */
    private static function speakingScore(int $userId): array
    {
        // Get all assistant messages in this user's conversation sessions
        $sessionIds = ConversationSession::where('user_id', $userId)->pluck('id');

        if ($sessionIds->isEmpty()) {
            return self::noData();
        }

        // Assistant messages carry the corrections array for the preceding user turn.
        // We only count messages where corrections is not null (AI actually evaluated grammar).
        $assistantMessages = ConversationMessage::whereIn('session_id', $sessionIds)
            ->where('role', 'assistant')
            ->whereNotNull('corrections')
            ->get(['corrections']);

        $totalReviewed = $assistantMessages->count();

        if ($totalReviewed < self::MIN_SPEAKING_TURNS) {
            return self::noData(
                false,
                "Need {$totalReviewed}/" . self::MIN_SPEAKING_TURNS . " reviewed turns to calculate score"
            );
        }

        // Count turns where the AI found zero corrections (= no errors detected)
        $cleanTurns = $assistantMessages->filter(function ($msg) {
            $corrections = $msg->corrections;
            return empty($corrections);
        })->count();

        $accuracy = (int) round(($cleanTurns / $totalReviewed) * 100);

        // Note: "clean turn" means AI found no errors — this is a measure of
        // grammar accuracy in spoken production, not fluency or vocabulary range.
        return self::makeResult(
            $accuracy,
            false,
            '',
            "Measured: {$cleanTurns}/{$totalReviewed} turns with no grammar corrections"
        );
    }

    /**
     * Listening: cannot be measured from existing activities.
     *
     * The app has no listening comprehension activities yet.
     * Returning null here is the honest choice — no estimation, no proxy.
     * The UI will display "Cannot be measured yet."
     */
    private static function listeningScore(): array
    {
        return self::noData(true, 'No listening activities exist yet');
    }

    // ─── Helpers ────────────────────────────────────────────────────────────

    private static function makeResult(int $score, bool $estimated, string $estimationNote = '', string $measurementNote = ''): array
    {
        return [
            'score'            => $score,
            'cefr'             => self::toCefr($score),
            'confidence'       => self::toConfidenceLabel($score),
            'estimated'        => $estimated,
            'estimation_note'  => $estimationNote,
            'measurement_note' => $measurementNote,
        ];
    }

    private static function noData(bool $cannotMeasure = false, string $note = ''): array
    {
        return [
            'score'            => null,
            'cefr'             => null,
            'confidence'       => null,
            'estimated'        => false,
            'estimation_note'  => $note,
            'measurement_note' => '',
            'cannot_measure'   => $cannotMeasure,
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
