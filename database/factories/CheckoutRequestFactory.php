<?php

namespace Database\Factories;

use App\Models\CheckoutRequest;
use App\Models\Equipment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CheckoutRequestFactory extends Factory
{
    protected $model = CheckoutRequest::class;

    public function definition(): array
    {
        $startDate = $this->faker->dateTimeBetween('-1 month', '+1 month');
        $endDate = (clone $startDate)->modify('+' . $this->faker->numberBetween(1, 14) . ' days');

        return [
            // If users/equipment don't exist yet, the factory will create them on the fly
            'user_id' => User::factory(),
            'equipment_id' => Equipment::factory(),

            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'purpose' => $this->faker->sentence(),

            'status' => $this->faker->randomElement([
                'pending', 'pending',
                'approved', 'active', 'active',
                'denied', 'returned', 'overdue'
            ]),

            'admin_notes' => $this->faker->optional(0.2)->sentence(),
        ];
    }
}
