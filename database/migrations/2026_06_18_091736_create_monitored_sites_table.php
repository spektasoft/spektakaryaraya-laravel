<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('monitored_sites', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('creator_id')->constrained('users');
            $table->foreignUlid('project_id')->constrained();
            $table->string('name');
            $table->string('url');
            $table->boolean('is_active')->default(true);
            $table->string('uptime_status')->default('unknown');
            $table->integer('last_uptime_code')->nullable();
            $table->dateTime('last_uptime_checked_at')->nullable();
            $table->integer('last_uptime_latency')->nullable();
            $table->string('integrity_status')->default('unknown');
            $table->dateTime('last_integrity_checked_at')->nullable();
            $table->string('expected_md5_hash')->nullable();
            $table->string('last_md5_hash')->nullable();
            $table->integer('expected_links_count')->nullable();
            $table->integer('last_links_count')->nullable();
            $table->integer('expected_scripts_count')->nullable();
            $table->integer('last_scripts_count')->nullable();
            $table->text('last_error')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monitored_sites');
    }
};
