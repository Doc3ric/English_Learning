<?php

namespace App\Livewire\Mistakes;

use Livewire\Component;
use Livewire\Attributes\Url;
use App\Models\Mistake;
use Illuminate\Support\Facades\Auth;

class Practice extends Component
{
    #[Url(as: 'category')]
    public string $category = 'Grammar';

    public array $questions = [];
    public int $currentIndex = 0;
    public array $userAnswers = [];
    public string $selectedAnswer = '';
    public bool $isSubmitted = false;
    public bool $isCorrect = false;
    public int $score = 0;
    public bool $isCompleted = false;
    public int $earnedXp = 40;

    private const QUESTION_BANK = [
        'Preposition Usage' => [
            [
                'question' => "Right now I want to focus _____ my interview preparation.",
                'options' => ['on', 'about', 'in', 'to'],
                'answer' => 'on',
                'explanation' => "The verb 'focus' takes the preposition 'on' (e.g., 'focus on something')."
            ],
            [
                'question' => "She works as an AI engineer specializing _____ modern technology.",
                'options' => ['in', 'for', 'at', 'on'],
                'answer' => 'in',
                'explanation' => "The verb 'specialize' is followed by the preposition 'in'."
            ],
            [
                'question' => "This UI framework is especially good _____ web applications.",
                'options' => ['for', 'in', 'about', 'to'],
                'answer' => 'for',
                'explanation' => "Use 'good for' when talking about usefulness or suitability for a purpose."
            ],
            [
                'question' => "She was looking _____ a new job when the opportunity arrived.",
                'options' => ['for', 'at', 'about', 'on'],
                'answer' => 'for',
                'explanation' => "The phrasal verb 'look for' means to search for something."
            ],
            [
                'question' => "They have been studying English _____ three years.",
                'options' => ['for', 'since', 'during', 'from'],
                'answer' => 'for',
                'explanation' => "'For' is used with durations of time ('three years'), while 'since' is used for a starting point."
            ],
        ],
        'Word Order' => [
            [
                'question' => "The programming language I am most confident _____ is JavaScript.",
                'options' => ['in', 'for', 'at', 'on'],
                'answer' => 'in',
                'explanation' => "We say 'confident in something' (e.g. 'confident in JavaScript')."
            ],
            [
                'question' => "Rarely _____ such a well-structured codebase.",
                'options' => ['have I seen', 'I have seen', 'saw I', 'I saw'],
                'answer' => 'have I seen',
                'explanation' => "Starting a sentence with a negative adverb ('Rarely') requires auxiliary inversion ('have I seen')."
            ],
            [
                'question' => "Can you tell me where _____?",
                'options' => ['the office is', 'is the office', 'the office be', 'does the office be'],
                'answer' => 'the office is',
                'explanation' => "In indirect questions, use standard subject + verb word order ('where the office is')."
            ],
            [
                'question' => "Only after finishing the course _____ to apply for jobs.",
                'options' => ['did he decide', 'he decided', 'he decides', 'does he decided'],
                'answer' => 'did he decide',
                'explanation' => "Limiting adverbial phrases ('Only after...') at the beginning trigger auxiliary inversion."
            ],
            [
                'question' => "He bought a _____ car.",
                'options' => ['beautiful red Italian', 'red beautiful Italian', 'Italian beautiful red', 'beautiful Italian red'],
                'answer' => 'beautiful red Italian',
                'explanation' => "Adjective order follows Opinion ('beautiful') -> Color ('red') -> Origin ('Italian')."
            ],
        ],
        'Verb Usage' => [
            [
                'question' => "I _____ many technical challenges over the past few months.",
                'options' => ['have faced', 'faced', 'am facing', 'was faced'],
                'answer' => 'have faced',
                'explanation' => "'Over the past few months' indicates an action starting in the past and continuing to present, requiring present perfect ('have faced')."
            ],
            [
                'question' => "Can you help me _____ my English grammar skills?",
                'options' => ['boost', 'to boosting', 'boosted', 'in boosting'],
                'answer' => 'boost',
                'explanation' => "The verb 'help' is followed by a bare infinitive ('help me boost')."
            ],
            [
                'question' => "By the time the meeting starts, we _____ the presentation.",
                'options' => ['will have completed', 'completed', 'completing', 'have completed'],
                'answer' => 'will have completed',
                'explanation' => "'By the time...' referencing a future point requires the future perfect tense ('will have completed')."
            ],
            [
                'question' => "She suggested _____ a short rest before continuing.",
                'options' => ['taking', 'to take', 'took', 'taken'],
                'answer' => 'taking',
                'explanation' => "The verb 'suggest' is followed by a gerund ('suggest taking')."
            ],
            [
                'question' => "I look forward to _____ with your engineering team.",
                'options' => ['working', 'work', 'worked', 'be work'],
                'answer' => 'working',
                'explanation' => "In 'look forward to', 'to' is a preposition, so it must be followed by a gerund ('working')."
            ],
        ],
        'Sentence Structure' => [
            [
                'question' => "I have two initial _____ scheduled for next week.",
                'options' => ['interviews', 'interview', 'interviewing', 'interviewer'],
                'answer' => 'interviews',
                'explanation' => "Plural modifier 'two' requires the plural noun 'interviews'."
            ],
            [
                'question' => "They want to know how good my programming skills _____.",
                'options' => ['are', 'is', 'be', 'being'],
                'answer' => 'are',
                'explanation' => "Plural subject 'skills' requires the plural verb 'are'."
            ],
            [
                'question' => "Because the traffic was heavy, _____ late for the presentation.",
                'options' => ['we arrived', 'so we arrived', 'and we arrived', 'that we arrived'],
                'answer' => 'we arrived',
                'explanation' => "Do not pair 'because' with 'so' or 'and' in the main clause; 'because' already establishes the cause."
            ],
            [
                'question' => "Neither the manager nor the developers _____ available right now.",
                'options' => ['were', 'was', 'is', 'be'],
                'answer' => 'were',
                'explanation' => "With 'neither... nor', the verb agrees with the subject closest to it ('the developers' -> 'were')."
            ],
            [
                'question' => "Not only is he experienced, _____ very articulate.",
                'options' => ['but he is also', 'and he is', 'so he is', 'also he is'],
                'answer' => 'but he is also',
                'explanation' => "Correlative conjunction 'not only' connects with 'but... also'."
            ],
        ],
        'Word Choice' => [
            [
                'question' => "I am applying for a _____ as a web developer.",
                'options' => ['position', 'work', 'jobbing', 'career pathing'],
                'answer' => 'position',
                'explanation' => "Use 'position' or 'role' when referring to a specific job opening in formal English."
            ],
            [
                'question' => "The interview with the company _____ went very smoothly.",
                'options' => ['interviewer', 'company interviewer', 'interviews', 'interviewing'],
                'answer' => 'interviewer',
                'explanation' => "'Interviewer' is the person conducting the interview."
            ],
            [
                'question' => "Her speech had a profound _____ on everyone in the audience.",
                'options' => ['effect', 'affect', 'effective', 'affection'],
                'answer' => 'effect',
                'explanation' => "'Effect' is a noun meaning result or impact, whereas 'affect' is usually a verb."
            ],
            [
                'question' => "Please _____ your contact details before submitting the form.",
                'options' => ['verify', 'falsify', 'neglect', 'distort'],
                'answer' => 'verify',
                'explanation' => "'Verify' means to check or confirm accuracy."
            ],
            [
                'question' => "He is extremely _____; he always sees the positive side of things.",
                'options' => ['optimistic', 'pessimistic', 'cautious', 'indifferent'],
                'answer' => 'optimistic',
                'explanation' => "'Optimistic' describes someone who sees the positive side of situations."
            ],
        ],
        'Grammar' => [
            [
                'question' => "She _____ to the library every Saturday morning.",
                'options' => ['goes', 'go', 'going', 'gone'],
                'answer' => 'goes',
                'explanation' => "Third-person singular subjects ('she') require 'goes' in present simple."
            ],
            [
                'question' => "If I _____ more time yesterday, I would have finished the project.",
                'options' => ['had had', 'have', 'had', 'would have'],
                'answer' => 'had had',
                'explanation' => "Third conditional clauses require the past perfect ('had had') in the if-clause."
            ],
            [
                'question' => "They have been studying English _____ three years.",
                'options' => ['for', 'since', 'during', 'from'],
                'answer' => 'for',
                'explanation' => "'For' is used with durations of time ('three years')."
            ],
            [
                'question' => "Neither the teacher nor the students _____ present at the workshop.",
                'options' => ['were', 'was', 'is', 'be'],
                'answer' => 'were',
                'explanation' => "With 'neither... nor', the verb agrees with the subject closest to it ('the students' -> 'were')."
            ],
            [
                'question' => "You should avoid _____ the same mistakes repeatedly.",
                'options' => ['making', 'make', 'to make', 'made'],
                'answer' => 'making',
                'explanation' => "The verb 'avoid' is followed by a gerund ('making')."
            ],
        ],
        'Vocabulary' => [
            [
                'question' => "Her speech had a profound _____ on everyone in the audience.",
                'options' => ['effect', 'affect', 'effective', 'affection'],
                'answer' => 'effect',
                'explanation' => "'Effect' is a noun meaning a result or influence."
            ],
            [
                'question' => "He is extremely _____; he always looks on the bright side of things.",
                'options' => ['optimistic', 'pessimistic', 'cautious', 'indifferent'],
                'answer' => 'optimistic',
                'explanation' => "'Optimistic' describes someone who sees the bright side."
            ],
            [
                'question' => "The project was delayed due to _____ circumstances beyond our control.",
                'options' => ['unforeseen', 'intentional', 'predictable', 'familiar'],
                'answer' => 'unforeseen',
                'explanation' => "'Unforeseen' means unexpected or not anticipated."
            ],
            [
                'question' => "Please _____ your email address before submitting.",
                'options' => ['verify', 'falsify', 'neglect', 'distort'],
                'answer' => 'verify',
                'explanation' => "'Verify' means to confirm accuracy."
            ],
            [
                'question' => "She showed great _____ when dealing with the challenging client.",
                'options' => ['patience', 'patient', 'patiently', 'impatience'],
                'answer' => 'patience',
                'explanation' => "'Patience' is the noun form needed."
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
                    'Rarely have I seen such dedication.',
                    'Rarely I have seen such dedication.',
                    'Rarely I saw such dedication.',
                    'Have I rarely seen such dedication.'
                ],
                'answer' => 'Rarely have I seen such dedication.',
                'explanation' => "Negative adverbs at the beginning of a sentence ('Rarely') trigger subject-auxiliary inversion."
            ],
            [
                'question' => "Which connector best completes: 'The candidate was qualified; _____, he lacked experience.'",
                'options' => ['however', 'therefore', 'furthermore', 'because'],
                'answer' => 'however',
                'explanation' => "'However' introduces a contrasting statement."
            ],
            [
                'question' => "Choose the sentence that avoids a run-on structure:",
                'options' => [
                    'I love writing; it helps me clear my mind.',
                    'I love writing it helps me clear my mind.',
                    'I love writing, it helps me clear my mind.',
                    'I love writing so it helps me clear my mind.'
                ],
                'answer' => 'I love writing; it helps me clear my mind.',
                'explanation' => "A semicolon correctly joins two independent clauses."
            ],
            [
                'question' => "Which option is concise and eliminates wordiness?",
                'options' => [
                    'Because it rained, we cancelled.',
                    'Due to the fact that it rained, we cancelled.',
                    'On account of the rainfall event, we cancelled.',
                    'For the reason of rain happening, we cancelled.'
                ],
                'answer' => 'Because it rained, we cancelled.',
                'explanation' => "'Because' is direct and eliminates wordy filler phrases."
            ],
        ]
    ];

    public function mount()
    {
        // 1. If category requested in URL query string (e.g. ?category=Preposition%20Usage)
        $urlCategory = request()->query('category');
        if ($urlCategory && trim($urlCategory) !== '') {
            $this->category = trim($urlCategory);
        } else {
            // 2. Otherwise determine top weakness from mistakes table
            $topWeakness = Mistake::selectRaw('category, count(*) as total')
                ->groupBy('category')
                ->orderBy('total', 'desc')
                ->first();

            if ($topWeakness && !empty($topWeakness->category)) {
                $this->category = $topWeakness->category;
            }
        }

        $this->loadQuestions();
    }

    public function loadQuestions()
    {
        // Exact match or fallback mapping
        $cat = $this->category;
        
        if (isset(self::QUESTION_BANK[$cat])) {
            $this->questions = self::QUESTION_BANK[$cat];
        } else {
            // Smart alias mapping for categories logged by AI or mistakes
            $catLower = strtolower($cat);
            if (str_contains($catLower, 'preposition')) {
                $this->questions = self::QUESTION_BANK['Preposition Usage'];
            } elseif (str_contains($catLower, 'order')) {
                $this->questions = self::QUESTION_BANK['Word Order'];
            } elseif (str_contains($catLower, 'verb') || str_contains($catLower, 'tense')) {
                $this->questions = self::QUESTION_BANK['Verb Usage'];
            } elseif (str_contains($catLower, 'structure') || str_contains($catLower, 'clarity')) {
                $this->questions = self::QUESTION_BANK['Sentence Structure'];
            } elseif (str_contains($catLower, 'choice') || str_contains($catLower, 'word')) {
                $this->questions = self::QUESTION_BANK['Word Choice'];
            } elseif (str_contains($catLower, 'vocab')) {
                $this->questions = self::QUESTION_BANK['Vocabulary'];
            } elseif (str_contains($catLower, 'write') || str_contains($catLower, 'style')) {
                $this->questions = self::QUESTION_BANK['Writing'];
            } else {
                $this->questions = self::QUESTION_BANK['Grammar'];
            }
        }

        $this->currentIndex = 0;
        $this->selectedAnswer = '';
        $this->isSubmitted = false;
        $this->score = 0;
        $this->isCompleted = false;
    }

    public function setCategory($cat)
    {
        $this->category = $cat;
        $this->loadQuestions();
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
