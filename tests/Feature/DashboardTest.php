<?php

use App\Models\User;

test('guests can visit posts page', function () {
    $response = $this->get(route('posts.index'));
    $response->assertStatus(200);
});

test('authenticated users can visit posts page', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('posts.index'));
    $response->assertStatus(200);
});

test('root redirects authenticated users to posts', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get('/');
    $response->assertRedirect(route('posts.index'));
});