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
        // 1. Update cache table
        if (Schema::hasTable('cache')) {
            Schema::table('cache', function (Blueprint $table) {
                $table->bigInteger('expiration')->change();
                $table->index('expiration');
            });
        }

        // 2. Update cache_locks table
        if (Schema::hasTable('cache_locks')) {
            Schema::table('cache_locks', function (Blueprint $table) {
                $table->bigInteger('expiration')->change();
                $table->index('expiration');
            });
        }

        // 3. Update jobs table
        if (Schema::hasTable('jobs')) {
            Schema::table('jobs', function (Blueprint $table) {
                $table->unsignedSmallInteger('attempts')->change();
            });
        }

        // 4. Update failed_jobs table
        if (Schema::hasTable('failed_jobs')) {
            Schema::table('failed_jobs', function (Blueprint $table) {
                // Changing text columns to string (varchar) so they can be indexed
                $table->string('connection')->change();
                $table->string('queue')->change();
                $table->index(['connection', 'queue', 'failed_at']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Reverse failed_jobs table updates
        if (Schema::hasTable('failed_jobs')) {
            Schema::table('failed_jobs', function (Blueprint $table) {
                $table->dropIndex(['connection', 'queue', 'failed_at']);
                $table->text('connection')->change();
                $table->text('queue')->change();
            });
        }

        // 2. Reverse jobs table updates
        if (Schema::hasTable('jobs')) {
            Schema::table('jobs', function (Blueprint $table) {
                $table->unsignedTinyInteger('attempts')->change();
            });
        }

        // 3. Reverse cache_locks table updates
        if (Schema::hasTable('cache_locks')) {
            Schema::table('cache_locks', function (Blueprint $table) {
                $table->dropIndex(['expiration']);
                $table->integer('expiration')->change();
            });
        }

        // 4. Reverse cache table updates
        if (Schema::hasTable('cache')) {
            Schema::table('cache', function (Blueprint $table) {
                $table->dropIndex(['expiration']);
                $table->integer('expiration')->change();
            });
        }
    }
};
