@props([
    'title'    => '',
    'subtitle' => null,
    'crumbs'   => [],
])

<div class="page-header">
    <div style="min-width:0;flex:1;">
        @if (count($crumbs) > 0)
            <div class="crumbs">
                @foreach ($crumbs as $idx => $c)
                    @if (! is_null($c['url'] ?? null) && $idx < count($crumbs) - 1)
                        <a href="{{ $c['url'] }}">{{ $c['label'] }}</a>
                        <span class="sep">/</span>
                    @else
                        <span class="current">{{ $c['label'] }}</span>
                    @endif
                @endforeach
            </div>
        @endif
        <div class="ph-title">{{ $title }}</div>
        @if ($subtitle)
            <div class="ph-subtitle">{{ $subtitle }}</div>
        @endif
    </div>

    @if (isset($actions))
        <div class="ph-actions">{{ $actions }}</div>
    @endif
</div>
