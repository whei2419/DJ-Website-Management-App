<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        if (Schema::hasTable('djs') && ! Schema::hasColumn('djs', 'visible')) {
            Schema::table('djs', function (Blueprint $table) {
                $table->boolean('visible')->default(true)->after('slot');
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('djs') && Schema::hasColumn('djs', 'visible')) {
            Schema::table('djs', function (Blueprint $table) {
                $table->dropColumn('visible');
            });
        }
    }
};
