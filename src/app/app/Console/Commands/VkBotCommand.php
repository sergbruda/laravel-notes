<?php

namespace App\Console\Commands;

use App\Models\Note;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Console\Command;

class VkBotCommand extends Command
{
    protected $signature = 'vk:run';
    protected $description = 'ВК Бот';

    public function handle()
    {
        $token = env('VK_TOKEN');
        $groupId = env('VK_GROUP_ID');
        Auth::login(User::find(1));

        $this->info("ВК Бот запущен. Подключаемся...");

        $lp = json_decode($this->vkReq("https://api.vk.com/method/messages.getLongPollServer?group_id={$groupId}&access_token={$token}&v=5.131"));

        if (!isset($lp->response)) {
            $this->error("Ошибка подключения.");
            return 1;
        }

        $key = $lp->response->key;
        $server = $lp->response->server;
        $ts = $lp->response->ts;

        $this->info("Успешно! Ожидаю сообщений...");

        while (true) {
            $response = $this->vkReq("{$server}?act=a_check&key={$key}&ts={$ts}&wait=25&mode=2&v=5.131");
            $data = json_decode($response);

            if (isset($data->ts)) {
                $ts = $data->ts;
            }

            if (isset($data->updates)) {
                foreach ($data->updates as $update) {
                    if ($update->type === "message_new") {
                        $text = trim($update->object->message->text);
                        $peerId = $update->object->message->peer_id;
                        $reply = $this->processCommand($text);
                        $this->vkSendMessage($peerId, $reply, $token);
                    }
                }
            }
            sleep(1);
        }
    }

    private function processCommand($text)
    {
        if ($text === '/start' || $text === 'Начать') {
            return "Привет! Я бот твоего блокнота. Пиши /list.";
        }

        if ($text === '/list') {
            $notes = Note::where('user_id', 1)->orderBy('created_at', 'desc')->take(5)->get();
            if ($notes->isEmpty()) return "Заметок нет.";
            $response = "";
            foreach ($notes as $note) {
                $response .= $note->title . "\n";
            }
            return $response;
        }

        return "Нет такой команды.";
    }

    private function vkReq($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    private function vkSendMessage($peerId, $text, $token)
    {
        $url = "https://api.vk.com/method/messages.send";
        $params = [
            'peer_id' => $peerId,
            'message' => $text,
            'random_id' => rand(1, 2147483647),
            'access_token' => $token,
            'v' => '5.131'
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_exec($ch);
        curl_close($ch);
    }
}
