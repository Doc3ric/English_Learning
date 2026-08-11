<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\DailyReflection;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

/**
 * Daily Reflection — structured 2-minute end-of-day self-assessment.
 *
 * Embedded as a sub-component on the Dashboard after 6 PM.
 * One reflection per user per day; updates are allowed (overwrite same-day record).
 */
class DailyReflectionComponent extends Component
{
    public bool $didGrammar    = false;
    public bool $didVocabulary = false;
    public bool $didSpeaking   = false;
    public bool $didWriting    = false;

    public string $whatWasDifficult = '';
    public string $newExpression    = '';

    public bool $saved = false;

    /** Populated on mount if a reflection already exists today */
    public ?int $existingId = null;

    public function mount(): void
    {
        $userId  = Auth::id() ?? 1;
        $today   = Carbon::today()->toDateString();
        $existing = DailyReflection::where('user_id', $userId)->whereDate('date', $today)->first();

        if ($existing) {
            $this->existingId       = $existing->id;
            $this->didGrammar       = $existing->did_grammar;
            $this->didVocabulary    = $existing->did_vocabulary;
            $this->didSpeaking      = $existing->did_speaking;
            $this->didWriting       = $existing->did_writing;
            $this->whatWasDifficult = $existing->what_was_difficult ?? '';
            $this->newExpression    = $existing->new_expression ?? '';
        }
    }

    public function save(): void
    {
        $this->validate([
            'whatWasDifficult' => 'nullable|string|max:500',
            'newExpression'    => 'nullable|string|max:300',
        ], [
            'whatWasDifficult.max' => 'Please keep this to 500 characters or less.',
            'newExpression.max'    => 'Please keep this to 300 characters or less.',
        ]);

        $userId = Auth::id() ?? 1;
        $today  = Carbon::today()->toDateString();

        $data = [
            'user_id'            => $userId,
            'date'               => $today,
            'did_grammar'        => $this->didGrammar,
            'did_vocabulary'     => $this->didVocabulary,
            'did_speaking'       => $this->didSpeaking,
            'did_writing'        => $this->didWriting,
            'what_was_difficult' => trim($this->whatWasDifficult) ?: null,
            'new_expression'     => trim($this->newExpression) ?: null,
        ];

        // updateOrCreate: prevents duplicate records for the same day
        DailyReflection::updateOrCreate(
            ['user_id' => $userId, 'date' => $today],
            $data
        );

        $this->saved = true;
    }

    public function edit(): void
    {
        $this->saved = false;
    }

    public function render()
    {
        return view('livewire.daily-reflection');
    }
}
