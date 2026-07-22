<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use App\Models\Note;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'xp',
        'streak',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    public function notes()
    {
        return $this->hasMany(Note::class);
    }

    public function level() {
        return floor(sqrt($this->xp / 50)) + 1;
    }

    public function xpPercent() {
        $lvl = $this->level();
        $currXp = ($lvl - 1) * ($lvl - 1) * 50;
        $nextXp = $lvl * $lvl * 50;
        return round((($this->xp - $currXp) / ($nextXp - $currXp)) * 100);
    }
}
