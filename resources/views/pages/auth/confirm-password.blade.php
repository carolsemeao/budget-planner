<x-layouts::auth :title="__('Confirm password')">
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('Confirm password')" :description="__('This is a secure area of the application. Please confirm your password before continuing.')" />

        <x-auth-session-status class="text-center" :status="session('status')" />

        <x-passkey-verify options-route="passkey.confirm-options" submit-route="passkey.confirm" :label="__('Confirm with passkey')" :loading-label="__('Confirming...')" :separator="__('Or confirm with password')" />

        <form method="POST" action="{{ route('password.confirm.store') }}" class="flex flex-col gap-6">
            @csrf

            <fieldset class="fieldset">
                <label class="label" for="password">{{ __('Password') }}</label>
                <input id="password" type="password" name="password" class="input w-full"
                    placeholder="{{ __('Password') }}" required autocomplete="current-password" />
            </fieldset>

            <button type="submit" class="btn btn-block btn-soft btn-primary" data-test="confirm-password-button">
                {{ __('Confirm') }}
            </button>
        </form>
    </div>
</x-layouts::auth>