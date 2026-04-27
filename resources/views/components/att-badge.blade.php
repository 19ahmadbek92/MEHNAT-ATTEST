@props([
    'status' => 'pending',
    'label'  => null,
])

@php
    $map = [
        'submitted'   => ['cls' => 'sb-submitted', 'label' => 'Yuborilgan'],
        'approved'    => ['cls' => 'sb-approved',  'label' => 'Tasdiqlandi'],
        'hr_approved' => ['cls' => 'sb-approved',  'label' => 'Tekshiruvda'],
        'rejected'    => ['cls' => 'sb-rejected',  'label' => 'Rad etildi'],
        'hr_rejected' => ['cls' => 'sb-rejected',  'label' => 'Rad etildi'],
        'finalized'   => ['cls' => 'sb-finalized', 'label' => 'Yakunlangan'],
        'attested'    => ['cls' => 'sb-finalized', 'label' => 'Attestatsiyalandi'],
        'pending'     => ['cls' => 'sb-pending',   'label' => 'Kutilmoqda'],
        'open'        => ['cls' => 'sb-info',      'label' => 'Ochiq'],
        'closed'      => ['cls' => 'sb-pending',   'label' => 'Yopiq'],
        'info'        => ['cls' => 'sb-info',      'label' => 'Ma\'lumot'],
        'progress'    => ['cls' => 'sb-purple',    'label' => 'Jarayonda'],
    ];
    $cls   = $map[$status]['cls']   ?? 'sb-pending';
    $label = $label ?? ($map[$status]['label'] ?? ucfirst($status));
@endphp

<span {{ $attributes->merge(['class' => "status-badge $cls"]) }}>{{ $label }}</span>
