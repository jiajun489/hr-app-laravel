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
        Schema::table('tasks', function (Blueprint $table) {
            // Rename repo column to pr_url
            $table->renameColumn('repo', 'pr_url');
            
            // Add platform field
            $table->string('platform')->nullable()->after('pr_url')->comment('Platform where the PR is hosted (e.g., GitHub, GitLab, Bitbucket)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            // Remove platform field
            $table->dropColumn('platform');
            
            // Rename pr_url back to repo
            $table->renameColumn('pr_url', 'repo');
        });
    }
};
