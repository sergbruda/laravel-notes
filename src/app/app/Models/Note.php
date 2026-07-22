<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Mews\Purifier\Facades\Purifier;

class Note extends Model
{
    protected $fillable = [
        'title',
        'content',
        'category_id',
        'user_id',
        'status',
        'order'
    ];

    // Автоматическая очистка HTML перед сохранением в БД (Защита от XSS)
    public function setContentAttribute($value)
    {
        $this->attributes['content'] = Purifier::clean($value);
    }

    // Сортировка по умолчанию для Канбан-доски
    protected static function booted()
    {
        static::addGlobalScope('order', function (\Illuminate\Database\Eloquent\Builder $query) {
            $query->orderBy('order');
        });
    }
}