<?php

use App\Models\User;
use App\Models\ReadingSession;

$user = User::first();
if (!$user) {
    echo "No user found.\n";
    exit;
}

// Reset reading CEFR to B1 for testing
$user->update(['reading_cefr_level' => 'B1']);
echo "Initial Level: {$user->reading_cefr_level}\n";

// --- TEST PROMOTION ---
echo "\n--- Testing Promotion (Requires 3 sessions >= 80/4) ---\n";
ReadingSession::where('user_id', $user->id)->delete();

for ($i = 0; $i < 3; $i++) {
    ReadingSession::create([
        'user_id' => $user->id,
        'topic' => "Test Promotion $i",
        'cefr_level' => 'B1',
        'estimated_read_time' => 5,
        'article_text' => 'test',
        'article_title' => 'test',
        'article_word_count' => 100,
        'summary_score' => 85,
        'quiz_score' => 4,
        'created_at' => now()->subMinutes(10 - $i),
    ]);
}

// Trigger evaluateLevel manually
$recentSessions = ReadingSession::where('user_id', $user->id)
    ->whereNotNull('quiz_score')
    ->latest()
    ->limit(3)
    ->get();

$levels = ['A1', 'A2', 'B1', 'B2', 'C1', 'C2'];
$currentLevelIdx = array_search($user->reading_cefr_level, $levels);

$promote = true;
foreach ($recentSessions as $rs) {
    if ($rs->summary_score < 80 || $rs->quiz_score < 4) {
        $promote = false;
        break;
    }
}
if ($promote && $currentLevelIdx < 4) {
    $newLevel = $levels[$currentLevelIdx + 1];
    $user->update(['reading_cefr_level' => $newLevel]);
    echo "Promoted to: {$newLevel} (Expected: B2)\n";
} else {
    echo "Failed to promote.\n";
}

// --- TEST DEMOTION ---
echo "\n--- Testing Demotion (Requires 2 sessions < 50 or < 3) ---\n";
ReadingSession::where('user_id', $user->id)->delete();
$user->update(['reading_cefr_level' => 'B2']);

for ($i = 0; $i < 2; $i++) {
    ReadingSession::create([
        'user_id' => $user->id,
        'topic' => "Test Demotion $i",
        'cefr_level' => 'B2',
        'estimated_read_time' => 5,
        'article_text' => 'test',
        'article_title' => 'test',
        'article_word_count' => 100,
        'summary_score' => 40, // < 50
        'quiz_score' => 2, // < 3
        'created_at' => now()->subMinutes(10 - $i),
    ]);
}

$recentSessions = ReadingSession::where('user_id', $user->id)
    ->whereNotNull('quiz_score')
    ->latest()
    ->limit(3)
    ->get();

$currentLevelIdx = array_search($user->reading_cefr_level, $levels);

$lastTwo = $recentSessions->take(2);
$demote = true;
foreach ($lastTwo as $rs) {
    if ($rs->summary_score >= 50 && $rs->quiz_score >= 3) {
        $demote = false;
        break;
    }
}
if ($demote && $currentLevelIdx > 1) {
    $newLevel = $levels[$currentLevelIdx - 1];
    $user->update(['reading_cefr_level' => $newLevel]);
    echo "Demoted to: {$newLevel} (Expected: B1)\n";
} else {
    echo "Failed to demote.\n";
}

// Clean up fake sessions
ReadingSession::where('topic', 'like', 'Test %')->delete();

echo "\nDone testing.\n";
