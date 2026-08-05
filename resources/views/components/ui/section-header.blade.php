{{--
    Section header inside a card.

    Props:
      $title    — main heading text
      $subtitle — smaller secondary text (optional)

    Optional named slot $actions for right-aligned buttons/badges.

    Usage:
      <x-ui.section-header title="Weekly Goals" subtitle="Jan 1 – Jan 7">
          <x-slot:actions>
              <x-ui.button variant="secondary" size="sm" wire:click="$toggle('editing')">Edit</x-ui.button>
          </x-slot:actions>
      </x-ui.section-header>
--}}
@props([
    'title'    => '',
    'subtitle' => null,
])

<div {{ $attributes->merge(['class' => 'flex items-start justify-between pb-4 mb-6 border-b border-slate-800']) }}>
    <div>
        <h3 class="ds-section-title">{{ $title }}</h3>
        @if($subtitle)
            <p class="ds-muted mt-0.5">{{ $subtitle }}</p>
        @endif
    </div>
    @if(isset($actions))
        <div class="flex items-center gap-2 shrink-0 ml-4">{{ $actions }}</div>
    @endif
</div>
