<?php

use App\Models\FeatureFlag;

it('renders the admin overview', function (): void {
    FeatureFlag::factory()->create(['name' => 'demo.banner', 'enabled' => true]);

    $this->get(route('admin.dashboard'))
        ->assertOk()
        ->assertSee('Admin overview')
        ->assertSee('Feature flag inventory')
        ->assertSee('demo.banner')
        ->assertSee(route('admin.feature_flags.index'), false);
});

it('shows operational columns for each feature flag', function (): void {
    FeatureFlag::factory()->create([
        'name' => 'reports.bulk_actions',
        'enabled' => true,
        'attribute_rules' => [['attribute' => 'role', 'values' => ['admin']]],
    ]);
    FeatureFlag::factory()->withPercentage(25)->create(['name' => 'reports.photo_attachments']);

    $this->get(route('admin.dashboard'))
        ->assertOk()
        ->assertSee('Targeting')
        ->assertSee('Rollout')
        ->assertSee('Schedule')
        ->assertSee('role: admin')
        ->assertSee('reports.photo_attachments')
        ->assertSee('25%')
        ->assertDontSee('View flags')
        ->assertDontSee('New flag');
});

it('shows feature flag status totals', function (): void {
    FeatureFlag::factory()->create(['enabled' => true]);
    FeatureFlag::factory()->disabled()->create();
    FeatureFlag::factory()->create(['enabled' => true, 'starts_at' => now()->addDay()]);
    FeatureFlag::factory()->create(['enabled' => true, 'ends_at' => now()->subDay()]);

    $this->get(route('admin.dashboard'))
        ->assertOk()
        ->assertSeeInOrder(['Total', '4'])
        ->assertSeeInOrder(['Active', '1'])
        ->assertSeeInOrder(['Disabled', '1'])
        ->assertSeeInOrder(['Scheduled', '1'])
        ->assertSeeInOrder(['Expired', '1']);
});
