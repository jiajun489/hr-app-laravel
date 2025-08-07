<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add manager relationship to employees
        Schema::table('employees', function (Blueprint $table) {
            $table->foreignId('manager_id')->nullable()->constrained('employees')->after('role_id');
            $table->integer('annual_leave_days')->default(21)->after('salary');
        });

        // Add reason field to leave_requests
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->text('reason')->nullable()->after('end_date');
        });

        // Create work_life_balance_metrics table
        Schema::create('work_life_balance_metrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees');
            $table->date('week_start');
            $table->decimal('overtime_hours', 5, 2)->default(0);
            $table->integer('consecutive_work_days')->default(0);
            $table->decimal('leave_balance_ratio', 3, 2)->default(0);
            $table->timestamps();
            
            $table->unique(['employee_id', 'week_start']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_life_balance_metrics');
        
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->dropColumn('reason');
        });
        
        Schema::table('employees', function (Blueprint $table) {
            $table->dropForeign(['manager_id']);
            $table->dropColumn(['manager_id', 'annual_leave_days']);
        });
    }
};