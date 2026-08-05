@props([
    'padding' => 'p-5',
    'class'   => '',
])
<div {{ $attributes->merge(['class' => "ds-card-nested {$padding} {$class}"]) }}>
    {{ $slot }}
</div>
