<?php

return [
    'api_key' => env('WEATHER_API_KEY'),
    'default_city' => env('WEATHER_DEFAULT_CITY', 'Ufa'),
    'units' => env('WEATHER_UNITS', 'metric'),
    'lang' => env('WEATHER_LANG', 'ru'),
];
