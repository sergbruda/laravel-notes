<?php

namespace App\Livewire;

use Livewire\Component;
use Carbon\Carbon;

class DateCalendar extends Component
{
    public $month;
    public $year;
    public $from = null;
    public $to = null;

    public function mount()
    {
        $this->from = request('date_from');
        $this->to = request('date_to');
        $this->month = $this->from ? Carbon::parse($this->from)->month : now()->month;
        $this->year = $this->from ? Carbon::parse($this->from)->year : now()->year;
    }

    public function render()
    {
        $daysInMonth = Carbon::create($this->year, $this->month, 1)->daysInMonth;
        $startDay = Carbon::create($this->year, $this->month, 1)->dayOfWeekIso; 
        
        $days = [];
        for ($i = 1; $i < $startDay; $i++) $days[] = null;
        for ($i = 1; $i <= $daysInMonth; $i++) $days[] = $i;

        $ruMonths = [1=>'Январь', 2=>'Февраль', 3=>'Март', 4=>'Апрель', 5=>'Май', 6=>'Июнь', 7=>'Июль', 8=>'Август', 9=>'Сентябрь', 10=>'Октябрь', 11=>'Ноябрь', 12=>'Декабрь'];
        
        // Определяем сегодняшний день
        $today = now()->format('Y-m-d');

        return view('livewire.date-calendar', [
            'days' => $days,
            'monthName' => $ruMonths[$this->month],
            'today' => $today, // Передаем сегодня в вид
        ]);
    }

    public function prevMonth()
    {
        $date = Carbon::create($this->year, $this->month, 1)->subMonth();
        $this->month = $date->month;
        $this->year = $date->year;
    }

    public function nextMonth()
    {
        $date = Carbon::create($this->year, $this->month, 1)->addMonth();
        $this->month = $date->month;
        $this->year = $date->year;
    }

    public function selectDay($day)
    {
        $selectedDate = Carbon::create($this->year, $this->month, $day)->format('Y-m-d');

        if (!$this->from || ($this->from && $this->to)) {
            $this->from = $selectedDate;
            $this->to = null;
        } else {
            $this->to = $selectedDate;
            if ($this->from === $this->to) {
                $this->to = $this->from;
            }
            $this->dispatch('applyDates', from: $this->from, to: $this->to);
        }
    }

    public function clearFilter()
    {
        $this->from = null;
        $this->to = null;
        $this->dispatch('applyDates', from: null, to: null);
    }
}
