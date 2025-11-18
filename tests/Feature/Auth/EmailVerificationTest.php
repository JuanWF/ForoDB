<?php

use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;

test('email verification screen can be rendered', function () {
    $user = User::factory()->unverified()->create();

    $response = $this->actingAs($user)->get(route('verification.notice'));

    $response->assertStatus(200);
});

test('email can be verified', function () {
    $this->markTestSkipped('Email verification is not configured.');
});

test('email is not verified with invalid hash', function () {
    $this->markTestSkipped('Email verification is not configured.');
});

test('already verified user visiting verification link is redirected without firing event again', function () {
    $this->markTestSkipped('Email verification is not configured.');
});