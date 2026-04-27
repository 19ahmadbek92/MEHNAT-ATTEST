@props([
    'status' => 'pending',
    'label'  => null,
])

@php
    $map = [
        'submitted' => ['cls' => 'sb-submitted', 'label' => 'Yangi'],
        'approved'  => ['cls' => 'sb-approved',  'label' => 'Tasdiqlandi'],
        'rejected'  => ['cls' => 'sb-rejected',  'label' => 'Rad etildi'],
        'finalized' => ['cls' => 'sb-finalized', 'label' => 'Yakunlandi'],
        'pending'   => ['cls' => 'sb-pending',   'label' => 'Kutilmoqda'],
        'info'      => ['cls' => 'sb-info',      'label' => 'Ma\'lumot'],
        'progress'  => ['cls' => 'sb-purple',    'label' => 'Jarayonda'],
    ];
    $cls   = $map[$status]['cls']   ?? 'sb-pending';
    $label = $label ?? ($map[$status]['label'] ?? ucfirst($status));
@endphp

<span {{ $attributes->merge(['class' => "status-badge $cls"]) }}>{{ $label }}</span>
