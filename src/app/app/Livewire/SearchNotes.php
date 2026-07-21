<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Note;

class SearchNotes extends Component
{
    public string $search = '';

    // Эта функция срабатывает КАЖДЫЙ РАЗ, когда вы меняете текст в строке
    public function render()
    {
        $notes = [];

        // Если строка не пустая, ищем через MeiliSearch
        if (strlen($this->search) >= 2) {
            $notes = Note::search($this->search)->get();
        } else {
            // Иначе показываем последние 10 заметок
            $notes = Note::orderBy('created_at', 'desc')->take(10)->get();
        }

        return view('livewire.search-notes', compact('notes'));
    }
}
