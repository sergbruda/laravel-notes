<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Мои заметки и Погода</title>
    <style>
        :root {
            --bg-color: #f4f7f6; --text-color: #333; --card-bg: #fff; --card-shadow: rgba(0,0,0,0.05);
            --input-bg: #fff; --input-border: #ccc; --badge-gray: #95a5a6; --note-border: #eee; --cal-hover: #ecf0f1;
        }
        [data-theme="dark"] {
            --bg-color: #1a1a2e; --text-color: #eaeaea; --card-bg: #16213e; --card-shadow: rgba(0,0,0,0.3);
            --input-bg: #0f3460; --input-border: #533483; --badge-gray: #555; --note-border: #0f3460; --cal-hover: #1f4068;
        }
        body { font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif; background: var(--bg-color); color: var(--text-color); margin: 0; padding: 20px; font-size: 17px; transition: all 0.3s ease; }
        .container { max-width: 900px; margin: 0 auto; }
        .card { background: var(--card-bg); border-radius: 10px; padding: 25px; margin-bottom: 25px; box-shadow: 0 4px 6px var(--card-shadow); transition: all 0.3s ease; }
        h1, h2 { margin-top: 0; color: var(--text-color); font-size: 1.5em; }
        .weather-box { display: flex; align-items: center; gap: 15px; background: #3498db; color: white; padding: 20px; border-radius: 10px; }
        .weather-box img { width: 70px; height: 70px; }
        .weather-temp { font-size: 2.2em; font-weight: bold; }
        .counter { font-size: 1.2em; text-align: right; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .logout-form { display: inline; margin-left: 15px; }
        form { display: flex; flex-direction: column; gap: 12px; }
        input[type="text"], textarea, select { padding: 12px; border: 1px solid var(--input-border); border-radius: 5px; font-size: 1.05em; width: 100%; box-sizing: border-box; background: var(--input-bg); color: var(--text-color); }
        .btn { padding: 12px 18px; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 1.05em; text-decoration: none; display: inline-block; text-align: center; }
        .btn-success { background: #2ecc71; } .btn-success:hover { background: #27ae60; }
        .btn-warning { background: #f39c12; } .btn-warning:hover { background: #e67e22; }
        .btn-danger { background: #e74c3c; } .btn-danger:hover { background: #c0392b; }
        .btn-secondary { background: var(--badge-gray); } .btn-secondary:hover { opacity: 0.8; }
        .btn-info { background: #3498db; } .btn-info:hover { background: #2980b9; }
        .note-item { border-bottom: 1px solid var(--note-border); padding: 20px 0; }
        .note-item:last-child { border-bottom: none; }
        .badge { padding: 5px 12px; border-radius: 15px; font-size: 0.9em; color: white; display: inline-block; margin-bottom: 10px; }
        .badge-home { background: #3498db; } .badge-work { background: #e74c3c; } .badge-rest { background: #2ecc71; } .badge-none { background: var(--badge-gray); }
        .note-actions { margin-top: 12px; display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }
        .edit-form { display: none; gap: 10px; align-items: center; flex-wrap: wrap; width: 100%; margin-top: 12px; padding-top: 12px; border-top: 1px dashed var(--note-border); }
        .edit-form.active { display: flex; }
        .edit-form input, .edit-form select { width: auto; flex-grow: 1; }
        .success { color: #2ecc71; font-weight: bold; margin-bottom: 20px; font-size: 1.1em; }
        .current-time { font-size: 0.95em; color: var(--badge-gray); margin-bottom: 10px; }
        .note-date { display: inline-block; background: var(--card-bg); border: 1px solid var(--note-border); padding: 5px 10px; border-radius: 4px; font-size: 0.9em; margin-bottom: 10px; }
        .theme-btn { background: var(--card-bg); border: 2px solid var(--text-color); color: var(--text-color); width: 45px; height: 45px; border-radius: 50%; font-size: 1.5em; cursor: pointer; transition: all 0.3s ease; display: flex; align-items: center; justify-content: center; }
        .theme-btn:hover { transform: rotate(20deg) scale(1.1); }
        
        .cal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; font-size: 1.3em; font-weight: bold; }
        .cal-grid { display: grid; grid-template-columns: repeat(7, 1fr); gap: 6px; text-align: center; }
        .cal-day-name { font-weight: bold; color: var(--badge-gray); padding: 8px 0; font-size: 0.95em; }
        .cal-day { padding: 10px 0; border-radius: 8px; position: relative; cursor: pointer; transition: all 0.2s ease; color: var(--text-color); text-decoration: none; display: block; }
        .cal-day:hover { background: var(--cal-hover); transform: scale(1.1); }
        .cal-empty { padding: 10px 0; }
        .cal-today { background: #3498db; color: white; font-weight: bold; box-shadow: 0 4px 6px rgba(52, 152, 219, 0.3); }
        .cal-selected { background: #2ecc71; color: white; font-weight: bold; }
        .cal-dot { position: absolute; bottom: 4px; left: 50%; transform: translateX(-50%); width: 7px; height: 7px; background: #e74c3c; border-radius: 50%; }
        .cal-today .cal-dot, .cal-selected .cal-dot { background: white; }
        
        .forecast-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 10px; text-align: center; margin-top: 15px; }
        .forecast-day { padding: 15px 5px; border-radius: 10px; opacity: 0; transform: translateY(20px); animation: fadeSlideUp 0.5s ease-out forwards; transition: transform 0.2s ease, box-shadow 0.2s ease; cursor: default; }
        .forecast-day:nth-child(1) { animation-delay: 0.1s; } .forecast-day:nth-child(2) { animation-delay: 0.2s; } .forecast-day:nth-child(3) { animation-delay: 0.3s; } .forecast-day:nth-child(4) { animation-delay: 0.4s; } .forecast-day:nth-child(5) { animation-delay: 0.5s; }
        @keyframes fadeSlideUp { to { opacity: 1; transform: translateY(0); } }
        .forecast-day:hover { transform: translateY(-8px); box-shadow: 0 10px 20px rgba(0,0,0,0.1); }
        .forecast-day img { width: 50px; height: 50px; transition: transform 0.3s ease; }
        .forecast-day:hover img { transform: scale(1.2); }
        
        /* Смена дня и ночи на карточках */
        .forecast-daytime { background: #fef9ef; color: #333; }
        .forecast-nighttime { background: #2c3e50; color: #fff; }
        [data-theme="dark"] .forecast-daytime { background: #2d3e50; color: #fff; }
        [data-theme="dark"] .forecast-nighttime { background: #111827; color: #a1a1aa; }
    mark { background-color: #fff3cd; color: #333; padding: 0 2px; border-radius: 3px; }
</style>
<script>var a=document.getElementById("ach-data");if(a){var t=a.dataset.t;var d=document.createElement("div");d.innerHTML="<div style="font-size:2em">🏆</div><div><b>Ачивка!</b><div>"+t+"</div></div>";Object.assign(d.style,{position:"fixed",top:"20px",right:"20px",zIndex:"9999",background:"#2ecc71",color:"#fff",padding:"20px",borderRadius:"12px",boxShadow:"0 10px 25px rgba(0,0,0,0.3)",display:"flex",alignItems:"center",gap:"15px",opacity:"0",transform:"translateY(-20px)",transition:"all 0.5s ease"});document.body.appendChild(d);setTimeout(()=>{d.style.opacity="1";d.style.transform="translateY(0)";},50);setTimeout(()=>{d.remove();},5000);}</script>
<link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
@if(session("achievement"))<div id="ach-data" style="display:none" data-t="{{ session("achievement") }}"></div>@endif
<div class="container">
    <div class="header">
        <h1>Мой блокнот</h1>
        <div style="display: flex; align-items: center;">
            <button class="theme-btn" onclick="toggleTheme()" id="theme-icon">☀️</button>
                <div class="xp-bar" style="display: flex; align-items: center; gap: 10px; margin-left: 15px; background: var(--card-bg); padding: 5px 15px; border-radius: 15px; border: 1px solid var(--note-border);">
        <span style="font-weight: bold; color: #f39c12;" title="Уровень">⭐ Ур. {{ Auth::user()->level() }}</span>
        <div style="width: 100px; height: 10px; background: var(--cal-hover); border-radius: 5px; overflow: hidden;">
            <div style="height: 100%; width: {{ Auth::user()->xpPercent() }}%; background: linear-gradient(90deg, #f39c12, #e74c3c); transition: width 0.5s ease;"></div>
        </div>
        <span style="font-size: 0.8em;">{{ Auth::user()->xp }} XP</span>
    </div>
            <form method="POST" action="/logout" class="logout-form">@csrf<button type="submit" class="btn btn-secondary">Выйти</button></form>
            <strong style="margin-left: 10px;">Привет, {{ Auth::user()->name }}</strong>
        </div>
    </div>
    @if(session("success"))<div class="success">{{ session("success") }}</div>@endif

    <div class="card">
        <h2>Погода сейчас</h2>
        @if($weather)<div class="weather-box"><img src="http://openweathermap.org/img/wn/{{ $weather["icon"] }}@2x.png" alt="icon"><div><div class="weather-temp">{{ $weather["temp"] }}°C</div><div>{{ $weather["city"] }}, {{ ucfirst($weather["desc"]) }}</div></div></div>@else<p>Погода недоступна.</p>@endif
    </div>

    @php
        $dailyForecast = []; $hourlyForecast = [];
        if(!empty($weather["city"])) { try { $forecastResponse = Http::get("https://api.openweathermap.org/data/2.5/forecast", ["q" => $weather["city"], "appid" => config("weather.api_key"), "units" => "metric", "lang" => "ru"]);
        if ($forecastResponse->successful()) { $hourlyForecast = array_slice($forecastResponse->json()["list"], 0, 8); $grouped = []; foreach ($forecastResponse->json()["list"] as $item) { $day = date("Y-m-d", $item["dt"]); $hour = (int)date("H", $item["dt"]); if (!isset($grouped[$day])) { $grouped[$day] = ["icon_data" => $item, "day_temp" => $item, "night_temp" => $item]; } if (abs($hour - 12) < abs((int)date("H", strtotime($grouped[$day]["icon_data"]["dt"])) - 12)) { $grouped[$day]["icon_data"] = $item; } if (abs($hour - 15) < abs((int)date("H", strtotime($grouped[$day]["day_temp"]["dt"])) - 15)) { $grouped[$day]["day_temp"] = $item; } if (abs($hour - 3) < abs((int)date("H", strtotime($grouped[$day]["night_temp"]["dt"])) - 3)) { $grouped[$day]["night_temp"] = $item; } } $dailyForecast = array_slice($grouped, 0, 5, true); } } catch (\Exception $e) {} }
    @endphp

    @php
        $selMonth = request("month", date("m")); $selYear = request("year", date("Y"));
        $daysInMonth = date("t", strtotime("$selYear-$selMonth-01"));
        $firstDayOffset = date("N", strtotime("$selYear-$selMonth-01")) - 1;
        $todayStr = date("Y-m-d");
        $daysWithNotes = [];
        try { $notesQ = \App\Models\Note::whereYear("created_at", $selYear)->whereMonth("created_at", $selMonth)->pluck("created_at"); foreach($notesQ as $dt) { $daysWithNotes[] = date("j", strtotime($dt)); } $daysWithNotes = array_unique($daysWithNotes); } catch (\Exception $e) {}
        $prevMonth = $selMonth - 1; $prevYear = $selYear; if ($prevMonth < 1) { $prevMonth = 12; $prevYear--; }
        $nextMonth = $selMonth + 1; $nextYear = $selYear; if ($nextMonth > 12) { $nextMonth = 1; $nextYear++; }
        $rusMonths = [1=>"Январь", 2=>"Февраль", 3=>"Март", 4=>"Апрель", 5=>"Май", 6=>"Июнь", 7=>"Июль", 8=>"Август", 9=>"Сентябрь", 10=>"Октябрь", 11=>"Ноябрь", 12=>"Декабрь"];
    @endphp

    @if(count($hourlyForecast) > 0)
    <div class="card">
        <h2>⏳ Почасовой прогноз</h2>
        <div style="height: 200px; margin-bottom: 20px; background: var(--card-bg); border-radius: 10px; padding: 15px; border: 1px solid var(--note-border);">
    <canvas id="hourlyChart"></canvas>
</div>
<script>
    // Берем сырые данные из PHP (тут нет скобок, парсер не запутается)
    const rawHours = @json($hourlyForecast);
    
    // Обрабатываем прямо в JavaScript!
    const chartData = rawHours.map(function(h) {
        let d = new Date(h.dt * 1000);
        let time = d.getHours().toString().padStart(2, '0') + ':' + d.getMinutes().toString().padStart(2, '0');
        return { time: time, temp: Math.round(h.main.temp) };
    });
    
    new Chart(document.getElementById('hourlyChart'), {
        type: 'line',
        data: {
            labels: chartData.map(d => d.time),
            datasets: [{
                label: '°C',
                data: chartData.map(d => d.temp),
                borderColor: '#3498db',
                backgroundColor: 'rgba(52, 152, 219, 0.1)',
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#fff',
                pointBorderColor: '#3498db',
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 7
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { 
                y: { 
                    ticks: { 
                        callback: function(value) { return value + '°'; } 
                    } 
                } 
            }
        }
    });
</script>
<div style="display: flex; gap: 15px; overflow-x: auto; padding-bottom: 10px;">
            @foreach($hourlyForecast as $h)
                <div style="text-align: center; min-width: 70px; background: var(--card-bg); padding: 10px; border-radius: 10px; border: 1px solid var(--note-border);">
                    <div style="font-weight: bold; font-size: 0.9em;">{{ date("H:i", $h["dt"]) }}</div>
                    <img src="https://openweathermap.org/img/wn/{{ $h["weather"][0]["icon"] }}@2x.png" alt="icon" style="width: 45px; height: 45px;">
                    <div style="font-weight: bold;">{{ round($h["main"]["temp"]) }}°</div>
                </div>
            @endforeach
        </div>
    </div>
@endif

@if(count($dailyForecast) > 0)
<div class="card"><h2>📅 Прогноз на 5 дней</h2><div class="forecast-grid">
    @foreach($dailyForecast as $day => $data)
        @php
            $hour = (int)date("H", $data["icon_data"]["dt"]);
            $isDaytime = ($hour >= 6 && $hour < 20);
            $dayClass = $isDaytime ? "forecast-day forecast-daytime" : "forecast-day forecast-nighttime";
        @endphp
        <div class="{{ $dayClass }}">
            <div style="font-weight: bold; margin-bottom: 5px;">{{ date("d.m", $data["icon_data"]["dt"]) }}</div>
            <img src="https://openweathermap.org/img/wn/{{ $data["icon_data"]["weather"][0]["icon"] }}@2x.png" alt="Иконка">
            <div style="font-size: 0.85em; margin: 5px 0;">{{ ucfirst($data["icon_data"]["weather"][0]["description"]) }}</div>
            <div style="margin-top: 8px; font-weight: 600;"><span style="color: #e67e22;">☀️ {{ round($data["day_temp"]["main"]["temp"]) }}°</span></div>
            <div style="font-size: 0.9em;"><span style="color: #3498db;">🌙 {{ round($data["night_temp"]["main"]["temp"]) }}°</span></div>
        </div>
    @endforeach
</div></div>
@endif


<div class="card">
        <div class="cal-header">
            <a href="?month={{ $prevMonth }}&year={{ $prevYear }}" class="btn btn-secondary" style="padding: 5px 15px;">◀</a>
            <span>{{ $rusMonths[(int)$selMonth] }} {{ $selYear }}</span>
            <a href="?month={{ $nextMonth }}&year={{ $nextYear }}" class="btn btn-secondary" style="padding: 5px 15px;">▶</a>
        </div>
        <div class="cal-grid">
            <div class="cal-day-name">Пн</div><div class="cal-day-name">Вт</div><div class="cal-day-name">Ср</div><div class="cal-day-name">Чт</div><div class="cal-day-name">Пт</div><div class="cal-day-name">Сб</div><div class="cal-day-name">Вс</div>
            @for($i = 0; $i < $firstDayOffset; $i++)<div class="cal-empty"></div>@endfor
            @for($day = 1; $day <= $daysInMonth; $day++)
                @php $currentDateStr = "$selYear-$selMonth-$day"; $isSelected = (request("date_from") == $currentDateStr); $isToday = ($currentDateStr == $todayStr); $hasNotes = in_array($day, $daysWithNotes); $classes = "cal-day"; if ($isSelected) $classes .= " cal-selected"; elseif ($isToday) $classes .= " cal-today"; @endphp
                <a href="?date_from={{ $currentDateStr }}&date_to={{ $currentDateStr }}&month={{ $selMonth }}&year={{ $selYear }}" class="{{ $classes }}">{{ $day }}@if($hasNotes) <span class="cal-dot"></span> @endif</a>
            @endfor
        </div>
        <div style="text-align: center; margin-top: 15px;"><a href="/" class="btn btn-info" style="font-size: 0.9em; padding: 6px 18px;">Сбросить фильтр</a></div>
    </div>

    <div class="card counter">Найдено заметок: <strong>{{ $count }}</strong></div>
    <div class="card"><h2>Добавить заметку</h2><div class="current-time">Сейчас на сервере: {{ date("d.m.Y H:i:s") }}</div><form action="{{ route("notes.store") }}" method="POST">@csrf<input type="text" name="title" placeholder="Заголовок" required><textarea name="content" rows="3" placeholder="Текст заметки..."></textarea><select name="category_id"><option value="">-- Без категории --</option>@foreach($categories as $cat)<option value="{{ $cat->id }}">{{ $cat->name }}</option>@endforeach</select><button type="submit" class="btn btn-success" style="width: 100%;">➕ Сохранить</button></form></div>

    <div class="card"><form action="/" method="GET" style="display: flex; gap: 10px;"><input type="text" name="search" value="{{ request('search') }}" placeholder="🔍 Поиск по заметкам..." style="flex-grow: 1;"><button type="submit" class="btn btn-info">Найти</button>@if(request('search'))<a href="/" class="btn btn-secondary">Сбросить</a>@endif</form></div>
<div class="card"><h2>Мои заметки</h2>
        @foreach($notes as $note)
            <div class="note-item">
                @php $badgeClass = "badge-none"; if($note->category) { if($note->category->name == "Дом") $badgeClass = "badge-home"; if($note->category->name == "Работа") $badgeClass = "badge-work"; if($note->category->name == "Отдых") $badgeClass = "badge-rest"; } @endphp
                <span class="badge {{ $badgeClass }}">{{ $note->category ? $note->category->name : "Без категории" }}</span>
                <div class="note-date">📅 {{ $note->created_at->format("d.m.Y в H:i") }}</div>
                <strong style="font-size: 1.2em;">{{ $note->title }}</strong>
                <div style="margin: 8px 0;">{!! $note->content ?? "Без текста" !!}</div>
                <div class="note-actions">
                <form action="{{ route("notes.destroy", $note->id) }}" method="POST" onsubmit="return confirm('Точно удалить заметку?');" style="margin:0;">@csrf @method("DELETE")<button type="submit" class="btn btn-danger">🗑️ Удалить</button></form>
            </div>
            <form action="{{ route("notes.update", $note->id) }}" method="POST" class="edit-form">@csrf @method("PUT")
                    <input type="text" name="title" value="{{ $note->title }}" required placeholder="Новый заголовок">
                    <select name="category_id"><option value="">-- Без категории --</option>@foreach($categories as $cat)<option value="{{ $cat->id }}" {{ $note->category_id == $cat->id ? "selected" : "" }}>{{ $cat->name }}</option>@endforeach</select>
                    <textarea name="content" rows="2" placeholder="Текст заметки...">{{ $note->content ?? "" }}</textarea>
                    <button type="submit" class="btn btn-success">💾 Сохранить</button>
                </form>
            </div>
        @endforeach
    </div>
    <div style="text-align: center; margin-top: 20px;"><a href="/admin" class="btn btn-info">Перейти в панель управления →</a></div>
</div>

<script>
    function toggleTheme() {
        const isDark = document.body.getAttribute('data-theme') === 'dark';
        const next = isDark ? 'light' : 'dark';
        document.body.setAttribute('data-theme', next);
        localStorage.setItem('theme', next);
        document.getElementById('theme-icon').innerText = isDark ? '☀️' : '🌙';
    }
    if(localStorage.getItem('theme') === 'dark') {
        document.body.setAttribute('data-theme', 'dark');
        document.getElementById('theme-icon').innerText = '🌙';
    }
</script>
<style>
.ql-toolbar.ql-snow { border-color: var(--input-border) !important; background: var(--input-bg); border-radius: 5px 5px 0 0; }
.ql-container.ql-snow { border-color: var(--input-border) !important; border-radius: 0 0 5px 5px; font-size: 16px; }
mark { background-color: #fff3cd; color: #333; padding: 0 2px; border-radius: 3px; }
</style>
<script>
// 1. Для формы "Добавить заметку" (она всегда видна)
let addForm = document.querySelector('form[action*="notes.store"]');
if (addForm) {
    let addTxt = addForm.querySelector('textarea[name="content"]');
    addTxt.style.display = "none";
    let addDiv = document.createElement("div");
    addTxt.parentNode.insertBefore(addDiv, addTxt);
    let addQ = new Quill(addDiv, { theme: "snow", placeholder: "Текст заметки...", modules: { toolbar: [["bold", "italic", "underline", "strike"], ["image"]] } });
    addForm.addEventListener("submit", function() { addTxt.value = addDiv.querySelector(".ql-editor").innerHTML; });
}

// 2. Для кнопки "Редактировать" (создаем редактор по клику)
document.querySelectorAll(".btn-warning").forEach(function(btn) {
    btn.addEventListener("click", function() {
        // Ждем 50мс, чтобы inline-стиль 'display: flex' успел примениться после клика
        setTimeout(function() {
            let form = btn.closest(".note-item").querySelector(".edit-form");
            if (!form || !form.classList.contains("active")) return;

            let txt = form.querySelector("textarea[name=\"content\"]");
            if (!txt) return;

            // Если редактор еще не создан в этой форме
            if (!form.dataset.quillInit) {
                txt.style.display = "none";
                let editDiv = document.createElement("div");
                txt.parentNode.insertBefore(editDiv, txt);
                new Quill(editDiv, { theme: "snow", placeholder: "Редактирование...", modules: { toolbar: [["bold", "italic", "underline", "strike"], ["image"]] } });
                form.dataset.quillInit = "true";

                // При сохранении формы кладем HTML обратно в скрытое поле
                form.addEventListener("submit", function() {
                    txt.value = editDiv.querySelector(".ql-editor").innerHTML;
                });
            }

            // Вставляем текст заметки в визуальный редактор
            let editorEl = form.querySelector(".ql-editor");
            if (editorEl) {
                editorEl.innerHTML = txt.value;
            }
        }, 50);
    });
});
</script><div id="noteModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); z-index:9999; justify-content:center; align-items:center;" onclick="if(event.target===this)this.style.display='none'">
    <div style="background:var(--card-bg); padding:30px; border-radius:15px; max-width:600px; width:90%; max-height:80vh; overflow-y:auto; position:relative; box-shadow: 0 20px 50px rgba(0,0,0,0.3);">
        <button onclick="document.getElementById('noteModal').style.display='none'" style="position:absolute; top:15px; right:15px; background:none; border:none; font-size:2em; cursor:pointer; color:var(--text-color);">×</button>
        <h2 id="modalTitle" style="margin-top:0; color:var(--text-color);"></h2>
        <div id="modalContent" style="line-height: 1.6; color:var(--text-color);"></div>
    </div>
</div>

<script>
document.querySelectorAll('.preview-btn').forEach(function(btn){
    btn.addEventListener('click', function(){
        document.getElementById('modalTitle').innerText = this.dataset.title;
        // Безопасно переводим текст обратно в HTML для отображения картинок и форматирования
        var txt = document.createElement("textarea");
        txt.innerHTML = this.dataset.content;
        document.getElementById('modalContent').innerHTML = txt.value;
        document.getElementById('noteModal').style.display = 'flex';
    });
});
</script>
</body>
</html>
