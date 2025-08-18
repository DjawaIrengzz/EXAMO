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
        Schema::create('exam_result_a_i_s', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('exam_result_id')->index();
            $table->string('model_version')->nullable();
            $table->decimal('score', 5,2)->nullable();
            $table->decimal('confidence', 5,4)->nullable();
            $table->text('explanation')->nullable();
            $table->text('prompt')->nullable();
            $table->json('metadata')->nullable();
            $table->boolean('human_override')->default(false);
            $table->decimal('human_score', 5,2)->nullable();
            $table->text('human_feedback')->nullable();
            $table->timestamps();

    $table->foreign('exam_result_id')->references('id')->on('exam_results')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_result_a_i_s');
    }
};
