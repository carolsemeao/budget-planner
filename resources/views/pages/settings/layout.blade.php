<div class="flex items-start max-md:flex-col">
    <div class="me-10 w-full pb-4 md:w-[220px]">
        <ul class="menu w-full rounded-box w-56" aria-label="{{ __('Settings') }}">
            <li><a href="{{ route('profile.edit') }}" wire:navigate>{{ __('Profile') }}</a></li>
            <li><a href="{{ route('security.edit') }}" wire:navigate>{{ __('Security') }}</a></li>
            {{-- <li><a href="{{ route('appearance.edit') }}" wire:navigate>{{ __('Appearance') }}</a></li> --}}
        </ul>
    </div>

    <div class="divider md:hidden"></div>
    <div class="divider divider-horizontal me-10 max-md:hidden"></div>

    <div class="flex-1 self-stretch max-md:pt-6">
        <h2 class="font-title">{{ $heading ?? '' }}</h2>
        <p>{{ $subheading ?? '' }}</p>

        <div class="mt-5 w-full max-w-lg">
            {{ $slot }}
        </div>
    </div>
</div>