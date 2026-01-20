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
        if (Schema::hasTable('djs')) {
            Schema::table('djs', function (Blueprint $table) {
                if (!Schema::hasColumn('djs', 'preview_video_path')) {
                    $table->string('preview_video_path')->nullable()->after('video_path');
                }
                if (!Schema::hasColumn('djs', 'poster_path')) {
                    $table->string('poster_path')->nullable()->after('preview_video_path');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('djs')) {
            Schema::table('djs', function (Blueprint $table) {
                if (Schema::hasColumn('djs', 'poster_path')) {
                    $table->dropColumn('poster_path');
                }
                if (Schema::hasColumn('djs', 'preview_video_path')) {
                    $table->dropColumn('preview_video_path');
                }
            });
        }
    }
};
