<?php

namespace Database\Factories;

use App\Models\TravelRequest;
use App\Models\TravelRequestStatusHistory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TravelRequestStatusHistoryFactory extends Factory
{
    protected $model = TravelRequestStatusHistory::class;

    public function definition(): array
    {
        return [
            'travel_request_id' => TravelRequest::factory(),
            'status' => TravelRequest::STATUS_REQUESTED,
            'comment' => $this->faker->optional()->sentence(),
            'changed_by' => User::factory(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
