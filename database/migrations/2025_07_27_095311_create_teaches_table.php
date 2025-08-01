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
        // Schema::create('teaches', function (Blueprint $table) {
        //     $table->id();
        //     $table->string('name');
        //     $table->string('hp');
        //     $table->string('study');
        //     $table->text('addr');
        //     $table->date('birth');
        //     $table->timestamps();
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teaches');
    }
};
