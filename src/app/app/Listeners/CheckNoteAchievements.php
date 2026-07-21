<?php

namespace App\Listeners;

use App\Events\NoteCreated;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class CheckNoteAchievements
{
    public function handle(NoteCreated $event): void
    {
        $user = $event->note->user;
        $note = $event->note;
        
        $user->xp += 10;
        
        $today = now()->format('Y-m-d');
        $yesterday = now()->subDay()->format('Y-m-d');
        $lastNote = $user->notes()->latest('created_at')->skip(1)->first();
        
        if ($lastNote && $lastNote->created_at->format('Y-m-d') !== $today) {
            if ($lastNote->created_at->format('Y-m-d') === $yesterday) {
                $user->streak += 1;
            } else {
                $user->streak = 1;
            }
        } elseif (!$lastNote) {
            $user->streak = 1;
        }
        
        $user->save();

        $this->checkPoet($user, $note);
        $this->checkThunderstorm($user);
        $this->checkStreaks($user);
    }

    private function unlock($user, $type, $title, $desc)
    {
        if (!$user->achievements()->where('type', $type)->exists()) {
            $user->achievements()->create([
                'type' => $type,
                'title' => $title,
                'description' => $desc,
                'unlocked_at' => now()
            ]);
            
            session()->flash('achievement', $title);
        }
    }

    private function checkPoet($user, $note)
    {
        if (str_word_count(strip_tags($note->content)) >= 1000) {
            $this->unlock($user, 'poet', 'Poet', 'Wrote over 1000 words');
        }
    }

    private function checkThunderstorm($user)
    {
        try {
            $w = Http::get("https://api.openweathermap.org/data/2.5/weather", [
                "q" => "Ufa", 
                "appid" => env("WEATHER_API_KEY")
            ])->json();
            
            if (isset($w['weather'][0]['main'])) {
                $isThunder = Str::contains(strtolower($w['weather'][0]['main']), 'thunderstorm');
                if ($isThunder) {
                    $this->unlock($user, 'thunder', 'Thunder', 'Created note during a storm');
                }
            }
        } catch (\Exception $e) {}
    }

    private function checkStreaks($user)
    {
        if ($user->streak == 7) {
            $this->unlock($user, 'streak_7', '7 Days', '7 days streak');
        }
        if ($user->streak == 30) {
            $this->unlock($user, 'streak_30', '30 Days', '30 days streak');
        }
    }
}
