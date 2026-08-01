<?php

namespace App\Livewire\Grammar;

use Livewire\Component;
use App\Models\GrammarLesson;
use App\Models\QuizQuestion;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Index extends Component
{
    public $title = '';
    public $content = '';
    public $showAddForm = false;
    public $isGenerating = false;

    public function save()
    {
        $this->validate([
            'title' => 'required|string',
            'content' => 'required|string',
        ]);

        $maxOrder = GrammarLesson::max('order_index') ?? 0;

        GrammarLesson::create([
            'title' => $this->title,
            'content' => $this->content,
            'order_index' => $maxOrder + 1,
            'is_completed' => false,
            'is_generated' => false,
        ]);

        $this->reset(['title', 'content', 'showAddForm']);
        session()->flash('message', 'Lesson added manually!');
    }

    public function generateNextLesson()
    {
        $this->isGenerating = true;

        $user = auth()->user();
        $level = $user ? $user->level : 'Intermediate';

        $completedLessons = GrammarLesson::where('is_completed', true)->pluck('title')->toArray();
        $completedStr = empty($completedLessons) ? 'None' : implode(', ', $completedLessons);

        $prompt = "You are an expert English grammar teacher. Create a grammar lesson for a student at the {$level} level.
The student has already completed the following lessons: {$completedStr}.
Please provide the next logical grammar topic that builds upon their current knowledge.
Return the response strictly as a JSON object matching this structure (do NOT wrap in markdown code blocks, just raw JSON):
{
    \"title\": \"Lesson Title\",
    \"content\": \"Detailed markdown explanation of the grammar topic, including examples.\",
    \"questions\": [
        {
            \"question\": \"Multiple choice question text\",
            \"option_a\": \"Option A\",
            \"option_b\": \"Option B\",
            \"option_c\": \"Option C\",
            \"option_d\": \"Option D\",
            \"correct_answer\": \"A\",
            \"explanation\": \"Why this answer is correct\"
        }
    ]
}
Make sure to provide exactly 5 questions. The correct_answer field must be exactly one of 'A', 'B', 'C', or 'D'.";

        try {
            $response = Http::withToken(env('GROQ_API_KEY'))
                ->timeout(60)
                ->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model' => 'llama-3.3-70b-versatile',
                    'response_format' => ['type' => 'json_object'],
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'You are an API that outputs strict JSON only. Do not include formatting backticks or explanation text outside the JSON.'
                        ],
                        [
                            'role' => 'user',
                            'content' => $prompt
                        ]
                    ]
                ]);

            if ($response->successful()) {
                $result = $response->json();
                $content = $result['choices'][0]['message']['content'];
                $data = json_decode($content, true);

                if ($data && isset($data['title']) && isset($data['content']) && isset($data['questions'])) {
                    $maxOrder = GrammarLesson::max('order_index') ?? 0;
                    
                    $lesson = GrammarLesson::create([
                        'title' => $data['title'],
                        'content' => $data['content'],
                        'order_index' => $maxOrder + 1,
                        'is_completed' => false,
                        'is_generated' => true,
                    ]);

                    foreach ($data['questions'] as $q) {
                        QuizQuestion::create([
                            'grammar_lesson_id' => $lesson->id,
                            'question' => $q['question'],
                            'option_a' => $q['option_a'] ?? '',
                            'option_b' => $q['option_b'] ?? '',
                            'option_c' => $q['option_c'] ?? '',
                            'option_d' => $q['option_d'] ?? '',
                            'correct_answer' => $q['correct_answer'],
                            'explanation' => $q['explanation'] ?? '',
                        ]);
                    }

                    session()->flash('message', 'AI Lesson generated successfully!');
                } else {
                    session()->flash('error', 'Failed to parse AI response. Please try again.');
                }
            } else {
                Log::error('Groq API Error', ['response' => $response->body()]);
                session()->flash('error', 'API Request failed. Ensure GROQ_API_KEY is set in .env');
            }
        } catch (\Exception $e) {
            Log::error('Grammar Gen Exception: ' . $e->getMessage());
            session()->flash('error', 'An error occurred during generation: ' . $e->getMessage());
        }

        $this->isGenerating = false;
    }

    public function getLessonsProperty()
    {
        return GrammarLesson::orderBy('order_index', 'asc')->get();
    }

    public function render()
    {
        $lessons = $this->lessons;
        $unlockedStatus = [];
        $previousCompleted = true; // The very first lesson is always unlocked
        $nextSlotAvailable = false;

        foreach ($lessons as $lesson) {
            $unlockedStatus[$lesson->id] = $previousCompleted;
            $previousCompleted = $lesson->is_completed;
        }

        // If the last lesson is completed (or there are no lessons), we can generate a new one
        if (count($lessons) === 0 || $lessons->last()->is_completed) {
            $nextSlotAvailable = true;
        }

        return view('livewire.grammar.index', [
            'lessons' => $lessons,
            'unlockedStatus' => $unlockedStatus,
            'nextSlotAvailable' => $nextSlotAvailable
        ]);
    }
}
