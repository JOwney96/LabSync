<?php

namespace Database\Factories;

use App\Models\Equipment;
use Illuminate\Database\Eloquent\Factories\Factory;

class EquipmentFactory extends Factory
{
    protected $model = Equipment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Realistic lab equipment categories
        $categories = [
            'Microscope', 'Centrifuge', 'Spectrophotometer',
            'Incubator', 'Autoclave', 'PCR Machine', 'Freezer'
        ];

        $category = $this->faker->randomElement($categories);

        // Generate a realistic tag ID (e.g., MIC-042, CEN-199)
        $prefix = strtoupper(substr($category, 0, 3));
        $tagId = $prefix . '-' . $this->faker->unique()->numerify('###');

        return [
            'name' => $this->faker->company() . ' ' . $category, // e.g., "Fisher Scientific Centrifuge"
            'tag_id' => $tagId,
            'category' => $category,
            // Weight the statuses so most are available or in use, few in maintenance
            'status' => $this->faker->randomElement([
                'available', 'available', 'available',
                'in_use', 'in_use',
                'maintenance',
                'retired'
            ]),
            'location' => 'Room ' . $this->faker->randomLetter() . ', Bench ' . $this->faker->numberBetween(1, 12),
            // Calibration due anywhere from 2 months ago to 10 months from now
            'calibration_due' => $this->faker->dateTimeBetween('-2 months', '+10 months')->format('Y-m-d'),
            'purchase_date' => $this->faker->dateTimeBetween('-5 years', '-1 year')->format('Y-m-d'),
            'notes' => $this->faker->optional(0.3)->sentence(), // 30% chance of having notes
        ];
    }
}
