<x-layouts::auth :title="__('Register')">
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('Create an account')" :description="__('Enter your details below to create your account')" />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('register.store') }}" class="flex flex-col gap-6">
            @csrf
            <!-- Name -->
            <fieldset class="fieldset">
                <label class="label" for="name">{{ __('Name') }}</label>
                <input type="text" name="name" class="input w-full" placeholder="{{ __('Full name') }}" required
                    autocomplete="name" autofocus :value="old('name')" />
            </fieldset>
            {{--
            <flux:input name="name" :label="__('Name')" :value="old('name')" type="text" required autofocus
                autocomplete="name" :placeholder="__('Full name')" /> --}}

            <!-- Email Address -->
            <fieldset class="fieldset">
                <label class="label" for="email">{{ __('Email address') }}</label>
                <input type="email" name="email" class="input w-full" placeholder="email@example.com" required
                    autocomplete="email" :value="old('email')" />
            </fieldset>
            {{--
            <flux:input name="email" :label="__('Email address')" :value="old('email')" type="email" required
                autocomplete="email" placeholder="email@example.com" /> --}}

            <!-- Password -->
            <fieldset class="fieldset">
                <label class="label" for="password">{{ __('Password') }}</label>
                <input type="password" name="password" class="input w-full" placeholder="{{ __('Password') }}" required
                    autocomplete="new-password"
                    passwordrules="{{ \Illuminate\Validation\Rules\Password::defaults()->toPasswordRulesString() }}"
                    viewable />
            </fieldset>
            {{--
            <flux:input name="password" :label="__('Password')" type="password" required autocomplete="new-password"
                :placeholder="__('Password')"
                passwordrules="{{ \Illuminate\Validation\Rules\Password::defaults()->toPasswordRulesString() }}"
                viewable /> --}}

            <!-- Confirm Password -->
            <fieldset class="fieldset">
                <label class="label" for="password_confirmation">{{ __('Confirm password') }}</label>
                <input type="password" name="password_confirmation" class="input w-full"
                    placeholder="{{ __('Confirm password') }}" required autocomplete="new-password"
                    passwordrules="{{ \Illuminate\Validation\Rules\Password::defaults()->toPasswordRulesString() }}"
                    viewable />
            </fieldset>
            {{--
            <flux:input name="password_confirmation" :label="__('Confirm password')" type="password" required
                autocomplete="new-password" :placeholder="__('Confirm password')"
                passwordrules="{{ \Illuminate\Validation\Rules\Password::defaults()->toPasswordRulesString() }}"
                viewable /> --}}

            <div class="flex items-center justify-end">
                <button type="submit" class="btn btn-block btn-soft btn-primary" data-test="register-user-button">
                    {{ __('Create account') }}
                </button>
                {{-- <flux:button type="submit" variant="primary" class="w-full" data-test="register-user-button">
                    {{ __('Create account') }}
                </flux:button> --}}
            </div>
        </form>

        <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-base-content/70">
            <p>
                {{ __('Already have an account?') }}
                <a class="btn btn-link p-0 ml-1" href="{{ route('login') }}" wire:navigate>
                    {{ __('Log in') }}
                </a>
            </p>
        </div>
    </div>
</x-layouts::auth>