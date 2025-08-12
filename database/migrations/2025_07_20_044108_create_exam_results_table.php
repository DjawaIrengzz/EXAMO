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
        Schema::create('exam_results', function (Blueprint $table) {
            $table->string('ai_model_version')->nullable();
    $table->decimal('ai_essay_score', 5, 2)->nullable();   // 0.00 - 100.00
    $table->decimal('ai_confidence', 5, 4)->nullable();    // 0.0000 - 1.0000
    $table->text('ai_explanation')->nullable();
    $table->text('ai_prompt')->nullable();                 // consider hashing if sensitive
    $table->json('ai_metadata')->nullable();
    $table->decimal('human_score', 5, 2)->nullable();
    $table->boolean('human_override')->default(false);
    $table->text('human_feedback')->nullable();
    $table->decimal('ai_contribution_pct', 5, 2)->nullable();
    // Indexes:
    $table->index('user_id');
    $table->index('exam_id');
    $table->index('teacher_id');
    $table->index('ai_model_version');


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_results');
    }
};
