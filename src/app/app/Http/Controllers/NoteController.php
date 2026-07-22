<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class NoteController extends Controller
{
    public function index()
    {
        $notes = Note::where("user_id", Auth::id())
            ->when(request("search"), function($query, $search) {
                $query->where("title", "like", "%{$search}%")
                      ->orWhere("content", "like", "%{$search}%");
            })
            ->latest()
            ->get()
            ->map(function($note) {
                $s = request("search");
                if ($s) {
                    $note->title = str_ireplace($s, "<mark>".$s."</mark>", $note->title);
                    $note->content = preg_replace("/(<[^>]+>)|(".preg_quote($s, "/").")/iu", "$1<mark>$2</mark>", $note->content);
                }
                return $note;
            });

        $categories = \App\Models\Category::all();
        $count = $notes->count();

        $weather = null;
        try {
            $response = Http::get("https://api.openweathermap.org/data/2.5/weather", [
                "q"     => config("weather.default_city"),
                "appid" => config("weather.api_key"),
                "units" => config("weather.units"),
                "lang"  => config("weather.lang"),
            ]);
            
            if ($response->successful()) {
                $w = $response->json();
                $isRaining = isset($w["weather"][0]["main"]) && Str::contains(strtolower($w["weather"][0]["main"]), ["rain", "дождь", "ливень", "гроза"]);
                $weather = [
                    "temp" => round($w["main"]["temp"]),
                    "city" => $w["name"],
                    "desc" => $w["weather"][0]["description"],
                    "icon" => $w["weather"][0]["icon"],
                    "isRaining" => $isRaining
                ];
            }
        } catch (\Exception $e) {}

        return view("notes.index", compact("notes", "categories", "count", "weather"));
    }

    public function store(Request $request)
    {
        $request->validate(["title" => "required|max:255"]);
        $note = Note::create(array_merge($request->only("title", "content", "category_id"), ["user_id" => Auth::id()]));
        event(new \App\Events\NoteCreated($note));
        $request->user()->increment("xp", 10);
        return redirect()->back()->with("success", "Заметка добавлена!");
    }

    public function edit($id)
    {
        $note = Note::where("id", $id)->where("user_id", Auth::id())->firstOrFail();
        $categories = \App\Models\Category::all();
        return view("notes.edit", compact("note", "categories"));
    }

    public function update(Request $request, $id)
    {
        $note = Note::where("id", $id)->where("user_id", Auth::id())->firstOrFail();
        $request->validate(["title" => "required|max:255"]);
        $note->update($request->only("title", "content", "category_id"));
        return redirect()->back()->with("success", "Заметка обновлена!");
    }

    public function destroy($id)
    {
        $note = Note::where("id", $id)->where("user_id", Auth::id())->firstOrFail();
        $note->delete();
        return redirect()->back()->with("success", "Заметка удалена!");
    }

    public function kanban()
    {
        $allNotes = Note::where('user_id', Auth::id())->get();
        $notes = collect(['new' => collect(), 'in_progress' => collect(), 'done' => collect()])->merge($allNotes->groupBy('status'));
        $categories = \App\Models\Category::all();
        return view('notes.kanban', compact('notes', 'categories'));
    }

    public function updateOrder(Request $request)
    {
        $note = Note::where('id', $request->item_id)->where('user_id', Auth::id())->firstOrFail();
        $note->status = $request->new_status;
        $note->order = $request->new_order;
        $note->save();
        return response()->json(['success' => true]);
    }
}