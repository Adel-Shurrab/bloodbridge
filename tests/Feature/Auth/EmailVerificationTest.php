<?php

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;

// ---------------------------------------------------------------------------
// Basic verification flow
// ---------------------------------------------------------------------------

test('email verification screen can be rendered', function () {
    $user = User::factory()->unverified()->create();

    // The verification notice route is inside the locale group (/en/verify-email).
    // Locale-detection middleware causes redirects in tests with no session locale,
    // so we bypass it and test the controller response directly.
    $this->actingAs($user)
        ->withoutMiddleware([
            \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter::class,
            \Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect::class,
        ])
        ->get(route('verification.notice'))
        ->assertStatus(200);
});

test('email can be verified', function () {
    $user = User::factory()->unverified()->create(['role' => UserRole::DONOR]);

    Event::fake();

    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        ['id' => $user->id, 'hash' => sha1($user->email)]
    );

    $response = $this->actingAs($user)->get($verificationUrl);

    Event::assertDispatched(Verified::class);
    expect($user->fresh()->hasVerifiedEmail())->toBeTrue();
    $response->assertRedirect();
    expect($response->headers->get('Location'))->toContain('?verified=1');
});

test('email is not verified with invalid hash', function () {
    $user = User::factory()->unverified()->create();

    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        ['id' => $user->id, 'hash' => sha1('wrong-email')]
    );

    $this->actingAs($user)->get($verificationUrl);

    expect($user->fresh()->hasVerifiedEmail())->toBeFalse();
});

test('expired verification link returns 403', function () {
    $user = User::factory()->unverified()->create();

    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->subMinute(),
        ['id' => $user->id, 'hash' => sha1($user->email)]
    );

    $this->actingAs($user)->get($verificationUrl)->assertStatus(403);
});

test('already verified user is redirected to dashboard', function () {
    $user = User::factory()->create(['role' => UserRole::DONOR]);

    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        ['id' => $user->id, 'hash' => sha1($user->email)]
    );

    $this->actingAs($user)->get($verificationUrl)->assertRedirect();
});

// ---------------------------------------------------------------------------
// Locale fix — verification.verify must have NO locale prefix so that the
// localizationRedirect middleware cannot change the URL and break the signature
// ---------------------------------------------------------------------------

test('verification.verify route has no locale prefix in its URI', function () {
    $route = Route::getRoutes()->getByName('verification.verify');

    expect($route)->not->toBeNull()
        ->and($route->uri())->toBe('verify-email/{id}/{hash}');
});

test('signed verification URL works without being redirected to a locale-prefixed URL', function () {
    $user = User::factory()->unverified()->create(['role' => UserRole::DONOR]);

    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        ['id' => $user->id, 'hash' => sha1($user->email)]
    );

    // URL must NOT contain a locale segment (/en/ or /ar/)
    expect($verificationUrl)->not->toContain('/en/verify-email')
        ->and($verificationUrl)->not->toContain('/ar/verify-email');

    // Signature must be valid — hitting the URL should not return 403
    $response = $this->actingAs($user)->get($verificationUrl);
    $response->assertStatus(302); // redirect (not 403 invalid signature)
});
