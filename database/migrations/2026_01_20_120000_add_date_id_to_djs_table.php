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
        if (Schema::hasTable('d_j_s')) {
            Schema::table('d_j_s', function (Blueprint $table) {
                if (!Schema::hasColumn('d_j_s', 'date_id')) {
                    $table->foreignId('date_id')->nullable()->constrained('dates')->onDelete('cascade')->after('id');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('d_j_s')) {
            Schema::table('d_j_s', function (Blueprint $table) {
                if (Schema::hasColumn('d_j_s', 'date_id')) {
                    $table->dropForeign(['date_id']);
                    $table->dropColumn('date_id');
                }
            });
        }
    }
};
