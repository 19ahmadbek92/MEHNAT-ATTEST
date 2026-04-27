@props([
    'icon'  => '▦',
    'value' => 0,
    'label' => '',
    'color' => 'teal',
    'href'  => null,
    'trend' => null,
])

@php
    $color = in_array($color, ['teal','gold','green','red','blue']) ? $color : 'teal';
    $tag   = $href ? 'a' : 'div';
@endphp

<{{ $tag }} {{ $attributes->merge([
    'class' => "stat-card stat-card-$color",
    'href'  => $href,
]) }}>
    <div class="stat-icon">{!! $icon !!}</div>
    <div class="stat-value">{{ $value }}</div>
    <div class="stat-label">{{ $label }}</div>
    @if (! is_null($trend))
        <span class="stat-trend {{ ($trend['direction'] ?? 'up') === 'down' ? 'down' : 'up' }}">
            {{ ($trend['direction'] ?? 'up') === 'down' ? '↓' : '↑' }}
            {{ $trend['label'] ?? '' }}
        </span>
    @endif
</{{ $tag }}>
