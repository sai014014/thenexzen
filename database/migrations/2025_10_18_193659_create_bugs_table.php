<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bugs', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->enum('status', ['open', 'in_progress', 'testing', 'resolved', 'closed'])->default('open');
            $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->enum('type', ['bug', 'feature_request', 'improvement', 'task'])->default('bug');
            $table->string('reported_by')->nullable();
            $table->string('assigned_to')->nullable();
            $table->text('steps_to_reproduce')->nullable();
            $table->text('expected_behavior')->nullable();
            $table->text('actual_behavior')->nullable();
            $table->text('resolution_notes')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bugs');
    }
};
