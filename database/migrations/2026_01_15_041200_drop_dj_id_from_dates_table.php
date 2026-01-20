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
        Schema::table('dates', function (Blueprint $table) {
            if (Schema::hasColumn('dates', 'dj_id')) {
                $table->dropForeign(['dj_id']);
                $table->dropColumn('dj_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dates', function (Blueprint $table) {
            $table->foreignId('dj_id')->nullable()->constrained('djs')->onDelete('set null');
        });
    }
};
