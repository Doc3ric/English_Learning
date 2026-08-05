{{--
    Individual tab item — used inside x-ui.tab-bar.

    Props:
      $value  — identifier matched against parent $active
      $label  — display text
      $active — current active tab value (passed from parent)

    Wire click wired via $attributes so callers can pass wire:click, href, etc.

    Usage:
      <x-ui.tab value="daily" label="Today's Words" :active="$activeTab" wire:click="$set('activeTab','daily')" />
--}}
@props([
    'value'  => '',
    'label'  => '',
    'active' => '',
])

<button
    {{ $attributes->merge(['class' => 'ds-tab ' . ($active === $value ? 'ds-tab-active' : '')]) }}
    type="button"
>
    {{ $label }}
</button>
