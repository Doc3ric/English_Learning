@props([
    'padding' => 'p-6',
    'class'   => '',
])
<div {{ $attributes->merge(['class' => "ds-card {$padding} {$class}"]) }}>
    {{ $slot }}
</div>
