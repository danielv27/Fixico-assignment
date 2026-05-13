<?php

use App\DamageReports\ReportStatus;
use App\Models\DamageReport;

it('lists damage reports newest first', function (): void {
    $older = DamageReport::factory()->create(['created_at' => now()->subDay()]);
    $newer = DamageReport::factory()->create();

    $response = $this->getJson('/reports');

    $response->assertOk()
        ->assertJsonCount(2, 'data')
        ->assertJsonPath('data.0.id', $newer->id)
        ->assertJsonPath('data.1.id', $older->id);
});

it('creates a damage report with sensible defaults', function (): void {
    $payload = [
        'vehicle_make' => 'Volkswagen',
        'vehicle_model' => 'Golf',
        'license_plate' => 'AB-123-CD',
        'description' => 'Front bumper scratched in the supermarket parking lot.',
    ];

    $response = $this->postJson('/reports', $payload);

    $response->assertCreated()
        ->assertJsonPath('data.vehicle_make', 'Volkswagen')
        ->assertJsonPath('data.status', ReportStatus::Draft->value);

    expect(DamageReport::query()->count())->toBe(1);
});

it('validates required fields on create', function (): void {
    $this->postJson('/reports', [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['vehicle_make', 'vehicle_model', 'license_plate', 'description']);
});

it('rejects an unknown status on create', function (): void {
    $this->postJson('/reports', [
        'vehicle_make' => 'BMW',
        'vehicle_model' => 'M3',
        'license_plate' => 'XX-000-XX',
        'description' => 'whatever',
        'status' => 'not-a-real-status',
    ])->assertUnprocessable()->assertJsonValidationErrors(['status']);
});

it('shows a single report', function (): void {
    $report = DamageReport::factory()->create();

    $this->getJson("/reports/{$report->id}")
        ->assertOk()
        ->assertJsonPath('data.id', $report->id);
});

it('updates a report and persists changes', function (): void {
    $report = DamageReport::factory()->create(['status' => ReportStatus::Draft]);

    $this->patchJson("/reports/{$report->id}", [
        'description' => 'Updated description.',
        'status' => ReportStatus::Submitted->value,
    ])->assertOk()
        ->assertJsonPath('data.description', 'Updated description.')
        ->assertJsonPath('data.status', ReportStatus::Submitted->value);

    expect($report->fresh()->status)->toBe(ReportStatus::Submitted);
});

it('returns 404 for a missing report', function (): void {
    $this->getJson('/reports/999')->assertNotFound();
});
