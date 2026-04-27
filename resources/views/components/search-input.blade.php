@props([
    'name'        => 'q',
    'value'       => null,
    'placeholder' => 'Qidirish…',
])

<div class="att-search">
    <input
        type="search"
        name="{{ $name }}"
        value="{{ $value ?? request($name) }}"
        placeholder="{{ $placeholder }}"
        autocomplete="off"
        {{ $attributes }}
    />
</div>
