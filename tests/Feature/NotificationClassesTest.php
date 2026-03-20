<?php

use App\Models\BloodRequest;
use App\Models\DonorResponse;
use App\Models\User;
use App\Notifications\BloodRequestMatchNotification;
use App\Notifications\DonorResponseNotification;
use App\Notifications\ResponseNotNeededNotification;
use App\Notifications\DonorIneligibilityNotification;
use App\Notifications\SystemAnnouncement;

it('blood request match notification returns database and broadcast channels', function () {
    $user = User::factory()->create();
    $bloodRequest = BloodRequest::factory()->create();
    
    $notification = new BloodRequestMatchNotification($bloodRequest, 5.0);
    $channels = $notification->via($user);
    
    expect($channels)->toContain('database')
        ->and($channels)->toContain('broadcast');
});

it('blood request match notification has database message', function () {
    $user = User::factory()->create();
    $bloodRequest = BloodRequest::factory()->create();
    
    $notification = new BloodRequestMatchNotification($bloodRequest, 5.0);
    $databaseMessage = $notification->toDatabase($user);
    
    expect($databaseMessage)->toBeArray();
});

it('donor response notification returns database and broadcast', function () {
    $user = User::factory()->create();
    $donor = User::factory()->create();
    $response = DonorResponse::factory()->create(['user_id' => $donor->id]);
    
    $notification = new DonorResponseNotification($response);
    $channels = $notification->via($user);
    
    expect($channels)->toContain('database')
        ->and($channels)->toContain('broadcast');
});

it('response not needed notification includes required channels', function () {
    $user = User::factory()->create();
    $response = DonorResponse::factory()->create();
    
    $notification = new ResponseNotNeededNotification($response);
    $channels = $notification->via($user);
    
    expect($channels)->toContain('database')
        ->and($channels)->toContain('broadcast');
});

it('donor ineligibility notification includes channels', function () {
    $user = User::factory()->create();
    $reason = 'Test ineligibility reason';
    
    $notification = new DonorIneligibilityNotification($reason);
    $channels = $notification->via($user);
    
    expect($channels)->toContain('database')
        ->and($channels)->toContain('broadcast');
});

it('system announcement includes database and broadcast', function () {
    $user = User::factory()->create();
    
    $notification = new SystemAnnouncement('Test announcement');
    $channels = $notification->via($user);
    
    expect($channels)->toContain('database')
        ->and($channels)->toContain('broadcast');
});

it('blood request match notification has filament notification', function () {
    $user = User::factory()->create();
    $bloodRequest = BloodRequest::factory()->create();
    
    $notification = new BloodRequestMatchNotification($bloodRequest, 5.0);
    
    expect(method_exists($notification, 'buildFilamentNotification'))->toBeTrue();
});

it('all notifications have to_database method', function () {
    $user = User::factory()->create();
    $bloodRequest = BloodRequest::factory()->create();
    $response = DonorResponse::factory()->create();
    
    $notifications = [
        new BloodRequestMatchNotification($bloodRequest, 5.0),
        new DonorResponseNotification($response),
        new ResponseNotNeededNotification($response),
        new DonorIneligibilityNotification('Test reason'),
        new SystemAnnouncement('Test'),
    ];
    
    foreach ($notifications as $notification) {
        expect(method_exists($notification, 'toDatabase'))->toBeTrue();
        $result = $notification->toDatabase($user);
        expect($result)->toBeArray();
    }
});

it('all notifications have to_broadcast method', function () {
    $user = User::factory()->create();
    $bloodRequest = BloodRequest::factory()->create();
    $response = DonorResponse::factory()->create();
    
    $notifications = [
        new BloodRequestMatchNotification($bloodRequest, 5.0),
        new DonorResponseNotification($response),
        new ResponseNotNeededNotification($response),
        new DonorIneligibilityNotification('Test reason'),
        new SystemAnnouncement('Test'),
    ];
    
    foreach ($notifications as $notification) {
        expect(method_exists($notification, 'toBroadcast'))->toBeTrue();
        $result = $notification->toBroadcast($user);
        expect($result)->toBeArray();
    }
});

it('notification database content contains title', function () {
    $user = User::factory()->create();
    $bloodRequest = BloodRequest::factory()->create();
    
    $notification = new BloodRequestMatchNotification($bloodRequest, 5.0);
    $databaseMessage = $notification->toDatabase($user);
    
    expect($databaseMessage)->toHaveKey('title');
});

it('broadcast notification contains required fields', function () {
    $user = User::factory()->create();
    $bloodRequest = BloodRequest::factory()->create();
    
    $notification = new BloodRequestMatchNotification($bloodRequest, 5.0);
    $broadcastMessage = $notification->toBroadcast($user);
    
    expect($broadcastMessage)->toBeArray();
});
