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
            $table->timestamp('time')->nullable(); // waktu khusus jika perlu
            $table->text('ket')->nullable();
            $table->integer('status')->default(0);
            $table->integer('bulan')->default(0);
            $table->string('tahun')->nullable(0);
            $table->timestamps(); // created_at dan updated_at
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
