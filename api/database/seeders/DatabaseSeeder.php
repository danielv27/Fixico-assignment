<?php

namespace Database\Seeders;

use App\DamageReports\ReportStatus;
use App\Models\DamageReport;
use App\Models\FeatureFlag;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        FeatureFlag::query()->updateOrCreate(
            ['name' => 'demo.banner'],
            [
                'description' => 'Slice 1 sanity check — toggles the demo banner on the landing page.',
                'enabled' => true,
            ],
        );

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
        }
    }
}
