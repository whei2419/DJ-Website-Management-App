<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        if (Schema::hasTable('d_j_s') && !Schema::hasColumn('d_j_s', 'video_path')) {
            Schema::table('d_j_s', function (Blueprint $table) {
                $table->string('video_path')->nullable()->after('slot');
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('d_j_s') && Schema::hasColumn('d_j_s', 'video_path')) {
            Schema::table('d_j_s', function (Blueprint $table) {
                $table->dropColumn('video_path');
            });
        }
    }
};
