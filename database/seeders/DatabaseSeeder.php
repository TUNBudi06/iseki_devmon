<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            BrandSeeder::class,
            PhoneListSeeder::class,
        ]);

        User::insertOrIgnore([
            ['name' => 'Admin', 'username' => 'admin', 'password' => 'admin123'],
            ['name' => 'Saiful', 'username' => 'saiful', 'password' => 'vockompenir'],
        ]);
    }
}
