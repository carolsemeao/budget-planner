<?php

use Livewire\Component;

new class extends Component { }; ?>

<section class="mt-10 space-y-6">
    <div class="relative mb-5">
        <h3 class="font-title">{{ __('Delete account') }}</h3>
        <p>{{ __('Delete your account and all of its resources') }}</p>
    </div>

    <button class="btn btn-soft btn-error" data-test="delete-user-button"
        onclick="confirm_user_deletion.showModal()">{{ __('Delete account') }}</button>

    <livewire:pages::settings.delete-user-modal />
</section>