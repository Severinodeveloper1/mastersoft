<?php

namespace Database\Factories;

use App\Enums\CustomerType;
use App\Enums\DeliveryMethod;
use App\Enums\PaymentPreference;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'customer_type' => CustomerType::Person,
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'document_number' => fake()->numerify('########'),
            'phone' => fake()->numerify('9########'),
            'email' => fake()->safeEmail(),
            'delivery_method' => DeliveryMethod::Shipping,
            'department' => 'San Martín',
            'province' => 'Rioja',
            'district' => 'Nueva Cajamarca',
            'address' => fake()->streetAddress(),
            'payment_preference' => PaymentPreference::BankTransfer,
            'subtotal' => 411.02,
            'tax' => 73.98,
            'total' => 485.00,
            'privacy_accepted_at' => now(),
        ];
    }
}
