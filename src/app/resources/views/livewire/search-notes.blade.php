<?php
// Маленький помощник для подсветки текста (безопасный для HTML)
function highlightText($text, $search) {
    if (!$search || !$text) return $text;
    // Экранируем спецсимволы поиска для регулярного выражения
    $escapedSearch = preg_quote($search, '/');
    // Заменяем совпадения с сохранением регистра, оборачивая в тег <mark>
    return preg_replace('/(' . $escapedSearch . ')/iu', '<mark>$1</mark>', e($text));
}
?>

<div style="margin-bottom: 20px;">
    <!-- Компактная строка поиска -->
    <div style="position: relative;">
        <input 
            type="text" 
            wire:model.live.debounce.500ms="search" 
            placeholder="Поиск по заметкам..." 
            style="width: 100%; padding: 10px 15px 10px 40px; border: 1px solid #ddd; border-radius: 25px; font-size: 0.95em; outline: none; box-sizing: border-box; background: #fff; box-shadow: 0 2px 5px rgba(0,0,0,0.05); transition: 0.2s;"
            onfocus="this.style.borderColor='#3498db'; this.style.boxShadow='0 2px 10px rgba(52,152,219,0.2)'" 
            onblur="this.style.borderColor='#ddd'; this.style.boxShadow='0 2px 5px rgba(0,0,0,0.05)'"
        >
        <svg style="width: 18px; height: 18px; color: #95a5a6; position: absolute; left: 14px; top: 12px; pointer-events: none;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
    </div>

    <!-- Индикатор "Ищу..." -->
    <div wire:loading wire:target="search" style="color: #3498db; font-size: 0.85em; margin-top: 8px; padding-left: 5px; font-weight: 500;">
        ⚡ Ищу...
    </div>

    @if(strlen($search) >= 2)
        <div style="font-size: 0.85em; color: #7f8c8d; margin: 10px 0 5px 5px;">
            Найдено: <strong>{{ $notes->count() }}</strong>
        </div>
        
        @forelse($notes as $note)
            <div class="card" style="padding: 15px; margin-bottom: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.04);">
                @php 
                    $badgeClass = 'badge-none'; 
                    if($note->category) {
                        if($note->category->name == 'Дом') $badgeClass = 'badge-home';
                        if($note->category->name == 'Работа') $badgeClass = 'badge-work';
                        if($note->category->name == 'Отдых') $badgeClass = 'badge-rest';
                    }
                @endphp
                <span class="badge {{ $badgeClass }}" style="font-size: 0.7em;">{{ $note->category ? $note->category->name : 'Без категории' }}</span>
                
                <!-- ЗАГОЛОВОК С ПОДСВЕТКОЙ -->
                <strong style="display: block; margin: 4px 0; font-size: 1em;">
                    {!! highlightText($note->title, $search) !!}
                </strong>
                
                <!-- ТЕКСТ С ПОДСВЕТКОЙ (обрезанный и без HTML тегов) -->
                <p style="margin: 0; color: #666; font-size: 0.9em;">
                    {!! highlightText(mb_substr(strip_tags($note->content), 0, 120), $search) !!}...
                </p>
            </div>
        @empty
            <div style="text-align: center; color: #7f8c8d; padding: 20px; background: #fff; border-radius: 10px; margin-top: 10px;">
                Ничего не найдено 😔
            </div>
        @endforelse
    @else
        <div style="color: #bbb; font-size: 0.85em; margin-top: 8px; padding-left: 5px;" wire:loading.remove wire:target="search">
            Введите минимум 2 символа...
        </div>
    @endif
</div>

@push('styles')
<style>
    /* Стили для подсветки (как в Google, мягкий желтый цвет) */
    mark {
        background: #fff3bf;
        color: inherit;
        padding: 1px 3px;
        border-radius: 3px;
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
    }
</style>
@endpush
