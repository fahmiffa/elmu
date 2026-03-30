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
            $table->string('program')->nullable();
            $table->string('level_period')->nullable();
            $table->string('teacher')->nullable();
            $table->string('leader')->nullable();
            
            // Kompetensi Utama
            $table->integer('score_concept')->default(0);
            $table->string('note_concept')->nullable();
            $table->integer('score_concentration')->default(0);
            $table->string('note_concentration')->nullable();
            $table->integer('score_accuracy')->default(0);
            $table->string('note_accuracy')->nullable();
            $table->integer('score_independence')->default(0);
            $table->string('note_independence')->nullable();

            // Deskripsi
            $table->text('strength')->nullable();
            $table->text('progress_period')->nullable();
            $table->text('improvement')->nullable();

            // Ringkasan
            $table->string('category')->nullable();
            $table->string('recommendation')->nullable();
            $table->text('recommendation_note')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('raports', function (Blueprint $table) {
            $table->dropColumn([
                'program', 'level_period', 'teacher', 'leader',
                'score_concept', 'note_concept',
                'score_concentration', 'note_concentration',
                'score_accuracy', 'note_accuracy',
                'score_independence', 'note_independence',
                'strength', 'progress_period', 'improvement',
                'category', 'recommendation', 'recommendation_note'
            ]);
        });
    }
};
