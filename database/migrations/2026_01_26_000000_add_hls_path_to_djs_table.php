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
        Schema::table('djs', function (Blueprint $table) {
            if (!Schema::hasColumn('djs', 'hls_path')) {
                $table->string('hls_path')->nullable()->after('poster_path');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('djs', function (Blueprint $table) {
            if (Schema::hasColumn('djs', 'hls_path')) {
                $table->dropColumn('hls_path');
            }
        });
    }
};
