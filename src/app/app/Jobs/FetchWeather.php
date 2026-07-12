<?php
namespace App\Jobs;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class FetchWeather implements ShouldQueue
{
    use Queueable;

    public function handle()
    {
        $r = Http::get('https://wttr.in/Moscow?format=j1&lang=ru');
        if ($r->successful()) {
            Cache::put('weather', json_decode($r->body(), true), now()->addHours(2));
        }
    }
}
