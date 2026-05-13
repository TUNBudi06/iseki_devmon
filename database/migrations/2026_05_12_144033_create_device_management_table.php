<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('device_management', function (Blueprint $table) {
            $table->id();
            $table->string('device_name');
            $table->string('device_id')->unique();
            $table->boolean('approved')->default(false);
            $table->string('token')->comment('token hashed');
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('device_management');
    }
};
