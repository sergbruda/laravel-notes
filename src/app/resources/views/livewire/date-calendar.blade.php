<div>
    <!-- СИНЕЙ ФОН КАЛЕНДАРЯ (Адаптируется под темную тему) -->
    <div style="
        background: #e3f2fd; 
        border: 1px solid #90caf9; 
        border-radius: 10px; 
        padding: 15px; 
        user-select: none;
        transition: 0.3s;
    " 
    data-theme-style="background: #172554; border-color: #1e88e5;"
    >
        <style>
            [data-theme="dark"] .calendar-card { background: #172554 !important; border-color: #1e88e5 !important; }
            [data-theme="dark"] .calendar-header { color: #e3f2fd !important; }
            [data-theme="dark"] .cal-day { color: #bbdefb !important; }
            [data-theme="dark"] .cal-day:hover { background: #1e88e5 !important; color: #fff !important; }
            .calendar-card { background: #e3f2fd; border: 1px solid #90caf9; border-radius: 10px; padding: 15px; user-select: none; transition: 0.3s; }
            .calendar-header { color: #1565c0; }
            .cal-day { color: #333; transition: 0.2s; }
            .cal-day:hover { background: #bbdefb !important; }
        </style>

        <div class="calendar-card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                <button wire:click="prevMonth" style="background: rgba(255,255,255,0.5); border: none; color: #1565c0; border-radius: 5px; padding: 5px 10px; cursor: pointer; font-weight: bold;">◀</button>
                <strong class="calendar-header" style="font-size: 1.1em;">{{ $monthName }} {{ $year }}</strong>
                <button wire:click="nextMonth" style="background: rgba(255,255,255,0.5); border: none; color: #1565c0; border-radius: 5px; padding: 5px 10px; cursor: pointer; font-weight: bold;">▶</button>
            </div>

            <div style="display: grid; grid-template-columns: repeat(7, 1fr); gap: 5px; text-align: center; margin-bottom: 10px;">
                <div class="cal-day" style="font-size: 0.85em; font-weight: bold; opacity: 0.7;">Пн</div>
                <div class="cal-day" style="font-size: 0.85em; font-weight: bold; opacity: 0.7;">Вт</div>
                <div class="cal-day" style="font-size: 0.85em; font-weight: bold; opacity: 0.7;">Ср</div>
                <div class="cal-day" style="font-size: 0.85em; font-weight: bold; opacity: 0.7;">Чт</div>
                <div class="cal-day" style="font-size: 0.85em; font-weight: bold; opacity: 0.7;">Пт</div>
                <div class="cal-day" style="font-size: 0.85em; font-weight: bold; opacity: 0.5;">Сб</div>
                <div class="cal-day" style="font-size: 0.85em; font-weight: bold; opacity: 0.5;">Вс</div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(7, 1fr); gap: 5px; text-align: center;">
                @foreach($days as $day)
                    @if($day)
                        @php
                            $currentDate = \Carbon\Carbon::create($year, $month, $day)->format('Y-m-d');
                            $isFrom = $from === $currentDate;
                            $isTo = $to === $currentDate;
                            $isToday = $today === $currentDate; // Проверяем на сегодня
                            
                            $isInRange = false;
                            if ($from && $to && !$isFrom && !$isTo) {
                                $d = \Carbon\Carbon::parse($currentDate);
                                if ($d->between($from, $to)) $isInRange = true;
                            }

                            // Расчет стилей
                            $bg = 'transparent';
                            $color = '#333';
                            $weight = 'normal';
                            $radius = '8px';

                            if ($isFrom || $isTo) {
                                $bg = '#4caf50'; $color = '#fff'; $weight = 'bold';
                            } elseif ($isInRange) {
                                $bg = 'rgba(76, 175, 80, 0.2)';
                            } elseif ($isToday) {
                                // Подсветка сегодняшнего дня (Синий кружок)
                                $bg = 'rgba(21, 101, 192, 0.15)'; 
                                $color = '#1565c0'; 
                                $weight = 'bold';
                                $radius = '50%'; // Делаем круглешек для акцента
                            }
                        @endphp
                        <div 
                            wire:click="selectDay({{ $day }})"
                            class="cal-day"
                            style="
                                padding: 8px 0; 
                                border-radius: {{ $radius }}; 
                                cursor: pointer; 
                                background: {{ $bg }};
                                color: {{ $color }};
                                font-weight: {{ $weight }};
                            "
                        >
                            {{ $day }}
                        </div>
                    @else
                        <div></div>
                    @endif
                @endforeach
            </div>

            <div style="margin-top: 15px; text-align: right;">
                <button wire:click="clearFilter" class="btn btn-secondary" style="font-size: 0.85em; padding: 5px 12px;">Сбросить</button>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('applyDates', (data) => {
                let url = '/';
                if (data.from) {
                    let toDate = data.to ? data.to : data.from;
                    url += `?date_from=${data.from}&date_to=${toDate}`;
                }
                window.location.href = url;
            });
        });
    </script>
    @endpush
</div>
