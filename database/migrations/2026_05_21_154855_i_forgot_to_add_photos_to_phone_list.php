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
            $table->string('thumbnail')->nullable()->after('model_name')->comment('this a thumbnail fto for a device');
            $table->text('list_photos')->nullable()->comment('this contain list of json url foto for caraousel to make reactive to show all the photos of the device');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('phone_lists', function (Blueprint $table) {
            $table->dropColumn(['thumbnail', 'list_photos']);
        });
    }
};
