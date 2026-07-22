@props(['condition' => 'clear sky'])

@php
    $c = strtolower($condition);
    $type = 'clear';
    if (str_contains($c, 'thunder') || str_contains($c, 'гроза')) $type = 'storm';
    elseif (str_contains($c, 'rain') || str_contains($c, 'дождь') || str_contains($c, 'shower') || str_contains($c, 'ливень')) $type = 'rain';
    elseif (str_contains($c, 'snow') || str_contains($c, 'снег')) $type = 'snow';
    elseif (str_contains($c, 'cloud') || str_contains($c, 'облач') || str_contains($c, 'туман') || str_contains($c, 'mist')) $type = 'clouds';
@endphp

<div id="weather-fx" class="weather-fx-container weather-fx-{{ $type }}">
    @if($type === 'clear')
        <div class="fx-sun">
            <div class="fx-sun-core"></div>
            <div class="fx-sun-rays"></div>
        </div>
    @endif

    @if($type === 'rain' || $type === 'storm')
        <div class="fx-rain">
            @for($i = 1; $i <= 40; $i++)
                <div class="fx-drop" style="left: {{ rand(0, 100) }}%; animation-delay: {{ (rand(0, 100) / 100) }}s; animation-duration: {{ 0.5 + (rand(0, 30) / 100) }}s;"></div>
            @endfor
        </div>
    @endif

    @if($type === 'storm')
        <div class="fx-lightning"></div>
        <div class="fx-lightning fx-lightning-2"></div>
    @endif

    @if($type === 'snow')
        <div class="fx-snow">
            @for($i = 1; $i <= 50; $i++)
                <div class="fx-flake" style="left: {{ rand(0, 100) }}%; animation-delay: {{ (rand(0, 100) / 100) }}s; animation-duration: {{ 3 + (rand(0, 50) / 10) }}s; font-size: {{ 10 + rand(0, 10) }}px; opacity: {{ 0.5 + (rand(0, 50) / 100) }};"></div>
            @endfor
        </div>
    @endif
</div>

<style>
.weather-fx-container {
    position: fixed; top: 0; left: 0; width: 100%; height: 100%;
    pointer-events: none; z-index: 9998; overflow: hidden;
    transition: all 1s ease;
}

/* --- СОЛНЦЕ --- */
.fx-sun { position: absolute; top: -50px; right: -50px; width: 200px; height: 200px; }
.fx-sun-core {
    position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);
    width: 60px; height: 60px; background: #FFD700; border-radius: 50%;
    box-shadow: 0 0 40px #FFD700, 0 0 80px rgba(255, 215, 0, 0.4);
    animation: sunPulse 3s infinite ease-in-out;
}
.fx-sun-rays {
    position: absolute; top: 0; left: 0; width: 100%; height: 100%;
    background: conic-gradient(from 0deg, transparent 0deg, rgba(255,215,0,0.3) 30deg, transparent 60deg);
    border-radius: 50%; animation: sunSpin 15s linear infinite;
}
@keyframes sunSpin { to { transform: rotate(360deg); } }
@keyframes sunPulse { 0%, 100% { transform: translate(-50%, -50%) scale(1); } 50% { transform: translate(-50%, -50%) scale(1.1); box-shadow: 0 0 60px #FFD700; } }

/* --- ДОЖДЬ --- */
.fx-drop {
    position: absolute; top: -20px; width: 2px; height: 20px;
    background: linear-gradient(to bottom, transparent, rgba(100, 181, 246, 0.8));
    border-radius: 0 0 5px 5px;
    animation: rainFall linear infinite;
}
@keyframes rainFall { to { transform: translateY(105vh); } }

/* --- ГРОЗА --- */
.fx-lightning {
    position: absolute; top: 0; left: 0; width: 100%; height: 100%;
    background: rgba(255, 255, 255, 0.9); opacity: 0; pointer-events: none;
    animation: flash 8s infinite 2s;
}
.fx-lightning-2 { animation-delay: 4.5s; }
@keyframes flash { 0%, 100% { opacity: 0; } 4% { opacity: 0.8; } 6% { opacity: 0; } 8% { opacity: 0.6; } 10% { opacity: 0; } }

/* --- СНЕГ --- */
.fx-flake {
    position: absolute; top: -20px; color: white;
    animation: snowFall linear infinite;
    text-shadow: 0 0 5px rgba(255,255,255,0.8);
}
@keyframes snowFall {
    to { transform: translateY(105vh) translateX(30px); opacity: 0; }
    50% { transform: translateY(50vh) translateX(-20px); }
}
</style>