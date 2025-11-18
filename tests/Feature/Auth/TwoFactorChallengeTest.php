<?php

use App\Models\User;
use Laravel\Fortify\Features;

test('two factor challenge redirects to login when not authenticated', function () {
    $this->markTestSkipped('Two-factor authentication is not configured.');
});

test('two factor challenge can be rendered', function () {
    $this->markTestSkipped('Two-factor authentication is not configured.');
});