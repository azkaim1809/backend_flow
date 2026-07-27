<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('flow_connections', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('source_node_id');
            $table->unsignedBigInteger('target_node_id');
            $table->string('branch_label', 50)->nullable();
            $table->timestampTz('created_at')->useCurrent();
            $table->uuid('flow_id');

            $table->foreign('source_node_id')->references('id')->on('flow_nodes')->cascadeOnDelete();
            $table->foreign('target_node_id')->references('id')->on('flow_nodes')->cascadeOnDelete();
            $table->foreign('flow_id')->references('id')->on('flows')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('flow_connections');
    }
};