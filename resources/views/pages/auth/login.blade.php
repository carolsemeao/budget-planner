<x-layouts::auth :title="__('Log in')">
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('Log in to your account')" :description="__('Enter your email and password below to log in')" />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        {{-- <x-passkey-verify /> --}}

        <form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-6">
            @csrf

            <!-- Email Address -->
            <fieldset class="fieldset">
                <label class="label" for="email">{{ __('Email address') }}</label>
                <input
                    id="email"
                    type="email"
                    name="email"
                    class="input w-full validator"
                    placeholder="email@example.com"
                    value="{{ old('email') }}"
                    required
                    autocomplete="email"
                    autofocus
                    @error('email') aria-invalid="true" @enderror
                />
                <p class="validator-hint hidden">
                    @error('email')
                        {{ $message }}
                    @else
                        {{ __('Enter a valid email address.') }}
                    @enderror
                </p>
            </fieldset>

            <!-- Password -->
            <div class="relative">
                <fieldset class="fieldset">
                    <label class="label" for="password">{{ __('Password') }}</label>
                    <input
                        id="password"
                        type="password"
                        name="password"
                        class="input w-full"
                        placeholder="{{ __('Password') }}"
                        required
                        autocomplete="current-password"
                    />
                </fieldset>

                @if (Route::has('password.request'))
                    <a class="absolute -top-1 p-0 end-0 btn btn-sm btn-link" href="{{ route('password.request') }}"
                        wire:navigate>
                        {{ __('Forgot your password?') }}
                    </a>
                @endif
            </div>

            <!-- Remember Me -->
            <label class="label" for="remember">
                <input
                    id="remember"
                    type="checkbox"
                    class="checkbox"
                    name="remember"
                    value="1"
                    @checked(old('remember'))
                />
                {{ __('Remember me') }}
            </label>

            <div class="flex items-center justify-end">
                <button type="submit" class="btn btn-block btn-soft btn-primary" data-test="login-button">
                    {{ __('Log in') }}
                </button>
            </div>
        </form>

        <div class="space-x-1 text-sm text-center rtl:space-x-reverse text-base-content/70">
            <p>
                {{ __('Don\'t have an account?') }}
                <a class="btn btn-link p-0 ml-1" href="{{ route('register') }}" wire:navigate>
                    {{ __('Sign up') }}
                </a>
            </p>
        </div>
    </div>
</x-layouts::auth>
