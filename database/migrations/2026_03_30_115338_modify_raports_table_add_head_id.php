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
        Schema::table('raports', function (Blueprint $table) {
            $table->unsignedBigInteger('head_id')->nullable()->after('student_id');
            
            // Re-check table name 'head' for Foreign Key
            $table->foreign('head_id')->references('id')->on('head')->onDelete('set null');

            // Drop obsolete fields
            if (Schema::hasColumn('raports', 'program')) {
                $table->dropColumn('program');
            }
            if (Schema::hasColumn('raports', 'level_period')) {
                $table->dropColumn('level_period');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('raports', function (Blueprint $table) {
            $table->dropForeign(['head_id']);
            $table->dropColumn('head_id');
            $table->string('program')->nullable();
            $table->string('level_period')->nullable();
        });
    }
};
