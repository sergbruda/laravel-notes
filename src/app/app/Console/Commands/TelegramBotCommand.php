<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Note;
use Illuminate\Support\Facades\Http;

class TelegramBotCommand extends Command
{
    protected $signature = 'bot:run';
    protected $description = 'Telegram бот для заметок';

    public function handle()
    {
        $token = env('TELEGRAM_BOT_TOKEN');
        $adminId = env('TELEGRAM_ADMIN_ID');
        $proxy = env('TELEGRAM_PROXY');
        $offset = 0;

        if (!$token || $token === 'ВАШ_ТОКЕН') {
            $this->error("Вставьте настоящий TELEGRAM_BOT_TOKEN в .env!");
            return 1;
        }
        if (!$adminId) {
            $this->error("Вставьте ваш TELEGRAM_ADMIN_ID в .env!");
            return 1;
        }

        $this->info("Бот запущен в рабочем контейнере. Ожидаю сообщения...");

        while (true) {
            $url = "https://api.telegram.org/bot{$token}/getUpdates?offset=" . $offset . "&timeout=30";
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 35);
            curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
            
            if ($proxy) {
                curl_setopt($ch, CURLOPT_PROXY, $proxy);
                curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
            }

            $response = json_decode(curl_exec($ch), true);
            curl_close($ch);

            if (isset($response['ok']) && $response['ok']) {
                foreach ($response['result'] as $update) {
                    $offset = $update['update_id'] + 1;
                    $chatId = $update['message']['chat']['id'];
                    $text = trim($update['message']['text'] ?? '');

                    // Безопасность: отвечаем только вам
                    if ($chatId != $adminId) {
                        $this->sendMessage($chatId, "Доступ запрещен.", $proxy);
                        continue;
                    }

                    $this->sendMessage($chatId, $this->processCommand($text), $proxy);
                    sleep(1);
                }
            } else {
                // Если ошибка API (например, нет прокси в РФ), ждем подольше перед повтором
                sleep(10);
            }
        }
    }

    private function processCommand($text)
    {
        switch (true) {
            case $text === '/start':
                return "Привет! Я бот твоего блокнота.\n\nКоманды:\n/list - Показать последние 5 заметок\n/add Заголовок | Текст - Добавить заметку\n/weather - Погода в Уфе";

            case $text === '/list':
                // Берем заметки прямо по ID из .env, без авторизации
                $notes = Note::where('user_id', env('TELEGRAM_ADMIN_ID'))->orderBy('created_at', 'desc')->take(5)->get();
                if ($notes->isEmpty()) return "У тебя пока нет заметок.";
                
                $response = "Последние заметки:\n\n";
                foreach ($notes as $note) {
                    $cat = $note->category ? "[{$note->category->name}] " : "";
                    $response .= "Дата: {$note->created_at->format('d.m H:i')}\n{$cat}{$note->title}\n{$note->content}\n\n";
                }
                return $response;

            case $text === '/weather':
                $apiKey = env('WEATHER_API_KEY');
                if (!$apiKey) return "API ключ для погоды не настроен.";
                $w = Http::get("https://api.openweathermap.org/data/2.5/weather", ['q' => 'Ufa', 'appid' => $apiKey, 'units' => 'metric', 'lang' => 'ru']);
                if ($w->successful()) {
                    $data = $w->json();
                    return "Погода в {$data['name']}: {$data['main']['temp']}C, " . ucfirst($data['weather'][0]['description']);
                }
                return "Ошибка получения погоды.";

            case strpos($text, '/add ') === 0:
                $parts = explode('|', substr($text, 5), 2);
                $title = trim($parts[0]);
                $content = trim($parts[1] ?? '');
                if (empty($title)) return "Ошибка! Заголовок не может быть пустым.";
                Note::create(['title' => $title, 'content' => $content, 'user_id' => env('TELEGRAM_ADMIN_ID')]);
                return "✅ Заметка добавлена: {$title}";

            default:
                return "Я не понимаю эту команду. Напиши /start.";
        }
    }

    private function sendMessage($chatId, $text, $proxy)
    {
        $token = env('TELEGRAM_BOT_TOKEN');
        $url = "https://api.telegram.org/bot{$token}/sendMessage";
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['chat_id' => $chatId, 'text' => $text]));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type:application/json']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        if ($proxy) {
            curl_setopt($ch, CURLOPT_PROXY, $proxy);
            curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
        }
        
        curl_exec($ch);
        curl_close($ch);
    }
}
