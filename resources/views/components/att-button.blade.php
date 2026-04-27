@props([
    'variant' => 'primary',
    'size'    => null,
    'href'    => null,
    'type'    => 'button',
    'icon'    => null,
])

@php
    $variants = ['primary','secondary','ghost','danger','gold','success'];
    $variant  = in_array($variant, $variants) ? $variant : 'primary';
    $sizeCls  = $size === 'sm' ? ' btn-att-sm' : ($size === 'lg' ? ' btn-att-lg' : '');
    $cls      = "btn-att btn-att-$variant".$sizeCls;
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $cls]) }}>
        @if ($icon){!! $icon !!}@endif
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $cls]) }}>
        @if ($icon){!! $icon !!}@endif
        {{ $slot }}
    </button>
@endif
