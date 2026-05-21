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
        Schema::create('phone_lists', function (Blueprint $table) {
            $table->id();
            $table->string('brand_id');
            $table->string('model_id')->comment('unique identifier for each device unit (like serial number)');
            $table->string('model_type')->comment('Phone or Tablet');
            $table->string('buy_date');
            $table->string('price');
            $table->string('ram');
            $table->string('storage');
            $table->boolean('registered')->default(false)->comment('since this set as belum terdaftar semisal hp itu belum terkoneksi terdaftar di web');
            $table->text('hash_token')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phone_lists');
    }
};
