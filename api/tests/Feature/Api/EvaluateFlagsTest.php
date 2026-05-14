<?php

use App\Models\FeatureFlag;

it('returns a batch of flag decisions', function (): void {
    FeatureFlag::factory()->create(['name' => 'flag.a', 'enabled' => true]);
    FeatureFlag::factory()->create(['name' => 'flag.b', 'enabled' => false]);

    $response = $this->postJson('/api/flags/evaluate', [
        'subject' => 'subject-1',
    ]);

    $response->assertOk()->assertExactJson([
        'flags' => ['flag.a' => true, 'flag.b' => false],
    ]);
});

it('requires a subject', function (): void {
    $this->postJson('/api/flags/evaluate', [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['subject']);
});

it('returns an empty map when no flags are defined', function (): void {
    $this->postJson('/api/flags/evaluate', ['subject' => 'subject-1'])
        ->assertOk()
        ->assertExactJson(['flags' => []]);
});
