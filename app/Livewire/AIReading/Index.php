<?php

namespace App\Livewire\AIReading;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Services\AIReadingService;
use App\Models\ReadingSession;
use App\Models\WritingSession;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
class Index extends Component
{
    // States: 'idle', 'loading', 'reading'
    public string $state = 'idle';

    // Today's topic and resolved CEFR level
    public string $topic = '';
    public string $cefrLevel = '';

    // Generated article data
    public ?array $article = null;
    public ?int $sessionId = null;
    public ?string $errorMessage = null;

    /**
     * 14 topics — deliberately different subjects and a +6 day offset
     * from Writing Coach's 12-topic list so they never coincide on the same day.
     */
    private const TOPICS = [
        'The impact of social media on modern communication',
        'How cities around the world are adapting to climate change',
        'The history and global culture of coffee',
        'Artificial intelligence in everyday life',
        'The surprising benefits of learning a second language',
        'Renewable energy and the future of electricity',
        'How sleep affects learning, memory, and performance',
        'The rise of remote work and what it means for cities',
        'Traditional foods and their cultural significance',
        'Space exploration: where humanity is headed next',
        'Mental health awareness in the modern world',
        'The role of music across human cultures and history',
        'How technology is transforming education',
        'Why biodiversity matters and how we can protect it',
    ];

    public function mount()
    {
        // Topic: rotating list with +6 offset to avoid Writing Coach collision
        $this->topic = self::TOPICS[(date('z') + 6) % count(self::TOPICS)];

        // CEFR level: Option A — user profile level, fallback to latest writing session
        $user = Auth::user();
        $profileLevel = strtoupper(trim($user->level ?? ''));

        if ($profileLevel) {
            $this->cefrLevel = $profileLevel;
        } else {
            // Fallback: most recent writing session estimate
            $latest = WritingSession::where('user_id', $user->id)
                ->latest()
                ->value('cefr_estimate');
            $this->cefrLevel = $latest ? strtoupper($latest) : 'B1';
        }
    }

    public function generate()
    {
        if ($this->state === 'loading') return;

        $this->state       = 'loading';
        $this->errorMessage = null;

        $result = AIReadingService::generate($this->topic, $this->cefrLevel);

        if (!$result) {
            $this->state        = 'idle';
            $this->errorMessage = 'The article could not be generated. Please check your connection and try again.';
            return;
        }

        // Persist to DB
        $session = ReadingSession::create([
            'user_id'             => Auth::id(),
            'topic'               => $this->topic,
            'cefr_level'          => $this->cefrLevel,
            'estimated_read_time' => $result['estimated_read_time'],
            'article_text'        => $result['article'],
            'article_title'       => $result['title'],
            'article_word_count'  => $result['word_count'],
        ]);

        $this->article   = $result;
        $this->sessionId = $session->id;
        $this->state     = 'reading';
    }

    public function startNew()
    {
        $this->reset(['article', 'sessionId', 'errorMessage']);
        $this->state = 'idle';
    }

    public function render()
    {
        return view('livewire.ai-reading.index');
    }
}
