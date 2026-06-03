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
        Schema::table('phone_lists', function (Blueprint $table) {
            $table->string('imei', 15)->nullable()->unique()->after('storage')->comment('IMEI untuk HP/Tablet cellular');
            $table->string('mac_address', 17)->nullable()->unique()->after('imei')->comment('MAC Address WiFi untuk semua device (fallback jika tidak ada IMEI)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('phone_lists', function (Blueprint $table) {
            $table->dropColumn(['imei', 'mac_address']);
        });
    }
};
