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
            $table->uuid('id')->primary();
            $table->datetime('completed_at');
            $table->timestamps();
            $table->uuid('user_exam_id');
            $table->foreign('user_exam_id')->references('id')->on('user_exams')->onDelete('cascade')->onUpdate('None');
            $table->uuid('exam_id');
            $table->foreign('exam_id')->references('id')->on('exams')->onDelete('cascade')->onUpdate('None');
            $table->uuid('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('None');
            $table->uuid('teacher_id');
            $table->foreign('teacher_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('None');
            $table->integer('total_question');
            $table->integer('correct_answer');
            $table->integer('wrong_answer');
            $table->integer('unanswered');
            $table->decimal('score', 5, 2);
            $table->decimal('percentage',5,2);
            $table->boolean('is_passed')->default(false); // Indicates if the user passed the exam
            $table->json('detailed_answer');
            $table->integer('time_spent_minutes')->nullable(); // Time spent on the exam in minutes
            $table->text('feedback')->nullable();


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
