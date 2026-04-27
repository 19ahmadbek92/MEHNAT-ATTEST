@props([
    'icon'  => '◯',
    'title' => 'Hali hech narsa yo\'q',
    'description' => null,
])

<div {{ $attributes->merge(['class' => 'empty-state']) }}>
    <div class="empty-state-icon">{!! $icon !!}</div>
    <h4>{{ $title }}</h4>
    @if ($description)
        <p>{{ $description }}</p>
    @endif
    @if (isset($action))
        <div>{{ $action }}</div>
    @endif
</div>
