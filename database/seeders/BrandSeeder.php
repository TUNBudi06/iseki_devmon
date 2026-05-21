<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        Brand::insert([
            ['id' => 'samsung', 'name' => 'Samsung'],
            ['id' => 'vivo', 'name' => 'Vivo'],
            ['id' => 'redmi', 'name' => 'Redmi'],
            ['id' => 'oppo', 'name' => 'Oppo'],
            ['id' => 'poco', 'name' => 'Poco'],
        ]);
    }
}
