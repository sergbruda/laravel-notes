<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Note;
use Illuminate\Support\Facades\Http;

class TelegramWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $adminId = env('TELEGRAM_ADMIN_ID');
        $update = $request->all();
        
        if (!isset($update['message']['text'])) return response('ok');

        $chatId = $update['message']['chat']['id'];
        $text = trim($update['message']['text']);

        if ($chatId != $adminId) {
            return response()->json(['method' => 'sendMessage', 'chat_id' => $chatId, 'text' => "Доступ запрещен."]);
        }

        $responseText = $this->processCommand($text);
        
        // МАГИЯ: Мы не лезем в интернет! Мы просто отдаем JSON, и Telegram сам отправит сообщение!
        return response()->json([
            'method' => 'sendMessage',
            'chat_id' => $chatId,
            'text' => $responseText
        ]);
    }

    private function processCommand($text)
    {
        switch (true) {
            case $text === '/start':
                return "Привет! Я бот твоего блокнота.\n\nКоманды:\n/list - Показать последние 5 заметок\n/add Заголовок | Текст - Добавить заметку\n/weather - Погода в Уфе";

            case $text === '/list':
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
}
