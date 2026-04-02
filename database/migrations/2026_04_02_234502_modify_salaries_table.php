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
        Schema::table('salaries', function (Blueprint $table) {
            $table->dropColumn('head_id');
            $table->date('tanggal')->nullable();
            $table->integer('sesi')->default(0);
            $table->integer('persentase')->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->integer('jumlah_pertemuan')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('salaries', function (Blueprint $table) {
            $table->unsignedBigInteger('head_id')->nullable();
            $table->dropColumn(['tanggal', 'sesi', 'persentase', 'total', 'jumlah_pertemuan']);
        });
    }
};
