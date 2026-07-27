<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('node_executions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('simulation_id');
            $table->unsignedBigInteger('flow_node_id');
            $table->string('node_label', 255)->nullable();
            $table->string('node_type', 50)->nullable();
            $table->string('status', 20)->default('pending');
            $table->jsonb('input_data')->nullable();
            $table->jsonb('output_data')->nullable();
            $table->text('message')->nullable();
            $table->integer('duration_ms')->nullable();
            $table->timestampTz('executed_at')->nullable();
            $table->text('error_message')->nullable();

            $table->foreign('simulation_id')->references('id')->on('simulations')->cascadeOnDelete();
            $table->foreign('flow_node_id')->references('id')->on('flow_nodes')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('node_executions');
    }
};