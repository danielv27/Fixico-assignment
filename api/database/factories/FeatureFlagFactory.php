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
            'attribute_rules' => [],
            'rollout_percentage' => null,
            'starts_at' => null,
            'ends_at' => null,
        ];
    }

    public function disabled(): self
    {
        return $this->state(['enabled' => false]);
    }

    public function forRole(string $role): self
    {
        return $this->state([
            'attribute_rules' => [['attribute' => 'role', 'values' => [$role]]],
        ]);
    }

    public function withPercentage(int $percentage): self
    {
        return $this->state(['rollout_percentage' => $percentage]);
    }

    public function scheduledBetween(string $start, string $end): self
    {
        return $this->state(['starts_at' => $start, 'ends_at' => $end]);
    }
}
