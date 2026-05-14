<?php

use App\Models\FeatureFlag;
use Illuminate\Support\Facades\Cache;

it('lists flags ordered by name', function (): void {
    FeatureFlag::factory()->create(['name' => 'z.flag']);
    FeatureFlag::factory()->create(['name' => 'a.flag']);

    $this->getJson('/api/admin/flags')
        ->assertOk()
        ->assertJsonPath('data.0.name', 'a.flag')
        ->assertJsonPath('data.1.name', 'z.flag');
});

it('creates a flag', function (): void {
    $this->postJson('/api/admin/flags', [
        'name' => 'reports.bulk_delete',
        'description' => 'Enables bulk deletion.',
        'enabled' => false,
    ])->assertCreated()
        ->assertJsonPath('data.name', 'reports.bulk_delete')
        ->assertJsonPath('data.enabled', false);

    $this->assertDatabaseHas('feature_flags', ['name' => 'reports.bulk_delete']);
});

it('rejects a duplicate flag name', function (): void {
    FeatureFlag::factory()->create(['name' => 'reports.bulk_delete']);

    $this->postJson('/api/admin/flags', [
        'name' => 'reports.bulk_delete',
        'enabled' => true,
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['name']);
});

it('rejects an invalid flag name format', function (): void {
    $this->postJson('/api/admin/flags', [
        'name' => 'Has Spaces!',
        'enabled' => true,
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['name']);
});

it('shows a single flag', function (): void {
    $flag = FeatureFlag::factory()->create(['name' => 'demo.banner']);

    $this->getJson("/api/admin/flags/{$flag->id}")
        ->assertOk()
        ->assertJsonPath('data.name', 'demo.banner');
});

it('updates a flag', function (): void {
    $flag = FeatureFlag::factory()->create(['enabled' => true]);

    $this->patchJson("/api/admin/flags/{$flag->id}", ['enabled' => false, 'description' => 'Off for now.'])
        ->assertOk()
        ->assertJsonPath('data.enabled', false);

    $this->assertDatabaseHas('feature_flags', ['id' => $flag->id, 'enabled' => false]);
});

it('busts the flag cache on update', function (): void {
    $flag = FeatureFlag::factory()->create(['enabled' => true]);
    Cache::put('flags:index:v2', [['name' => $flag->name, 'enabled' => true]], 300);

    $this->patchJson("/api/admin/flags/{$flag->id}", ['enabled' => false]);

    expect(Cache::get('flags:index:v2'))->toBeNull();
});

it('deletes a flag', function (): void {
    $flag = FeatureFlag::factory()->create();

    $this->deleteJson("/api/admin/flags/{$flag->id}")->assertNoContent();

    $this->assertDatabaseMissing('feature_flags', ['id' => $flag->id]);
});

it('busts the flag cache on delete', function (): void {
    $flag = FeatureFlag::factory()->create();
    Cache::put('flags:index:v2', [['name' => $flag->name, 'enabled' => true]], 300);

    $this->deleteJson("/api/admin/flags/{$flag->id}");

    expect(Cache::get('flags:index:v2'))->toBeNull();
});
