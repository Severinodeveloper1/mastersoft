<?php

namespace Database\Factories;

use App\Enums\ClaimType;
use App\Enums\DocumentType;
use App\Enums\GoodType;
use App\Models\Claim;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Claim>
 */
class ClaimFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'document_type' => DocumentType::Dni,
            'document_number' => fake()->numerify('########'),
            'first_name' => fake()->firstName(),
            'paternal_surname' => fake()->lastName(),
            'maternal_surname' => fake()->lastName(),
            'phone' => fake()->numerify('9########'),
            'email' => fake()->safeEmail(),
            'department' => 'San Martín',
            'province' => 'Rioja',
            'district' => 'Nueva Cajamarca',
            'address' => fake()->streetAddress(),
            'is_minor' => false,
            'good_type' => GoodType::Product,
            'order_reference' => null,
            'claimed_amount' => 380.00,
            'good_description' => fake()->sentence(),
            'claim_type' => ClaimType::Complaint,
            'detail' => fake()->paragraph(),
            'consumer_request' => fake()->sentence(),
            'privacy_accepted_at' => now(),
            'email_notification_accepted' => true,
        ];
    }
}
