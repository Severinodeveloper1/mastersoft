<?php

namespace Database\Factories;

use App\Enums\BannerPlacement;
use App\Models\Banner;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Banner>
 */
class BannerFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'placement' => BannerPlacement::Hero,
            'title' => fake()->sentence(3),
            'link_url' => null,
            'sort_order' => 0,
            'is_active' => true,
        ];
    }
}
