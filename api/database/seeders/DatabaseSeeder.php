<?php

namespace Database\Seeders;

use App\Models\FeatureFlag;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
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
                'description' => 'Enables the bulk-delete toolbar on the reports list. Only visible to admin users.',
                'enabled' => true,
                'attribute_rules' => [['attribute' => 'role', 'values' => ['admin']]],
                'rollout_percentage' => null,
            ],
        );

        FeatureFlag::query()->updateOrCreate(
            ['name' => 'form.description_first'],
            [
                'description' => 'Reorders the report form to show the damage description field first. Rolled out to 50 % of NL users.',
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
            ['name' => 'reports.repair_timeline'],
            [
                'description' => 'Repair-status timeline for report detail pages. Scheduled to roll out to 20 % starting next week.',
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
    }
}
