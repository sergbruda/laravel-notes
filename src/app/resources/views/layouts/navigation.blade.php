<nav class="container-fluid">
@auth
<a href="/notes">Заметки</a>
<form method="POST" action="/logout" style="display:inline">
@csrf
<button type="submit" class="secondary">Выйти</button>
</form>
@else
<a href="/login">Войти</a>
<a href="/register">Регистрация</a>
@endauth
</nav>