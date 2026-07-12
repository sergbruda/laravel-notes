<!DOCTYPE html>
<html lang="ru">
<head><meta charset="UTF-8"><title>Вход</title>
<style>body{font-family:sans-serif;background:#f4f7f6;display:flex;justify-content:center;align-items:center;height:100vh;margin:0;}.card{background:#fff;padding:30px;border-radius:10px;box-shadow:0 4px 6px rgba(0,0,0,0.1);width:300px;}h2{margin-top:0;color:#2c3e50;}input{width:100%;padding:10px;margin:10px 0;border:1px solid #ccc;border-radius:5px;box-sizing:border-box;}button{width:100%;padding:10px;background:#3498db;color:white;border:none;border-radius:5px;cursor:pointer;}button:hover{background:#2980b9;}.links{text-align:center;margin-top:15px;font-size:0.9em;}.error{color:#e74c3c;font-size:0.9em;}</style>
</head>
<body>
<div class="card">
    <h2>Вход</h2>
    @if($errors->any())<div class="error">{{ $errors->first('email') }}</div>@endif
    <form method="POST" action="/login">
        @csrf
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Пароль" required>
        <button type="submit">Войти</button>
    </form>
    <div class="links">Нет аккаунта? <a href="/register">Регистрация</a></div>
</div>
</body>
</html>
