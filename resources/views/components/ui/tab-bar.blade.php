{{--
    Tab navigation bar component.

    Pass individual tabs as children using wire:click or href.
    Active state is controlled by the parent via the $active prop compared against each tab's $value.

    Usage:
      <x-ui.tab-bar :active="$activeTab">
          <x-ui.tab value="daily"  label="Today's Words" />
          <x-ui.tab value="review" label="Review"        />
          <x-ui.tab value="add"    label="Add Word"      />
      </x-ui.tab-bar>
--}}
@props(['active' => ''])

<div class="ds-tab-bar mb-6">
    {{ $slot }}
</div>
