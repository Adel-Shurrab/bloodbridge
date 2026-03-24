<?php

use App\Enums\UserRole;
use App\Models\User;
use Filament\Panel;

test('login screen can be rendered', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
});

test('users can authenticate using the login screen', function () {
    $user = User::factory()->create([
        'role' => UserRole::DONOR,
    ]);

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('filament.donor.pages.dashboard', absolute: false));
});

test('admins are redirected to the admin dashboard after login', function () {
    $user = User::factory()->create([
        'role' => UserRole::ADMIN,
    ]);

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticatedAs($user);
    $response->assertRedirect(route('filament.admin.pages.dashboard', absolute: false));
});

test('admins cannot access the organization panel', function () {
    $user = User::factory()->create([
        'role' => UserRole::ADMIN,
    ]);

    $organizationPanel = \Mockery::mock(Panel::class);
    $organizationPanel->shouldReceive('getId')->once()->andReturn('organization');

    expect($user->canAccessPanel($organizationPanel))->toBeFalse();
});

test('users can not authenticate with invalid password', function () {
    $user = User::factory()->create();

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();
});

test('users can logout', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/logout');

    $this->assertGuest();
    $response->assertRedirect('/');
});
