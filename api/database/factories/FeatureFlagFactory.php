<?php

namespace Database\Factories;

use App\Models\FeatureFlag;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FeatureFlag>
 */
class FeatureFlagFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => 'demo.'.fake()->unique()->slug(2),
            'description' => fake()->sentence(),
            'enabled' => true,
        ];
    }

    public function disabled(): self
    {
        return $this->state(['enabled' => false]);
    }
}
