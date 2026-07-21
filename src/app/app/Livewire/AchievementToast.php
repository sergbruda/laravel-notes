<?php
namespace App\Livewire;
use Livewire\Component;
class AchievementToast extends Component
{
    public $title = '';
    public $show = false;
    public function mount() {
        if ($achievement = session('achievement')) {
            $this->title = $achievement;
            session()->forget('achievement');
            $this->show = true;
        }
    }
    public function render() { return view('livewire.achievement-toast'); }
}