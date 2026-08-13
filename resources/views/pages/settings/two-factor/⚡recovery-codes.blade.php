<?php

use Laravel\Fortify\Actions\GenerateNewRecoveryCodes;
use Livewire\Attributes\Locked;
use Livewire\Component;

new class extends Component {
    #[Locked]
    public array $recoveryCodes = [];

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->loadRecoveryCodes();
    }

    /**
     * Generate new recovery codes for the user.
     */
    public function regenerateRecoveryCodes(GenerateNewRecoveryCodes $generateNewRecoveryCodes): void
    {
        $generateNewRecoveryCodes(auth()->user());

        $this->loadRecoveryCodes();
    }

    /**
     * Load the recovery codes for the user.
     */
    private function loadRecoveryCodes(): void
    {
        $user = auth()->user();

        if ($user->hasEnabledTwoFactorAuthentication() && $user->two_factor_recovery_codes) {
            try {
                $this->recoveryCodes = json_decode(decrypt($user->two_factor_recovery_codes), true);
            } catch (Exception) {
                $this->addError('recoveryCodes', 'Failed to load recovery codes');

                $this->recoveryCodes = [];
            }
        }
    }
}; ?>

<div class="card card-dash bg-base-100 border-base-content w-full mt-6" wire:cloak
    x-data="{ showRecoveryCodes: false }">
    <div class="card-body">
        <h3 class="card-title">
            <flux:icon.lock-closed variant="outline" class="size-5" />
            {{ __('2FA recovery codes') }}
        </h3>
        <p>{{ __('Recovery codes let you regain access if you lose your 2FA device. Store them in a secure password
            manager.') }}</p>


        <div class="card-actions justify-end">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <button class="btn btn-primary" x-show="!showRecoveryCodes" variant="primary"
                    @click="showRecoveryCodes = true;" aria-expanded="false" aria-controls="recovery-codes-section">
                    <flux:icon.eye variant="outline" />
                    {{ __('View recovery codes') }}
                </button>

                <button class="btn btn-primary" x-show="showRecoveryCodes" variant="primary"
                    @click="showRecoveryCodes = false;" aria-expanded="true" aria-controls="recovery-codes-section">
                    <flux:icon.eye-slash variant="outline" />
                    {{ __('Hide recovery codes') }}
                </button>


                @if (filled($recoveryCodes))
                    <button class="btn btn-neutral" x-show="showRecoveryCodes" variant="primary"
                        wire:click="regenerateRecoveryCodes">
                        <flux:icon.arrow-path variant="outline" />
                        {{ __('Regenerate codes') }}
                    </button>
                @endif
            </div>

            <div x-show="showRecoveryCodes" x-transition id="recovery-codes-section" class="relative overflow-hidden"
                x-bind:aria-hidden="!showRecoveryCodes">
                <div class="mt-3 space-y-3">
                    @error('recoveryCodes')
                        <div role="alert" class="alert alert-error">
                            <flux:icon.x-circle variant="outline" />
                            <span>{{$message}}</span>
                        </div>
                    @enderror

                    @if (filled($recoveryCodes))
                        <div class="grid gap-1 p-4 font-mono text-sm rounded-lg bg-neutral-content" role="list"
                            aria-label="{{ __('Recovery codes') }}">
                            @foreach($recoveryCodes as $code)
                                <div role="listitem" class="select-text text-neutral"
                                    wire:loading.class="opacity-50 animate-pulse">
                                    {{ $code }}
                                </div>
                            @endforeach
                        </div>
                        <p class="text-xs opacity-70">
                            {{ __('Each recovery code can be used once to access your account and will be removed after use. If
                                                                                                                    you need more, click Regenerate codes above.') }}
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>