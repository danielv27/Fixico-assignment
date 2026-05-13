<?php

namespace Database\Factories;

use App\DamageReports\ReportStatus;
use App\Models\DamageReport;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DamageReport>
 */
class DamageReportFactory extends Factory
{
    public function definition(): array
    {
        return [
            'vehicle_make' => fake()->randomElement(['Toyota', 'Volkswagen', 'BMW', 'Renault', 'Ford']),
            'vehicle_model' => fake()->word(),
            'license_plate' => strtoupper(fake()->bothify('??-###-??')),
            'description' => fake()->paragraph(),
            'status' => ReportStatus::Draft,
        ];
    }

    public function submitted(): self
    {
        return $this->state(['status' => ReportStatus::Submitted]);
    }

    public function approved(): self
    {
        return $this->state(['status' => ReportStatus::Approved]);
    }
}
