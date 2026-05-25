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

        // Generate dev-XXX berdasarkan increment terakhir di DB
        $last = PhoneList::where('model_id', 'like', 'dev-%')
            ->orderByRaw('CAST(SUBSTRING(model_id, -3) AS UNSIGNED) DESC')
            ->first();
        $lastIncrement = $last ? (int) substr($last->model_id, -3) : 0;
        $nextIncrement = str_pad($lastIncrement + 1, 3, '0', STR_PAD_LEFT);

        return [
            'brand_id' => $brandId,
            'model_id' => 'dev-'.$nextIncrement,
            'model_name' => $modelName,
            'model_type' => fake()->randomElement(['Phone', 'Tablet']),
            'buy_date' => fake()->date(),
            'price' => (string) fake()->numberBetween(1_000_000, 15_000_000),
            'ram' => fake()->randomElement(['4 GB', '6 GB', '8 GB', '12 GB']),
            'storage' => fake()->randomElement(['64 GB', '128 GB', '256 GB', '512 GB']),
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
