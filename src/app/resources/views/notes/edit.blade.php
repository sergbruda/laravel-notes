@extends('layouts.app')
@section('content')
<div class="container">
<h2>Редактировать заметку</h2>
<article>
<form method="POST" action="/notes/{{ $note->id }}">
@method('PUT')
@csrf
<label>Заголовок:</label>
<input name="title" required value="{{ $note->title }}">
<label>Категория:</label>
<select name="category_id">
<option value="">-- Без категории --</option>
@foreach($categories as $cat)
<option value="{{ $cat->id }}" {{ $note->category_id == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
@endforeach
</select>
<label>Текст:</label>
<textarea name="content" rows="3" required>{{ $note->content }}</textarea>
<button type="submit" class="primary">Обновить</button>
<a href="/notes" class="button">Отмена</a>
</form>
</article>
</div>
@endsection
