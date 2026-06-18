<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('phone_lists', function (Blueprint $table) {
            $table->string('departemen', 50)->default('Production')->after('storage');
        });
    }

    public function down(): void
    {
        Schema::table('phone_lists', function (Blueprint $table) {
            $table->dropColumn('departemen');
        });
    }
};
