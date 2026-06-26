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
        Schema::create('device_checks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('phone_list_id')->constrained('phone_lists')->cascadeOnDelete();
            $table->string('checked_by_name');
            $table->string('checked_by_username');
            $table->boolean('imei_ok')->default(false);
            $table->boolean('mac_ok')->default(false);
            $table->text('keterangan')->nullable();
            $table->json('foto')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('device_checks');
    }
};
