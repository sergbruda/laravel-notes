<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Mews\Purifier\Facades\Purifier;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Note extends Model
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "Заметка была " . match($eventName) {
                'created' => 'создана',
                'updated' => 'обновлена',
                'deleted' => 'удалена'
            });
    }

    protected $fillable = [
        'title',
        'content',
        'category_id',
        'user_id',
        'status',
        'order'
    ];

    public function setContentAttribute($value)
    {
        $this->attributes['content'] = Purifier::clean($value);
    }

    protected static function booted()
    {
        static::addGlobalScope('order', function (\Illuminate\Database\Eloquent\Builder $query) {
            $query->orderBy('order');
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}