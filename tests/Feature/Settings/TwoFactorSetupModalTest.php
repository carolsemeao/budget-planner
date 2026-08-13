<?php

use App\Models\User;
use Laravel\Fortify\Features;
use Laravel\Fortify\Fortify;
use Livewire\Livewire;
use PragmaRX\Google2FA\Google2FA;

beforeEach(function () {
    $this->skipUnlessFortifyHas(Features::twoFactorAuthentication());

    Features::twoFactorAuthentication([
        'confirm' => true,
        'confirmPassword' => true,
    ]);
});

test('starting two factor setup loads qr data for the modal', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    Livewire::test('pages::settings.two-factor-setup-modal', [
        'requiresConfirmation' => true,
    ])
        ->dispatch('start-two-factor-setup')
        ->assertSet('qrCodeSvg', fn (string $svg) => $svg !== '')
        ->assertSet('manualSetupKey', fn (string $key) => $key !== '')
        ->assertSee('Enable two-factor authentication');

    expect($user->fresh()->two_factor_secret)->not->toBeNull();
});

test('security page enable button does not open the modal before livewire finishes', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->withSession(['auth.password_confirmed_at' => time()])
        ->get(route('security.edit'))
        ->assertOk()
        ->assertSee('wire:click="$dispatch(\'start-two-factor-setup\')"', false)
        ->assertDontSee('onclick="two_factor_setup_modal.showModal()"', false);
});

test('confirm two factor requires a six digit code', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    Livewire::test('pages::settings.two-factor-setup-modal', [
        'requiresConfirmation' => true,
    ])
        ->dispatch('start-two-factor-setup')
        ->call('showVerificationIfNecessary')
        ->assertSet('showVerificationStep', true)
        ->set('code', '123')
        ->call('confirmTwoFactor')
        ->assertHasErrors(['code']);
});

test('confirm two factor accepts a valid authenticator code', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $component = Livewire::test('pages::settings.two-factor-setup-modal', [
        'requiresConfirmation' => true,
    ])->dispatch('start-two-factor-setup');

    $secret = Fortify::currentEncrypter()
        ->decrypt($user->fresh()->two_factor_secret);

    $otp = app(Google2FA::class)->getCurrentOtp($secret);

    $component
        ->call('showVerificationIfNecessary')
        ->set('code', $otp)
        ->call('confirmTwoFactor')
        ->assertHasNoErrors()
        ->assertDispatched('two-factor-enabled');

    expect($user->fresh()->two_factor_confirmed_at)->not->toBeNull();
});
