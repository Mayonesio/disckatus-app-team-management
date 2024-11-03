@props(['type', 'rating'])

@php
$throwNames = [
    'hammer' => 'H',
    'scoober' => 'S',
    'push_pass' => 'P',
    'thumber' => 'T',
    'low_release' => 'L',
    'high_release' => 'H',
    'espantaguiris' => 'E',
    'blade' => 'B',
    'no_look' => 'N',
    'over_the_head' => 'O',
    'upside_down' => 'U'
];

$colors = [
    'border' => $rating >= 8 ? '#FFD700' : ($rating >= 5 ? '#C0C0C0' : '#CD7F32'),
    'background' => $rating >= 8 ? '#ffd70033' : ($rating >= 5 ? '#c0c0c033' : '#cd7f3233'),
];
@endphp

<div class="relative group">
    <svg class="w-12 h-12" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
        <defs>
            <linearGradient id="{{ $type }}_gradient" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" style="stop-color:{{ $colors['border'] }};stop-opacity:1" />
                <stop offset="100%" style="stop-color:{{ $colors['background'] }};stop-opacity:1" />
            </linearGradient>
        </defs>
        <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm0 2.18l7 3.12v5.7c0 4.83-3.36 9.36-7 10.46-3.64-1.1-7-5.63-7-10.46v-5.7l7-3.12z"
              fill="url(#{{ $type }}_gradient)"/>
        <circle cx="12" cy="12" r="5" fill="#10163f"/>
    </svg>
    <span class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 text-white text-xs font-bold">
        {{ $throwNames[$type] ?? substr(ucfirst($type), 0, 1) }}
    </span>
    <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 bg-gray-800 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap z-10">
        {{ ucfirst(str_replace('_', ' ', $type)) }}: {{ $rating }}/10
    </div>
</div>