<?php

use App\Models\FeatureFlag;

it('returns a batch of flag decisions', function (): void {
    FeatureFlag::factory()->create(['name' => 'flag.a', 'enabled' => true]);
    FeatureFlag::factory()->create(['name' => 'flag.b', 'enabled' => false]);

    $response = $this->postJson('/api/feature_flags/evaluate', [
        'user_id' => 'user-1',
    ]);

    $response->assertOk()->assertExactJson([
        'flags' => ['flag.a' => true, 'flag.b' => false],
    ]);
});

it('requires a user_id', function (): void {
    $this->postJson('/api/feature_flags/evaluate', [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['user_id']);
});

it('returns an empty map when no flags are defined', function (): void {
    $this->postJson('/api/feature_flags/evaluate', ['user_id' => 'user-1'])
        ->assertOk()
        ->assertExactJson(['flags' => []]);
});
