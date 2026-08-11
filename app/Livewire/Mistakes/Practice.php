<?php

namespace App\Livewire\Mistakes;

use Livewire\Component;
use App\Models\Mistake;
use Illuminate\Support\Facades\Auth;

class Practice extends Component
{
    public $category = 'Grammar';
    public $questions = [];
    public $currentIndex = 0;
    public $userAnswers = [];
    public $selectedAnswer = '';
    public $isSubmitted = false;
    public $isCorrect = false;
    public $score = 0;
    public $isCompleted = false;
    public $earnedXp = 40;

    private const QUESTION_BANK = [
        'Grammar' => [
            [
                'question' => "She _____ to the library every Saturday morning.",
                'options' => ['go', 'goes', 'going', 'gone'],
                'answer' => 'goes',
                'explanation' => "Third-person singular subjects (she) require 'goes' in the present simple tense."
            ],
            [
                'question' => "If I _____ more time yesterday, I would have finished the project.",
                'options' => ['have', 'had', 'had had', 'would have'],
                'answer' => 'had had',
                'explanation' => "Third conditional clauses require the past perfect ('had had') in the if-clause."
            ],
            [
                'question' => "They have been studying English _____ three years.",
                'options' => ['since', 'for', 'during', 'from'],
                'answer' => 'for',
                'explanation' => "'For' is used with durations of time ('three years'), while 'since' is used for a starting point."
            ],
            [
                'question' => "Neither the teacher nor the students _____ present at the workshop.",
                'options' => ['was', 'were', 'is', 'be'],
                'answer' => 'were',
                'explanation' => "With 'neither... nor', the verb agrees with the subject closest to it ('the students' -> 'were')."
            ],
            [
                'question' => "You should avoid _____ the same mistakes repeatedly.",
                'options' => ['make', 'to make', 'making', 'made'],
                'answer' => 'making',
                'explanation' => "The verb 'avoid' is followed by a gerund ('making')."
            ],
        ],
        'Vocabulary' => [
            [
                'question' => "Her speech had a profound _____ on everyone in the audience.",
                'options' => ['affect', 'effect', 'effective', 'affection'],
                'answer' => 'effect',
                'explanation' => "'Effect' is a noun meaning a result or influence, whereas 'affect' is usually a verb."
            ],
            [
                'question' => "He is extremely _____; he always looks on the bright side of things.",
                'options' => ['pessimistic', 'optimistic', 'cautious', 'indifferent'],
                'answer' => 'optimistic',
                'explanation' => "'Optimistic' describes someone who is hopeful and sees the bright side of situations."
            ],
            [
                'question' => "The project was delayed due to _____ circumstances beyond our control.",
                'options' => ['unforeseen', 'intentional', 'predictable', 'familiar'],
                'answer' => 'unforeseen',
                'explanation' => "'Unforeseen' means unexpected or not anticipated in advance."
            ],
            [
                'question' => "Please _____ your email address before submitting the registration form.",
                'options' => ['verify', 'falsify', 'neglect', 'distort'],
                'answer' => 'verify',
                'explanation' => "'Verify' means to check or confirm that something is accurate."
            ],
            [
                'question' => "She showed great _____ when dealing with the challenging client.",
                'options' => ['patience', 'patient', 'patiently', 'impatience'],
                'answer' => 'patience',
                'explanation' => "'Patience' is the noun form needed after the adjective 'great'."
            ],
        ],
        'Writing' => [
            [
                'question' => "Select the option with the most natural phrasing for formal correspondence:",
                'options' => [
                    'I am writing to inquire about...',
                    'I wanna ask you about...',
                    'Just hitting you up to know...',
                    'Tell me what is up with...'
                ],
                'answer' => 'I am writing to inquire about...',
                'explanation' => "'I am writing to inquire about...' is standard, professional register in English."
            ],
            [
                'question' => "Choose the sentence with correct word order:",
                'options' => [
                    'Rarely I have seen such dedication.',
                    'Rarely have I seen such dedication.',
                    'Rarely I saw such dedication.',
                    'Have I rarely seen such dedication.'
                ],
                'answer' => 'Rarely have I seen such dedication.',
                'explanation' => "Negative adverbs at the beginning of a sentence ('Rarely') trigger subject-auxiliary inversion."
            ],
            [
                'question' => "Which connector best completes the sentence: 'The candidate was qualified; _____, he lacked practical experience.'",
                'options' => ['however', 'therefore', 'furthermore', 'because'],
                'answer' => 'however',
                'explanation' => "'However' is used to introduce a contrasting statement."
            ],
            [
                'question' => "Choose the sentence that avoids a run-on structure:",
                'options' => [
                    'I love writing it helps me clear my mind.',
                    'I love writing, it helps me clear my mind.',
                    'I love writing; it helps me clear my mind.',
                    'I love writing so it helps me clear my mind.'
                ],
                'answer' => 'I love writing; it helps me clear my mind.',
                'explanation' => "A semicolon correctly joins two independent clauses without creating a comma splice."
            ],
            [
                'question' => "Which option is concise and eliminates unnecessary wordiness?",
                'options' => [
                    'Due to the fact that it rained, we cancelled.',
                    'Because it rained, we cancelled.',
                    'On account of the rainfall event, we cancelled.',
                    'For the reason of rain happening, we cancelled.'
                ],
                'answer' => 'Because it rained, we cancelled.',
                'explanation' => "'Because' is direct and eliminates wordy filler phrases like 'due to the fact that'."
            ],
        ]
    ];

    public function mount()
    {
        // Determine top weakness from mistakes table
        $topWeakness = Mistake::selectRaw('category, count(*) as total')
            ->groupBy('category')
            ->orderBy('total', 'desc')
            ->first();

        if ($topWeakness && isset(self::QUESTION_BANK[$topWeakness->category])) {
            $this->category = $topWeakness->category;
        }

        $this->loadQuestions();
    }

    public function loadQuestions()
    {
        $this->questions = self::QUESTION_BANK[$this->category] ?? self::QUESTION_BANK['Grammar'];
        $this->currentIndex = 0;
        $this->selectedAnswer = '';
        $this->isSubmitted = false;
        $this->score = 0;
        $this->isCompleted = false;
    }

    public function setCategory($cat)
    {
        if (isset(self::QUESTION_BANK[$cat])) {
            $this->category = $cat;
            $this->loadQuestions();
        }
    }

    public function selectAnswer($ans)
    {
        if (!$this->isSubmitted) {
            $this->selectedAnswer = $ans;
        }
    }

    public function submitAnswer()
    {
        if (empty($this->selectedAnswer) || $this->isSubmitted) return;

        $currentQ = $this->questions[$this->currentIndex];
        $this->isSubmitted = true;
        $this->isCorrect = ($this->selectedAnswer === $currentQ['answer']);

        if ($this->isCorrect) {
            $this->score++;
        }
    }

    public function nextQuestion()
    {
        if ($this->currentIndex + 1 < count($this->questions)) {
            $this->currentIndex++;
            $this->selectedAnswer = '';
            $this->isSubmitted = false;
            $this->isCorrect = false;
        } else {
            $this->isCompleted = true;
            Auth::user()?->addXp($this->earnedXp);
            \App\Services\RecommendationEngineService::logAndComplete(Auth::id() ?? 1, 'weakness_practice', $this->category, 300, $this->score);
        }
    }

    public function restart()
    {
        $this->loadQuestions();
    }

    public function render()
    {
        return view('livewire.mistakes.practice');
    }
}
