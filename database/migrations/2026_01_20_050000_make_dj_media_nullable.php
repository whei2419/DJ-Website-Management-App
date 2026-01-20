<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('djs')) {
            try {
                DB::statement("ALTER TABLE `djs` MODIFY `video_url` VARCHAR(255) NULL DEFAULT NULL;");
                DB::statement("ALTER TABLE `djs` MODIFY `video_path` VARCHAR(255) NULL DEFAULT NULL;");
                DB::statement("ALTER TABLE `djs` MODIFY `preview_video_path` VARCHAR(255) NULL DEFAULT NULL;");
                DB::statement("ALTER TABLE `djs` MODIFY `poster_path` VARCHAR(255) NULL DEFAULT NULL;");
            } catch (\Exception $e) {
                logger()->warning('Could not alter djs media columns to nullable', ['exception' => $e->getMessage()]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('djs')) {
            try {
                DB::statement("ALTER TABLE `djs` MODIFY `video_url` VARCHAR(255) NOT NULL;");
                DB::statement("ALTER TABLE `djs` MODIFY `video_path` VARCHAR(255) NOT NULL;");
                DB::statement("ALTER TABLE `djs` MODIFY `preview_video_path` VARCHAR(255) NOT NULL;");
                DB::statement("ALTER TABLE `djs` MODIFY `poster_path` VARCHAR(255) NOT NULL;");
            } catch (\Exception $e) {
                logger()->warning('Could not revert djs media columns to NOT NULL', ['exception' => $e->getMessage()]);
            }
        }
    }
};
