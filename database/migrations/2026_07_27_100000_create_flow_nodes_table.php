<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('flow_nodes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('template_id')->nullable();
            $table->string('label', 255);
            $table->string('node_type', 50);
            $table->string('icon', 50)->nullable();
            $table->string('color', 20)->nullable();
            $table->double('pos_x')->default(0);
            $table->double('pos_y')->default(0);
            $table->jsonb('input_params')->nullable();
            $table->text('validation_rules')->nullable();
            $table->text('process_logic')->nullable();
            $table->jsonb('output_template')->nullable();
            $table->text('condition_expression')->nullable();
            $table->integer('order_index')->default(0);
            $table->timestamps();
            $table->uuid('flow_id');

            $table->foreign('flow_id')->references('id')->on('flows')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('flow_nodes');
    }
};