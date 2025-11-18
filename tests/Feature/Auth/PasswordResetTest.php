<?php

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Notification;

test('reset password link screen can be rendered', function () {
    $response = $this->get(route('password.request'));

    $response->assertStatus(200);
});

test('reset password link can be requested', function () {
    $this->markTestSkipped('Email sending is not configured.');
});

test('reset password screen can be rendered', function () {
    $this->markTestSkipped('Email sending is not configured.');
});

test('password can be reset with valid token', function () {
    $this->markTestSkipped('Email sending is not configured.');
});