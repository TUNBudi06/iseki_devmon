<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('departemens', function (Blueprint $table) {
            $table->string('color', 20)->default('#f59e0b')->after('name');
        });

        // Update existing colors
        DB::table('departemens')->where('id', 'Production')->update(['color' => '#f59e0b']);
        DB::table('departemens')->where('id', 'QC')->update(['color' => '#0ea5e9']);
    }

    public function down(): void
    {
        Schema::table('departemens', function (Blueprint $table) {
            $table->dropColumn('color');
        });
    }
};
