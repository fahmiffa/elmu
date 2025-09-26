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
        Schema::create('paids', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('head');
            $table->date('tanggal')->nullable();
            $table->timestamp('time')->nullable();
            $table->text('ket')->nullable();
            $table->text('via')->nullable();
            $table->integer('status')->default(0);
            $table->integer('bulan')->default(0);
            $table->integer('first')->default(0);
            $table->string('tahun')->nullable();
            $table->string('mid')->nullable();
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paids');
    }
};
