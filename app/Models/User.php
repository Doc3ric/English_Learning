<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'level',
        'reading_cefr_level',
        'xp',
    ];

    public function addXp(int $amount): void
    {
        $this->increment('xp', $amount);
    }

    public function getXpLevelInfoAttribute(): array
    {
        $xp = $this->xp ?? 0;

        if ($xp < 100) {
            $level = 1; $minXp = 0; $maxXp = 100; $cefr = 'A1';
        } elseif ($xp < 250) {
            $level = 2; $minXp = 100; $maxXp = 250; $cefr = 'A1';
        } elseif ($xp < 500) {
            $level = 3; $minXp = 250; $maxXp = 500; $cefr = 'A2';
        } elseif ($xp < 1000) {
            $level = 4; $minXp = 500; $maxXp = 1000; $cefr = 'A2';
        } elseif ($xp < 1750) {
            $level = 5; $minXp = 1000; $maxXp = 1750; $cefr = 'B1';
        } elseif ($xp < 2500) {
            $level = 6; $minXp = 1750; $maxXp = 2500; $cefr = 'B1';
        } elseif ($xp < 3500) {
            $level = 7; $minXp = 2500; $maxXp = 3500; $cefr = 'B2';
        } elseif ($xp < 5000) {
            $level = 8; $minXp = 3500; $maxXp = 5000; $cefr = 'B2';
        } else {
            $extra = floor(($xp - 5000) / 1500);
            $level = 9 + $extra;
            $minXp = 5000 + ($extra * 1500);
            $maxXp = $minXp + 1500;
            $cefr = 'C1';
        }

        $currentProgressXp = $xp - $minXp;
        $requiredXpInLevel = max(1, $maxXp - $minXp);
        $percentage = min(100, max(0, round(($currentProgressXp / $requiredXpInLevel) * 100)));

        return [
            'level' => (int) $level,
            'cefr' => $cefr,
            'total_xp' => $xp,
            'min_xp' => $minXp,
            'max_xp' => $maxXp,
            'current_level_xp' => $currentProgressXp,
            'required_level_xp' => $requiredXpInLevel,
            'percentage' => $percentage,
        ];
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
