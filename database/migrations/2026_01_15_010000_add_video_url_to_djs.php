<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        if (Schema::hasTable('d_j_s') && !Schema::hasColumn('d_j_s', 'video_url')) {
            Schema::table('d_j_s', function (Blueprint $table) {
                $table->string('video_url')->nullable()->after('name');
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('d_j_s') && Schema::hasColumn('d_j_s', 'video_url')) {
            Schema::table('d_j_s', function (Blueprint $table) {
                $table->dropColumn('video_url');
            });
        }
    }
};
