{{--
    Badge / pill component.

    Props:
      $variant — 'emerald' | 'amber' | 'red' | 'slate'  (default: 'slate')
      Note: indigo has been retired — CEFR level badges use 'slate' (informational/neutral).

    Usage:
      <x-ui.badge variant="emerald">B1</x-ui.badge>
      <x-ui.badge variant="amber">Warning</x-ui.badge>
      <x-ui.badge variant="red">Error</x-ui.badge>
      <x-ui.badge>B2 (CEFR level — default slate)</x-ui.badge>
--}}
@props([
    'variant' => 'slate',
])

@php
    $variantClass = match($variant) {
        'emerald' => 'ds-badge-emerald',
        'amber'   => 'ds-badge-amber',
        'red'     => 'ds-badge-red',
        default   => 'ds-badge-slate',
    };
@endphp

<span {{ $attributes->merge(['class' => $variantClass]) }}>
    {{ $slot }}
</span>
