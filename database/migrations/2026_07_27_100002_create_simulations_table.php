<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('simulations', function (Blueprint $table) {
            $table->id();
            $table->string('status', 20)->default('running');
            $table->timestampTz('started_at')->nullable();
            $table->timestampTz('completed_at')->nullable();
            $table->jsonb('input_data')->nullable();
            $table->integer('total_duration_ms')->nullable();
            $table->timestampTz('created_at')->useCurrent();
            $table->uuid('flow_id');

            $table->foreign('flow_id')->references('id')->on('flows')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('simulations');
    }
};