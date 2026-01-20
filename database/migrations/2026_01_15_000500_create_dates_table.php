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
        // Create `dates` table first (parent)
        Schema::create('dates', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->timestamps();
        });

        // Create `djs` table in the same migration so schema is defined in one place.
        if (! Schema::hasTable('djs')) {
            Schema::create('djs', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->foreignId('date_id')->nullable()->constrained('dates')->onDelete('cascade');
                $table->string('video_url')->nullable();
                $table->string('video_path')->nullable();
                $table->string('preview_video_path')->nullable();
                $table->string('poster_path')->nullable();
                $table->boolean('visible')->default(true);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop child table first
        Schema::dropIfExists('djs');
        Schema::dropIfExists('dates');
    }
};
