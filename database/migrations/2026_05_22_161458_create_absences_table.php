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
        Schema::create('absences', function (Blueprint $table) {
            $table->id();
            $table->string('device_id');
            $table->string('nik', 6);
            $table->string('name');
            $table->text('catatan')->nullable();
            $table->timestamp('time_absence')->useCurrent();
            $table->timestamps();

            $table->index(['device_id', 'time_absence']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absences');
    }
};
