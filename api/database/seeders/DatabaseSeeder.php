<?php

namespace Database\Seeders;

use App\DamageReports\ReportStatus;
use App\Models\DamageReport;
use App\Models\FeatureFlag;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Feature flags

        FeatureFlag::query()->updateOrCreate(
            ['name' => 'demo.banner'],
            [
                'description' => 'Shows a dismissable info banner linking to the flag admin. Demonstrates the basic on/off mechanism.',
                'enabled' => true,
                'attribute_rules' => [],
                'rollout_percentage' => null,
            ],
        );

        FeatureFlag::query()->updateOrCreate(
            ['name' => 'reports.bulk_actions'],
            [
                'description' => 'Enables the bulk-delete toolbar on the reports list. Only visible to admin-role viewers.',
                'enabled' => true,
                'attribute_rules' => [['attribute' => 'role', 'values' => ['admin']]],
                'rollout_percentage' => null,
            ],
        );

        FeatureFlag::query()->updateOrCreate(
            ['name' => 'report.new_form_layout'],
            [
                'description' => 'Rolls out a redesigned new-report page to 50 % of NL users.',
                'enabled' => true,
                'attribute_rules' => [['attribute' => 'country', 'values' => ['NL']]],
                'rollout_percentage' => 50,
            ],
        );

        FeatureFlag::query()->updateOrCreate(
            ['name' => 'reports.photo_attachments'],
            [
                'description' => 'Shows a photo documentation section on the report detail page. 25 % rollout.',
                'enabled' => true,
                'attribute_rules' => [],
                'rollout_percentage' => 25,
            ],
        );

        FeatureFlag::query()->updateOrCreate(
            ['name' => 'dashboard.v2'],
            [
                'description' => 'Redesigned dashboard with live repair-status timeline. Scheduled to roll out to 20 % starting next week.',
                'enabled' => true,
                'attribute_rules' => [],
                'rollout_percentage' => 20,
                'starts_at' => now()->addDays(7),
                'ends_at' => null,
            ],
        );

        FeatureFlag::query()->updateOrCreate(
            ['name' => 'promo.winter_2024'],
            [
                'description' => '15 % discount on repair bookings for the Winter 2024 campaign.',
                'enabled' => true,
                'attribute_rules' => [],
                'rollout_percentage' => null,
                'starts_at' => Carbon::parse('2024-12-01'),
                'ends_at' => Carbon::parse('2024-12-31'),
            ],
        );

        // Demo damage reports

        if (DamageReport::query()->count() === 0) {
            DamageReport::factory()->create([
                'vehicle_make' => 'Volkswagen',
                'vehicle_model' => 'Golf',
                'license_plate' => 'AB-123-CD',
                'description' => 'Front bumper scratched in the supermarket parking lot.',
                'status' => ReportStatus::Submitted,
            ]);

            DamageReport::factory()->create([
                'vehicle_make' => 'Toyota',
                'vehicle_model' => 'Yaris',
                'license_plate' => 'GH-456-IJ',
                'description' => 'Hailstorm damage on the roof and hood.',
                'status' => ReportStatus::Approved,
            ]);

            DamageReport::factory()->create([
                'vehicle_make' => 'BMW',
                'vehicle_model' => '3 Series',
                'license_plate' => 'KL-789-MN',
                'description' => 'Side mirror knocked off in a narrow street.',
                'status' => ReportStatus::Draft,
            ]);
        }
    }
}
