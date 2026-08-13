<x-layouts::auth :title="__('Two-factor authentication')">
    <div class="flex flex-col gap-6">
        <div class="relative w-full h-auto" x-cloak x-data="{
                showRecoveryInput: @js($errors->has('recovery_code')),
                code: '',
                recovery_code: '',
                focusOtp() {
                    this.$nextTick(() => this.$refs.otp?.querySelector('input')?.focus());
                },
                init() {
                    if (! this.showRecoveryInput) {
                        this.focusOtp();
                    }
                },
                toggleInput() {
                    this.showRecoveryInput = !this.showRecoveryInput;

                    this.code = '';
                    this.recovery_code = '';

                    $nextTick(() => {
                        this.showRecoveryInput
                            ? this.$refs.recovery_code?.focus()
                            : this.focusOtp();
                    });
                },
            }">
            <div x-show="!showRecoveryInput">
                <x-auth-header :title="__('Authentication code')" :description="__('Enter the authentication code provided by your authenticator application.')" />
            </div>

            <div x-show="showRecoveryInput">
                <x-auth-header :title="__('Recovery code')" :description="__('Please confirm access to your account by entering one of your emergency recovery codes.')" />
            </div>

            <form method="POST" action="{{ route('two-factor.login.store') }}">
                @csrf

                <div class="space-y-5 text-center">
                    <div x-show="!showRecoveryInput">
                        <div class="flex items-center justify-center my-5" x-ref="otp">
                            <label class="otp otp-lg mx-auto">
                                <span></span>
                                <span></span>
                                <span></span>
                                <span></span>
                                <span></span>
                                <span></span>
                                <input type="text" name="code" wire:model.live="code" autocomplete="one-time-code"
                                    inputmode="numeric" maxlength="6" pattern="[0-9]{6}" />
                            </label>
                            @error('code')
                                <p class="mt-2 text-sm text-error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div x-show="showRecoveryInput">
                        <div class="my-5">
                            <flux:input type="text" name="recovery_code" x-ref="recovery_code"
                                x-bind:required="showRecoveryInput" autocomplete="one-time-code"
                                x-model="recovery_code" />
                        </div>

                        @error('recovery_code')
                            <flux:text color="red">
                                {{ $message }}
                            </flux:text>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-block btn-soft btn-primary">
                        {{ __('Continue') }}
                    </button>
                </div>

                <div class="mt-5 space-x-0.5 text-sm leading-5 text-center text-base-content/70">
                    <p>
                        {{ __('or you can') }}
                        <span x-show="!showRecoveryInput" @click="toggleInput()"
                            class="btn btn-link p-0 ml-1">{{ __('login using a recovery code') }}</span>
                        <span x-show="showRecoveryInput" @click="toggleInput()"
                            class="btn btn-link p-0 ml-1">{{ __('login using an authentication code') }}</span>
                    </p>
                </div>
            </form>
        </div>
    </div>
</x-layouts::auth>