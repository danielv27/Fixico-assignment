<?php

namespace Database\Seeders;

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
    }
}
