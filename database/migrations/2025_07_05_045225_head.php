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
        Schema::create('head', function (Blueprint $table) {
            $table->id();
            $table->text('note')->nullable();
            $table->date('tanggal')->nullable();
            $table->Biginteger('students')->nullable();
            $table->Biginteger('unit')->nullable();
            $table->Biginteger('kelas')->nullable();
            $table->Biginteger('program')->nullable();
            $table->Biginteger('payment')->nullable();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
