{{--
    Button component.

    Props:
      $variant  — 'primary' | 'secondary' | 'danger' | 'ghost'   (default: 'primary')
      $size     — 'sm' | 'md' | 'lg'                              (default: 'md')
      $type     — HTML button type attribute                        (default: 'button')

    Usage:
      <x-ui.button>Save</x-ui.button>
      <x-ui.button variant="secondary" size="sm">Cancel</x-ui.button>
      <x-ui.button variant="danger" size="lg" wire:click="delete">Delete</x-ui.button>
--}}
@props([
    'variant' => 'primary',
    'size'    => 'md',
    'type'    => 'button',
])

@php
    $variantClass = match($variant) {
        'secondary' => 'ds-btn-secondary',
        'danger'    => 'ds-btn-danger',
        'ghost'     => 'ds-btn-ghost',
        default     => 'ds-btn-primary',
    };

    $sizeClass = match($size) {
        'sm' => 'ds-btn-sm',
        'lg' => 'ds-btn-lg',
        default => 'ds-btn-md',
    };
@endphp

<button
    type="{{ $type }}"
    {{ $attributes->merge(['class' => "ds-btn {$sizeClass} {$variantClass}"]) }}
>
    {{ $slot }}
</button>
