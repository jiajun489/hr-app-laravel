<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_ai_analyses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees');
            $table->date('analysis_date');
            $table->text('check_in_pattern_summary')->nullable();
            $table->text('ai_insights')->nullable();
            $table->enum('risk_level', ['low', 'medium', 'high'])->default('low');
            $table->json('categories')->nullable();
            $table->text('recommendations')->nullable();
            $table->boolean('notified_hr')->default(false);
            $table->text('hr_feedback')->nullable();
            $table->timestamps();
            
            // Each employee should have only one analysis per date
            $table->unique(['employee_id', 'analysis_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_ai_analyses');
    }
};
