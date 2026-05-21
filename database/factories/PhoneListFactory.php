<?php

namespace Database\Factories;

use App\Models\Brand;
use App\Models\PhoneList;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PhoneList>
 */
class PhoneListFactory extends Factory
{
    protected $model = PhoneList::class;

    public function definition(): array
    {
        $brandId = Brand::inRandomOrder()->first()?->id ?? 'samsung';
        $modelName = fake()->randomElement(['Galaxy S24', 'iPhone 15', 'Y02', '15C', 'A60', 'Hot 60']);

        return [
            'brand_id' => $brandId,
            'model_id' => fake()->unique()->regexify('[A-Z]{2,4}[0-9]{3,5}'),
            'model_name' => $modelName,
            'model_type' => fake()->randomElement(['Phone', 'Tablet']),
            'buy_date' => fake()->date(),
            'price' => (string) fake()->numberBetween(1_000_000, 15_000_000),
            'ram' => fake()->randomElement(['4GB', '6GB', '8GB', '12GB']),
            'storage' => fake()->randomElement(['64GB', '128GB', '256GB', '512GB']),
            'registered' => false,
            'hash_token' => null,
        ];
    }

    public function registered(): static
    {
        return $this->state(fn (array $attributes) => [
            'registered' => true,
            'hash_token' => hash('sha256', fake()->uuid()),
        ]);
    }

    public function forBrand(string $brandId): static
    {
        return $this->state(fn (array $attributes) => [
            'brand_id' => $brandId,
        ]);
    }
}
