<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Мой Блокнот</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <livewire:styles />
</head>
<body class="bg-gray-50 min-h-screen py-12">
    <div class="container mx-auto px-4 max-w-4xl">
        
        <h1 class="text-4xl font-extrabold text-center text-gray-900 mb-2">📝 Мой Блокнот</h1>
        <p class="text-center text-gray-500 mb-10">Мгновенный поиск на Livewire</p>
        
        <livewire:search-notes />

        @php
            use Illuminate\Support\Facades\Http;
            $city = "Moscow";
            $current = null;
            $dailyForecast = [];

            try {
                $currentResponse = Http::get("https://api.openweathermap.org/data/2.5/weather", ["q" => $city, "appid" => env("WEATHER_API_KEY"), "units" => "metric", "lang" => "ru"]);
                if ($currentResponse->successful()) $current = $currentResponse->json();
            } catch (\Exception $e) {}

            try {
                $forecastResponse = Http::get("https://api.openweathermap.org/data/2.5/forecast", ["q" => $city, "appid" => env("WEATHER_API_KEY"), "units" => "metric", "lang" => "ru"]);
                if ($forecastResponse->successful()) {
                    $grouped = [];
                    foreach ($forecastResponse->json()["list"] as $item) {
                        $day = date("Y-m-d", $item["dt"]);
                        $hour = date("H", $item["dt"]);
                        if (!isset($grouped[$day])) {
                            $grouped[$day] = $item;
                        } elseif (abs($hour - 12) < abs(date("H", $grouped[$day]["dt"]) - 12)) {
                            $grouped[$day] = $item;
                        }
                    }
                    $dailyForecast = array_slice($grouped, 0, 5, true);
                }
            } catch (\Exception $e) {}
        @endphp

        <div class="mt-10 bg-white rounded-2xl shadow-sm p-8 border border-gray-100 flex flex-col md:flex-row items-center justify-between gap-6">
            @if($current)
                <div class="flex items-center gap-6">
                    <img src="https://openweathermap.org/img/wn/{{ $current[weather][0][icon] }}@4x.png" alt="Погода" class="w-28 h-28 -mt-4 drop-shadow-lg">
                    <div>
                        <p class="text-6xl font-black text-gray-900">{{ round($current[main][temp]) }}°C</p>
                        <p class="text-xl text-gray-500 font-medium">{{ ucfirst($current[weather][0][description]) }}</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-3xl mb-1">📍</p>
                    <p class="text-lg font-bold text-gray-800">{{ $current[name] }}</p>
                    <p class="text-sm text-gray-400">Ощущается как {{ round($current[main][feels_like]) }}°C</p>
                </div>
            @else
                <p class="text-gray-400 w-full text-center">Не удалось загрузить погоду (проверьте ключ API или интернет)</p>
            @endif
        </div>

        @if(count($dailyForecast) > 0)
            <div class="mt-6 bg-white rounded-2xl shadow-sm p-6 border border-gray-100">
                <h2 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">📅 Прогноз на 5 дней</h2>
                <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                    @foreach($dailyForecast as $day => $data)
                        <div class="text-center p-3 rounded-xl bg-gray-50 hover:bg-gray-100 transition">
                            <p class="text-xs font-bold text-gray-500 uppercase">{{ date("D", $data["dt"]) == date("D") ? "Сегодня" : strftime("%a", $data["dt"]) }}</p>
                            <img src="https://openweathermap.org/img/wn/{{ $data[weather][0][icon] }}@2x.png" alt="Иконка" class="w-16 h-16 mx-auto my-2">
                            <p class="text-sm text-gray-600">{{ ucfirst($data[weather][0][description]) }}</p>
                            <div class="mt-2 text-sm font-bold">
                                <span class="text-gray-900">{{ round($data[main][temp_max]) }}°</span>
                                <span class="text-gray-400 ml-1">{{ round($data[main][temp_min]) }}°</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="mt-8 text-center">
            <a href="/admin" class="text-amber-600 hover:text-amber-800 font-semibold transition">Перейти в панель управления →</a>
        </div>

    </div>
    <livewire:scripts />
</body>
</html>
