<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

class NoteController extends Controller
{
    public function index(Request $request)
    {
        // Берем заметки только текущего пользователя
        $notes = Note::with('category')->where('user_id', Auth::id());

        // --- НОВОЕ: Логика поиска по дате ---
        if ($request->filled('date_from')) {
            $notes->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $notes->whereDate('created_at', '<=', $request->date_to);
        }
        // --------------------------------------

        $notes = $notes->orderBy('created_at', 'desc')->get();
        $categories = Category::all();
        $count = $notes->count();

        $weather = null;
        $apiKey = env('WEATHER_API_KEY'); 
        
        if ($apiKey) {
            $response = Http::get("https://api.openweathermap.org/data/2.5/weather", [
                'q' => 'Ufa', 'appid' => $apiKey, 'units' => 'metric', 'lang' => 'ru'
            ]);
            if ($response->successful()) {
                $w = $response->json();
                $weather = ['city' => $w['name'], 'temp' => $w['main']['temp'], 'desc' => $w['weather'][0]['description'], 'icon' => $w['weather'][0]['icon']];
            }
        }
        
        // Передаем введенные даты обратно в шаблон, чтобы они не пропадали из полей
        return view('notes.index', compact('notes', 'count', 'categories', 'weather', 'request'));
    }

    public function store(Request $request)
    {
        $request->validate(['title' => 'required|max:255']);
        Note::create(array_merge($request->only('title', 'content', 'category_id'), ['user_id' => Auth::id()]));
        return redirect()->back()->with('success', 'Заметка добавлена!');
    }

    public function update(Request $request, $id)
    {
        $note = Note::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        $request->validate(['title' => 'required|max:255']);
        $note->update($request->only('title', 'content', 'category_id'));
        return redirect()->back()->with('success', 'Заметка обновлена!');
    }

    public function destroy($id)
    {
        Note::where('id', $id)->where('user_id', Auth::id())->delete();
        return redirect()->back()->with('success', 'Заметка удалена!');
    }
}
