<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            BrandSeeder::class,
        ]);

        User::insert([
            ['name' => 'Admin', 'username' => 'admin', 'password' => 'admin123'],
            ['name' => 'Saiful', 'username' => 'saiful', 'password' => 'vockompenir'],
        ]);
    }
}
