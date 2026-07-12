<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Мои заметки и Погода</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f4f7f6; color: #333; margin: 0; padding: 20px; }
        .container { max-width: 900px; margin: 0 auto; }
        .card { background: #fff; border-radius: 10px; padding: 20px; margin-bottom: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        h1, h2 { margin-top: 0; color: #2c3e50; }
        .weather-box { display: flex; align-items: center; gap: 15px; background: #3498db; color: white; padding: 20px; border-radius: 10px; }
        .weather-box img { width: 60px; height: 60px; }
        .weather-temp { font-size: 2em; font-weight: bold; }
        .counter { font-size: 1.2em; color: #7f8c8d; text-align: right; }
        
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .logout-form { display: inline; }
        
        form { display: flex; flex-direction: column; gap: 10px; }
        input[type="text"], textarea, select { padding: 10px; border: 1px solid #ccc; border-radius: 5px; font-size: 1em; width: 100%; box-sizing: border-box; }
        
        .btn { padding: 10px 15px; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 1em; text-decoration: none; display: inline-block; text-align: center; }
        .btn-success { background: #2ecc71; } .btn-success:hover { background: #27ae60; }
        .btn-warning { background: #f39c12; } .btn-warning:hover { background: #e67e22; }
        .btn-danger { background: #e74c3c; } .btn-danger:hover { background: #c0392b; }
        .btn-secondary { background: #95a5a6; } .btn-secondary:hover { background: #7f8c8d; }
        .btn-info { background: #3498db; } .btn-info:hover { background: #2980b9; }
        
        .note-item { border-bottom: 1px solid #eee; padding: 15px 0; }
        .note-item:last-child { border-bottom: none; }
        
        .badge { padding: 4px 10px; border-radius: 15px; font-size: 0.8em; color: white; display: inline-block; margin-bottom: 8px; }
        .badge-home { background: #3498db; }
        .badge-work { background: #e74c3c; }
        .badge-rest { background: #2ecc71; }
        .badge-none { background: #95a5a6; }

        .note-actions { margin-top: 10px; display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }
        .edit-form { display: flex; gap: 8px; align-items: center; flex-wrap: wrap; width: 100%; margin-top: 10px; padding-top: 10px; border-top: 1px dashed #ccc; }
        .edit-form input, .edit-form select { width: auto; flex-grow: 1; }
        
        .success { color: #2ecc71; font-weight: bold; margin-bottom: 15px; }
        .current-time { font-size: 0.9em; color: #7f8c8d; margin-bottom: 10px; }
        .note-date { display: inline-block; background: #ecf0f1; padding: 4px 8px; border-radius: 4px; font-size: 0.85em; color: #555; margin-bottom: 8px; }
        
        /* Стили для формы поиска */
        .search-form { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }
        .search-form input[type="date"] { width: auto; }
        .search-form a { font-size: 0.9em; color: #3498db; text-decoration: none; }
    </style>
</head>
<body>

<div class="container">
    <!-- Шапка -->
    <div class="header">
        <h1>Мой блокнот</h1>
        <div>
            Привет, <strong>{{ Auth::user()->name }}</strong>! 
            <form method="POST" action="/logout" class="logout-form">
                @csrf
                <button type="submit" class="btn btn-secondary" style="margin-left: 15px;">Выйти</button>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <h2>Погода сейчас</h2>
        @if($weather)
            <div class="weather-box">
                <img src="http://openweathermap.org/img/wn/{{ $weather['icon'] }}@2x.png" alt="icon">
                <div>
                    <div class="weather-temp">{{ $weather['temp'] }}°C</div>
                    <div>{{ $weather['city'] }}, {{ ucfirst($weather['desc']) }}</div>
                </div>
            </div>
        @else
            <p>Погода недоступна.</p>
        @endif
    </div>

    <!-- Поиск по дате -->
    <div class="card">
        <h2>Поиск по дате создания</h2>
        <form method="GET" action="/" class="search-form">
            <label>С: <input type="date" name="date_from" value="{{ request('date_from') }}"></label>
            <label>По: <input type="date" name="date_to" value="{{ request('date_to') }}"></label>
            <button type="submit" class="btn btn-info">Найти</button>
            <a href="/">Сбросить фильтр</a>
        </form>
    </div>

    <div class="card counter">
        Найдено заметок: <strong>{{ $count }}</strong>
    </div>

    <div class="card">
        <h2>Добавить заметку</h2>
        <!-- Показываем текущее время сервера -->
        <div class="current-time">Сейчас на сервере: {{ date('d.m.Y H:i:s') }}</div>
        
        <form action="{{ route('notes.store') }}" method="POST">
            @csrf
            <input type="text" name="title" placeholder="Заголовок" required>
            <textarea name="content" rows="3" placeholder="Текст заметки..."></textarea>
            <select name="category_id">
                <option value="">-- Без категории --</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-success" style="width: 100%;">➕ Сохранить</button>
        </form>
    </div>

    <div class="card">
        <h2>Мои заметки</h2>
        @foreach($notes as $note)
            <div class="note-item">
                @php 
                    $badgeClass = 'badge-none'; 
                    if($note->category) {
                        if($note->category->name == 'Дом') $badgeClass = 'badge-home';
                        if($note->category->name == 'Работа') $badgeClass = 'badge-work';
                        if($note->category->name == 'Отдых') $badgeClass = 'badge-rest';
                    }
                @endphp
                
                <span class="badge {{ $badgeClass }}">
                    {{ $note->category ? $note->category->name : 'Без категории' }}
                </span>
                
                <!-- Красиво выделенная дата создания -->
                <div class="note-date">📅 Создано: {{ $note->created_at->format('d.m.Y в H:i') }}</div>
                
                <strong>{{ $note->title }}</strong>
                <p style="margin: 5px 0; color: #555;">{{ $note->content ?? 'Без текста' }}</p>
                
                <div class="note-actions">
                    <button class="btn btn-warning btn-sm" onclick="this.parentElement.querySelector('.edit-form').style.display = 'flex'">✏️ Редактировать</button>
                    
                    <form action="{{ route('notes.destroy', $note->id) }}" method="POST" onsubmit="return confirm('Точно удалить заметку?');" style="margin:0;">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger">🗑️ Удалить</button>
                    </form>
                </div>

                <form action="{{ route('notes.update', $note->id) }}" method="POST" class="edit-form" style="display: none;">
                    @csrf @method('PUT')
                    <input type="text" name="title" value="{{ $note->title }}" required placeholder="Новый заголовок">
                    <select name="category_id">
                        <option value="">-- Без категории --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ $note->category_id == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-success">💾 Сохранить изменения</button>
                </form>
            </div>
        @endforeach
    </div>
</div>

</body>
</html>
