<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // First change the default value to NULL
            DB::statement('ALTER TABLE users ALTER COLUMN employee_id DROP DEFAULT');
            
            // Then change the type to bigint and make it nullable
            DB::statement('ALTER TABLE users ALTER COLUMN employee_id TYPE bigint USING (CASE WHEN employee_id = \'0\' THEN NULL ELSE employee_id::bigint END)');
            DB::statement('ALTER TABLE users ALTER COLUMN employee_id DROP NOT NULL');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
