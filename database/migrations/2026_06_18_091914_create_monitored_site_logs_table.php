<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('monitored_site_logs', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('monitored_site_id')->constrained()->cascadeOnDelete();
            $table->string('type')->index();
            $table->string('status');
            $table->integer('status_code')->nullable();
            $table->integer('latency')->nullable();
            $table->json('details')->nullable();
            $table->timestamps();

            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monitored_site_logs');
    }
};
