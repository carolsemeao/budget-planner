<?php

use Laravel\Fortify\Actions\ConfirmTwoFactorAuthentication;
use Laravel\Fortify\Actions\EnableTwoFactorAuthentication;
use Laravel\Fortify\Fortify;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;

new class extends Component {
    #[Locked]
    public bool $requiresConfirmation;

    #[Locked]
    public string $qrCodeSvg = '';

    #[Locked]
    public string $manualSetupKey = '';

    public bool $showVerificationStep = false;

    public bool $setupComplete = false;

    #[Validate('required|string|size:6', onUpdate: false)]
    public string $code = '';

    /**
     * Mount the component.
     */
    public function mount(bool $requiresConfirmation): void
    {
        $this->requiresConfirmation = $requiresConfirmation;
    }

    #[On('start-two-factor-setup')]
    public function startTwoFactorSetup(): void
    {
        $enableTwoFactorAuthentication = app(EnableTwoFactorAuthentication::class);
        $enableTwoFactorAuthentication(auth()->user());

        $this->loadSetupData();

        // Open after Livewire morphs — opening before the re-render loses dialog open state.
        $this->js('document.getElementById("two_factor_setup_modal")?.showModal()');
    }

    /**
     * Load the two-factor authentication setup data for the user.
     */
    private function loadSetupData(): void
    {
        $user = auth()->user()?->fresh();

        try {
            if (!$user || !$user->two_factor_secret) {
                throw new Exception('Two-factor setup secret is not available.');
            }

            $this->qrCodeSvg = $user->twoFactorQrCodeSvg();
            $this->manualSetupKey = Fortify::currentEncrypter()->decrypt($user->two_factor_secret);
        } catch (Exception) {
            $this->addError('setupData', 'Failed to fetch setup data.');

            $this->reset('qrCodeSvg', 'manualSetupKey');
        }
    }

    /**
     * Show the two-factor verification step if necessary.
     */
    public function showVerificationIfNecessary(): void
    {
        if ($this->requiresConfirmation) {
            $this->showVerificationStep = true;

            $this->resetErrorBag();

            return;
        }

        $this->closeModal();
        $this->dispatch('two-factor-enabled');
    }

    /**
     * Confirm two-factor authentication for the user.
     */
    public function confirmTwoFactor(ConfirmTwoFactorAuthentication $confirmTwoFactorAuthentication): void
    {
        $this->code = trim($this->code);

        $this->validate();

        $confirmTwoFactorAuthentication(auth()->user()->fresh(), $this->code);

        $this->setupComplete = true;

        $this->closeModal();

        $this->dispatch('two-factor-enabled');
    }

    /**
     * Reset two-factor verification state.
     */
    public function resetVerification(): void
    {
        $this->reset('code', 'showVerificationStep');

        $this->resetErrorBag();
    }

    /**
     * Close the two-factor authentication modal.
     */
    public function closeModal(): void
    {
        $this->reset(
            'code',
            'manualSetupKey',
            'qrCodeSvg',
            'showVerificationStep',
            'setupComplete',
        );

        $this->resetErrorBag();

        $this->js('document.getElementById("two_factor_setup_modal")?.close()');
    }

    /**
     * Get the current modal configuration state.
     */
    #[Computed]
    public function modalConfig(): array
    {
        if ($this->setupComplete) {
            return [
                'title' => __('Two-factor authentication enabled'),
                'description' => __('Two-factor authentication is now enabled. Scan the QR code or enter the setup key in your authenticator app.'),
                'buttonText' => __('Close'),
            ];
        }

        if ($this->showVerificationStep) {
            return [
                'title' => __('Verify authentication code'),
                'description' => __('Enter the 6-digit code from your authenticator app.'),
                'buttonText' => __('Continue'),
            ];
        }

        return [
            'title' => __('Enable two-factor authentication'),
            'description' => __('To finish enabling two-factor authentication, scan the QR code or enter the setup key in your authenticator app.'),
            'buttonText' => __('Continue'),
        ];
    }
}; ?>

<dialog id="two_factor_setup_modal" focusable wire:ignore.self class="modal modal-bottom sm:modal-middle">
    <div class="modal-box">
        <form method="dialog">
            <button type="button" class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2"
                wire:click="closeModal">✕</button>
        </form>
        <div class="space-y-6">
            <div class="flex flex-col items-center space-y-4">
                <div class="p-0.5 w-auto rounded-full border border-base-200 bg-white shadow-sm">
                    <div class="p-2.5 rounded-full border border-base-100/20 overflow-hidden bg-base-content relative">
                        <div
                            class="flex items-stretch absolute inset-0 w-full h-full divide-x [&>div]:flex-1 divide-white justify-around opacity-20">
                            @for ($i = 1; $i <= 5; $i++)
                                <div></div>
                            @endfor
                        </div>

                        <div
                            class="flex flex-col items-stretch absolute w-full h-full divide-y [&>div]:flex-1 inset-0 divide-white justify-around opacity-20">
                            @for ($i = 1; $i <= 5; $i++)
                                <div></div>
                            @endfor
                        </div>

                        <flux:icon.qr-code class="relative z-20 text-white" />
                    </div>
                </div>

                <div class="space-y-2 text-center">
                    <h3 class="font-title">{{ $this->modalConfig['title'] }}</h3>
                    <p>{{ $this->modalConfig['description'] }}</p>
                </div>
            </div>
        </div>
        @if ($showVerificationStep)
            <div class="space-y-6">
                <div class="flex flex-col items-center mt-4 mb-8 justify-center" x-data
                    x-init="$nextTick(() => $el.querySelector('input')?.focus())">
                    <label class="otp otp-lg mx-auto">
                        <span></span>
                        <span></span>
                        <span></span>
                        <span></span>
                        <span></span>
                        <span></span>
                        <input
                            type="text"
                            name="code"
                            wire:model.live="code"
                            autocomplete="one-time-code"
                            inputmode="numeric"
                            maxlength="6"
                            pattern="[0-9]{6}"
                        />
                    </label>
                    @error('code')
                        <p class="mt-2 text-sm text-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="join join-vertical lg:join-horizontal w-full">
                    <button type="button" class="btn btn-outline join-item"
                        wire:click="resetVerification">{{ __('Back') }}</button>
                    <button
                        type="button"
                        class="btn btn-outline btn-primary join-item"
                        wire:click="confirmTwoFactor"
                        x-bind:disabled="$wire.code.length < 6"
                    >{{ __('Confirm') }}</button>
                </div>
            </div>
        @else
            @error('setupData')
                <div role="alert" class="alert alert-error">
                    <flux:icon.x-circle />
                    <span>{{ $message }}</span>
                </div>
            @enderror

            <div class="flex justify-center flex-1">
                <div class="relative w-64 overflow-hidden aspect-square">
                    @empty($qrCodeSvg)
                        <div
                            class="absolute inset-0 flex items-center border rounded-lg border-base-content/50 justify-center bg-base-content/50">
                            <span class="loading loading-spinner loading-xl"></span>
                        </div>
                    @else
                        <div x-data class="flex items-center justify-center h-full">
                            <div class="bg-white p-3 rounded"
                                :style="($flux.appearance === 'dark' || ($flux.appearance === 'system' && $flux.dark)) ? 'filter: invert(1) brightness(1.5)' : ''">
                                {!! $qrCodeSvg !!}
                            </div>
                        </div>
                    @endempty
                </div>
            </div>

            <button class="btn btn-soft btn-primary w-full" wire:click="showVerificationIfNecessary"
                :disabled="$errors->has('setupData')">
                {{ $this->modalConfig['buttonText'] }}
            </button>

            <div>
                <div class="divider my-10">{{ __('or, enter the code manually') }}</div>

                <div class="flex items-center space-x-2" x-data="{
                                                                                                copied: false,
                                                                                                async copy() {
                                                                                                    const input = this.$refs.setupKey ?? this.$el.querySelector('input[readonly]');
                                                                                                    const value = input?.value ?? '';

                                                                                                    if (! value) {
                                                                                                        return;
                                                                                                    }

                                                                                                    try {
                                                                                                        if (navigator.clipboard?.writeText) {
                                                                                                            await navigator.clipboard.writeText(value);
                                                                                                        } else {
                                                                                                            throw new Error('Clipboard API unavailable');
                                                                                                        }
                                                                                                    } catch (e) {
                                                                                                        input.focus();
                                                                                                        input.select();
                                                                                                        input.setSelectionRange(0, value.length);
                                                                                                        document.execCommand('copy');
                                                                                                        input.blur();
                                                                                                    }

                                                                                                    this.copied = true;
                                                                                                    setTimeout(() => this.copied = false, 1500);
                                                                                                }
                                                                                            }">
                    <div class="flex items-stretch w-full">
                        @empty($manualSetupKey)
                            <div class="flex items-center justify-center w-full p-3 bg-base-200">
                                <span class="loading loading-spinner loading-sm"></span>
                            </div>
                        @else
                            <div class="join w-full">
                                <div class="flex-1">
                                    <label class="input validator join-item w-full">
                                        <input x-ref="setupKey" type="text" readonly value="{{ $manualSetupKey }}" required />
                                    </label>
                                </div>
                                <button type="button" @click="copy()"
                                    class="px-3 transition-colors border-l cursor-pointer join-item btn btn-outline border-base-content/20">
                                    <flux:icon.document-duplicate x-show="!copied" variant="outline" />
                                    <flux:icon.check x-show="copied" variant="solid" class="text-primary" />
                                </button>
                            </div>
                        @endempty
                    </div>
                </div>
            </div>
        @endif
    </div>
</dialog>