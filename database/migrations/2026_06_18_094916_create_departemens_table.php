<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('departemens', function (Blueprint $table) {
            $table->string('id', 50)->primary();
            $table->string('name', 100);
            $table->timestamps();
        });

        // Seed initial departemen
        DB::table('departemens')->insert([
            ['id' => 'Production', 'name' => 'Production', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 'QC', 'name' => 'QC', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('departemens');
    }
};
