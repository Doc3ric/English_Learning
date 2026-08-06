<?php

use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\ReadingSession;
use Livewire\Livewire;
use App\Livewire\AiReading\Index;

$user = User::first();
Auth::login($user);

echo "Starting AI Reading test...\n";

// Mock the AI service to prevent real API calls and make it fast
\Illuminate\Support\Facades\Http::fake([
    '*' => \Illuminate\Support\Facades\Http::response([
        'candidates' => [
            [
                'content' => [
                    'parts' => [
                        [
                            'text' => json_encode([
                                'title' => 'Test Article',
                                'article' => 'This is a test article for calculating WPM. It has some words in it.',
                                'cefr_level' => 'B2',
                                'word_count' => 14,
                                'estimated_read_time' => 1,
                                'vocabulary' => []
                            ])
                        ]
                    ]
                ]
            ]
        ]
    ], 200)
]);

$component = Livewire::test(Index::class);

echo "Generating article...\n";
$component->call('generate');

echo "Sleeping for 5 seconds to simulate reading...\n";
sleep(5);

echo "Finishing reading...\n";
$component->call('startSummary');

$session = ReadingSession::latest()->first();
echo "WPM calculated: " . $session->words_per_minute . " (Time taken: " . $session->time_taken_seconds . "s)\n";

