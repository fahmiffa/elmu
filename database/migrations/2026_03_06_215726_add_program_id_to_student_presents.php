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
        Schema::table('student_presents', function (Blueprint $table) {
            $table->unsignedBigInteger('program_id')->nullable()->after('unit_schedules_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_presents', function (Blueprint $table) {
            $table->dropColumn('program_id');
        });
    }
};
