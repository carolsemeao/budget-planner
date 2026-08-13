<?php

use App\Concerns\PasswordValidationRules;
use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

new class extends Component {
    use PasswordValidationRules;

    public string $password = '';

    /**
     * Delete the currently authenticated user.
     */
    public function deleteUser(Logout $logout): void
    {
        $this->validate([
            'password' => $this->currentPasswordRules(),
        ]);

        tap(Auth::user(), $logout(...))->delete();

        $this->redirect('/', navigate: true);
    }
}; ?>

<dialog id="confirm_user_deletion" focusable class="modal modal-bottom sm:modal-middle">
    <div class="modal-box">
        <h3 class="font-title text-">{{ __('Are you sure you want to delete your account?') }}</h3>
        <p>
            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
        </p>
        <div class="modal-action">
            <form method="POST" wire:submit="deleteUser" class="space-y-6 w-full">
                <input type="password" placeholder="{{ __('Password') }}" class="input w-full" wire:model="password"
                    viewable />

                <div class="join join-vertical lg:join-horizontal justify-end w-full">
                    <button type="button" class="btn btn-soft join-item"
                        onclick="confirm_user_deletion.close()">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-soft btn-error join-item"
                        data-test="confirm-delete-user-button">{{ __('Delete account') }}</button>
                </div>
            </form>
        </div>
    </div>
</dialog>