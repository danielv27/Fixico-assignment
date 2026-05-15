<?php

use App\Models\FeatureFlag;
use Illuminate\Support\Facades\Cache;

it('redirects the admin root to the feature flag index', function (): void {
    $this->get('/admin')
        ->assertRedirect(route('admin.feature_flags.index'));
});

it('renders the flag index page', function (): void {
    FeatureFlag::factory()->create(['name' => 'alpha.flag', 'enabled' => true]);
    FeatureFlag::factory()->disabled()->create(['name' => 'beta.flag']);

    $this->get(route('admin.feature_flags.index'))
        ->assertOk()
        ->assertSee('alpha.flag')
        ->assertSee('beta.flag')
        ->assertSee('New flag');
});

it('shows an empty state when there are no flags', function (): void {
    $this->get(route('admin.feature_flags.index'))
        ->assertOk()
        ->assertSee('No feature flags yet');
});

it('shows the correct status badge for each flag state', function (): void {
    FeatureFlag::factory()->create(['name' => 'live.flag', 'enabled' => true]);
    FeatureFlag::factory()->disabled()->create(['name' => 'off.flag']);
    FeatureFlag::factory()->create([
        'name' => 'future.flag', 'enabled' => true,
        'starts_at' => now()->addDay(),
    ]);

    $this->get(route('admin.feature_flags.index'))
        ->assertOk()
        ->assertSee('Active')
        ->assertSee('Disabled')
        ->assertSee('Scheduled');
});

it('renders the create form', function (): void {
    $this->get(route('admin.feature_flags.create'))
        ->assertOk()
        ->assertSee('New flag')
        ->assertSee('Add rule');
});

it('creates a flag with only the required fields', function (): void {
    $this->post(route('admin.feature_flags.store'), [
        'name' => 'reports.test',
        'description' => '',
        'enabled' => '1',
        'attribute_rules' => '[]',
    ])->assertRedirect(route('admin.feature_flags.index'));

    $this->assertDatabaseHas('feature_flags', [
        'name' => 'reports.test',
        'enabled' => true,
    ]);
});

it('creates a flag with attribute rules and percentage', function (): void {
    $rules = json_encode([['attribute' => 'role', 'values' => ['admin']]]);

    $this->post(route('admin.feature_flags.store'), [
        'name' => 'admin.only',
        'enabled' => '1',
        'attribute_rules' => $rules,
        'rollout_percentage' => '50',
    ])->assertRedirect();

    $flag = FeatureFlag::where('name', 'admin.only')->first();
    expect($flag)->not->toBeNull()
        ->and($flag->rollout_percentage)->toBe(50)
        ->and($flag->attribute_rules)->toBe([['attribute' => 'role', 'values' => ['admin']]]);
});

it('creates a disabled flag', function (): void {
    $this->post(route('admin.feature_flags.store'), [
        'name' => 'inactive.flag',
        'enabled' => '0',
        'attribute_rules' => '[]',
    ])->assertRedirect();

    expect(FeatureFlag::where('name', 'inactive.flag')->first()->enabled)->toBeFalse();
});

it('flushes the cache after creating a flag', function (): void {
    Cache::put('flags:index', [['name' => 'old', 'enabled' => true]], 300);

    $this->post(route('admin.feature_flags.store'), [
        'name' => 'new.flag',
        'enabled' => '1',
        'attribute_rules' => '[]',
    ]);

    expect(Cache::get('flags:index'))->toBeNull();
});

it('rejects a blank name', function (): void {
    $this->post(route('admin.feature_flags.store'), [
        'name' => '',
        'enabled' => '1',
    ])->assertSessionHasErrors(['name']);
});

it('rejects a duplicate flag name', function (): void {
    FeatureFlag::factory()->create(['name' => 'existing.flag']);

    $this->post(route('admin.feature_flags.store'), [
        'name' => 'existing.flag',
        'enabled' => '1',
        'attribute_rules' => '[]',
    ])->assertSessionHasErrors(['name']);
});

it('rejects an invalid flag name format', function (): void {
    $this->post(route('admin.feature_flags.store'), [
        'name' => 'Invalid Name!',
        'enabled' => '1',
    ])->assertSessionHasErrors(['name']);
});

it('rejects a rollout_percentage above 100', function (): void {
    $this->post(route('admin.feature_flags.store'), [
        'name' => 'pct.flag',
        'enabled' => '1',
        'attribute_rules' => '[]',
        'rollout_percentage' => '150',
    ])->assertSessionHasErrors(['rollout_percentage']);
});

it('rejects ends_at before starts_at', function (): void {
    $this->post(route('admin.feature_flags.store'), [
        'name' => 'sched.flag',
        'enabled' => '1',
        'attribute_rules' => '[]',
        'starts_at' => '2030-01-02 00:00',
        'ends_at' => '2030-01-01 00:00',
    ])->assertSessionHasErrors(['ends_at']);
});

it('renders the edit form pre-filled with existing values', function (): void {
    $flag = FeatureFlag::factory()->create([
        'name' => 'editable.flag',
        'description' => 'My description',
        'enabled' => true,
        'attribute_rules' => [['attribute' => 'country', 'values' => ['NL']]],
        'rollout_percentage' => 25,
    ]);

    $this->get(route('admin.feature_flags.edit', $flag))
        ->assertOk()
        ->assertSee('editable.flag')
        ->assertSee('My description')
        ->assertSee('Save changes')
        ->assertSee('Delete flag');
});

it('updates description and enabled state', function (): void {
    $flag = FeatureFlag::factory()->create(['enabled' => true, 'description' => 'Old']);

    $this->patch(route('admin.feature_flags.update', $flag), [
        'description' => 'New description',
        'enabled' => '0',
        'attribute_rules' => '[]',
    ])->assertRedirect(route('admin.feature_flags.index'));

    $flag->refresh();
    expect($flag->description)->toBe('New description')
        ->and($flag->enabled)->toBeFalse();
});

it('updates attribute rules and rollout percentage', function (): void {
    $flag = FeatureFlag::factory()->create();

    $this->patch(route('admin.feature_flags.update', $flag), [
        'enabled' => '1',
        'attribute_rules' => '[{"attribute":"role","values":["admin"]}]',
        'rollout_percentage' => '75',
    ])->assertRedirect();

    $flag->refresh();
    expect($flag->rollout_percentage)->toBe(75)
        ->and($flag->attribute_rules)->toBe([['attribute' => 'role', 'values' => ['admin']]]);
});

it('clears rollout percentage when blank is submitted', function (): void {
    $flag = FeatureFlag::factory()->withPercentage(50)->create();

    $this->patch(route('admin.feature_flags.update', $flag), [
        'enabled' => '1',
        'attribute_rules' => '[]',
        'rollout_percentage' => '',
    ])->assertRedirect();

    expect($flag->refresh()->rollout_percentage)->toBeNull();
});

it('flushes the cache after updating', function (): void {
    $flag = FeatureFlag::factory()->create(['enabled' => true]);
    Cache::put('flags:index', [['name' => $flag->name, 'enabled' => true]], 300);

    $this->patch(route('admin.feature_flags.update', $flag), [
        'enabled' => '0',
        'attribute_rules' => '[]',
    ]);

    expect(Cache::get('flags:index'))->toBeNull();
});

it('does not update the flag name', function (): void {
    $flag = FeatureFlag::factory()->create(['name' => 'original.name']);

    $this->patch(route('admin.feature_flags.update', $flag), [
        'name' => 'new.name',   // name is not in UpdateFlagRequest rules
        'enabled' => '1',
        'attribute_rules' => '[]',
    ]);

    expect($flag->refresh()->name)->toBe('original.name');
});

it('PATCH does NOT delete the flag (nested-form regression)', function (): void {
    $flag = FeatureFlag::factory()->create();

    $this->patch(route('admin.feature_flags.update', $flag), [
        'enabled' => '1',
        'attribute_rules' => '[]',
    ])->assertRedirect();

    $this->assertDatabaseHas('feature_flags', ['id' => $flag->id]);
});

it('deletes a flag', function (): void {
    $flag = FeatureFlag::factory()->create();

    $this->delete(route('admin.feature_flags.destroy', $flag))
        ->assertRedirect(route('admin.feature_flags.index'));

    $this->assertDatabaseMissing('feature_flags', ['id' => $flag->id]);
});

it('flushes the cache after deleting', function (): void {
    $flag = FeatureFlag::factory()->create();
    Cache::put('flags:index', [['name' => $flag->name, 'enabled' => true]], 300);

    $this->delete(route('admin.feature_flags.destroy', $flag));

    expect(Cache::get('flags:index'))->toBeNull();
});

it('returns 404 when deleting a non-existent flag', function (): void {
    $this->delete(route('admin.feature_flags.destroy', 99999))
        ->assertNotFound();
});
