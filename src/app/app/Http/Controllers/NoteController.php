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
        $dailyForecast = [];

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

            $forecastResponse = Http::get("https://api.openweathermap.org/data/2.5/forecast", [
                "q"     => config("weather.default_city"),
                "appid" => config("weather.api_key"),
                "units" => config("weather.units"),
                "lang"  => config("weather.lang"),
            ]);

            if ($forecastResponse->successful()) {
                $fData = $forecastResponse->json();
                $grouped = [];
                
                foreach ($fData["list"] as $item) {
                    $date = date("Y-m-d", $item["dt"]);
                    if (!isset($grouped[$date])) {
                        $grouped[$date] = [
                            "date" => $date,
                            "temp" => round($item["main"]["temp"]),
                            "icon" => $item["weather"][0]["icon"],
                            "desc" => $item["weather"][0]["description"],
                        ];
                    }
                }
                $dailyForecast = array_slice(array_values($grouped), 0, 5); 
            }

        } catch (\Exception $e) {}
        $dailyForecast = [];

        $forecastResponse = Http::get("https://api.openweathermap.org/data/2.5/forecast", [
            "q"     => config("weather.default_city"),
            "appid" => config("weather.api_key"),
            "units" => config("weather.units"),
            "lang"  => config("weather.lang"),
        ]);

        if ($forecastResponse->successful()) {
            $fData = $forecastResponse->json();
            $grouped = [];
            
            foreach ($fData["list"] as $item) {
                $date = date("Y-m-d", $item["dt"]);
                if (!isset($grouped[$date])) {
                    $grouped[$date] = [
                        "date" => $date,
                        "temp" => round($item["main"]["temp"]),
                        "icon" => $item["weather"][0]["icon"],
                        "desc" => $item["weather"][0]["description"],
                    ];
                }
            }
            $dailyForecast = array_slice(array_values($grouped), 0, 5); 
        }

        return view("notes.index", compact("notes", "categories", "count", "weather", "dailyForecast"));
    }

    public function store(Request $request)
    {
        $request->validate(["title" => "required|max:255"]);
        $content = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $request->input("content", ""));
        $note = Note::create(array_merge($request->only("title", "category_id"), ["content" => $content, "user_id" => Auth::id()]));
        event(new \App\Events\NoteCreated($note)); $request->user()->increment("xp", 10);
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
        $content = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $request->input("content", ""));
        $note->update($request->only("title", "category_id") + ["content" => $content]);
        return redirect()->back()->with("success", "Заметка обновлена!");
    }

    public function destroy($id)
    {
        $note = Note::where("id", $id)->where("user_id", Auth::id())->firstOrFail();
        $note->delete();
        return redirect()->back()->with("success", "Заметка удалена!");
    }
}