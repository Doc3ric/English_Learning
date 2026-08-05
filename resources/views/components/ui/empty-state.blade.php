{{--
    Empty state component.

    Props:
      $icon  — emoji or SVG string shown in the icon circle (default: '📭')
      $title — heading text
      $body  — supporting text (optional)

    Optional named slot $action for a CTA button below the text.

    Usage:
      <x-ui.empty-state icon="📖" title="No articles yet" body="Add your first reading article to get started.">
          <x-slot:action>
              <x-ui.button wire:click="openForm">Add Article</x-ui.button>
          </x-slot:action>
      </x-ui.empty-state>
--}}
@props([
    'icon'  => '📭',
    'title' => 'Nothing here yet',
    'body'  => null,
])

<div {{ $attributes->merge(['class' => 'ds-empty']) }}>
    <div class="ds-empty-icon">{{ $icon }}</div>
    <p class="ds-empty-title">{{ $title }}</p>
    @if($body)
        <p class="ds-empty-body">{{ $body }}</p>
    @endif
    @if(isset($action))
        <div class="mt-2">{{ $action }}</div>
    @endif
</div>
