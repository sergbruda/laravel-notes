<?php
namespace App\Events;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Note;
class NoteCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public function __construct(public Note $note) {}
}