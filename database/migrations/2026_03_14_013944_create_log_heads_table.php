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
        Schema::create('log_heads', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('head_id')->nullable();
            $table->unsignedBigInteger('student_id')->nullable();
            $table->integer('tipe')->default(0);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_heads');
    }
};
