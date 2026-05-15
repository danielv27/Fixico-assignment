<?php

use App\Models\DamageReport;
use App\Models\FeatureFlag;

it('returns 410 when the flag is disabled', function (): void {
    FeatureFlag::factory()->disabled()->create(['name' => 'reports.bulk_actions']);

    $this->deleteJson('/api/reports/bulk', ['ids' => [1, 2, 3]])
        ->assertStatus(410)
        ->assertJson(['error' => 'feature_disabled', 'flag' => 'reports.bulk_actions']);
});

it('returns 410 when the flag does not exist', function (): void {
    $this->deleteJson('/api/reports/bulk', ['ids' => [1]])
        ->assertStatus(410);
});

it('executes the mutation when the flag is enabled', function (): void {
    FeatureFlag::factory()->create(['name' => 'reports.bulk_actions', 'enabled' => true]);
    $reports = DamageReport::factory()->count(2)->create();

    $this->deleteJson('/api/reports/bulk', ['ids' => $reports->pluck('id')->all()])
        ->assertOk()
        ->assertJson(['deleted' => 2]);

    expect(DamageReport::count())->toBe(0);
});

it('passes attributes through the context for attribute-gated flags', function (): void {
    FeatureFlag::factory()->create([
        'name' => 'reports.bulk_actions',
        'enabled' => true,
        'attribute_rules' => [['attribute' => 'role', 'values' => ['admin']]],
    ]);

    // Customer role → attribute rule fails → 410
    $this->deleteJson('/api/reports/bulk', [
        'ids' => [],
        'subject' => 'user-1',
        'attributes' => ['role' => 'customer'],
    ])->assertStatus(410);

    // Admin role → attribute rule passes → proceeds to controller
    $this->deleteJson('/api/reports/bulk', [
        'ids' => [],
        'subject' => 'user-1',
        'attributes' => ['role' => 'admin'],
    ])->assertStatus(422); // 422 = no IDs provided, past the flag gate
});
