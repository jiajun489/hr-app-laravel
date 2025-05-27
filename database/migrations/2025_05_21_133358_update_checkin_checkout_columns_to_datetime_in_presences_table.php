<?php
// database/migrations/2025_05_21_133358_update_checkin_checkout_columns_to_datetime_in_presences_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('presences', function (Blueprint $table) {
            $table->dateTime('check_in')->change();
            $table->dateTime('check_out')->nullable()->change(); // <- update jadi nullable
        });
    }

    public function down(): void {
        Schema::table('presences', function (Blueprint $table) {
            $table->date('check_in')->change();
            $table->date('check_out')->nullable(false)->change(); // <- revert nullable di down
        });
    }
};
