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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('induk')->nullable();
            $table->string('jenjang')->nullable();
            $table->integer('kelas')->nullable();
            $table->string('place')->nullable();
            $table->date('birth')->nullable();
            $table->integer('gender')->nullable();
            $table->string('sekolah_kelas')->nullable();
            $table->string('alamat_sekolah')->nullable();
            $table->string('dream')->nullable();
            $table->string('hp_siswa')->nullable();
            $table->string('agama')->nullable();
            $table->string('sosmedChild')->nullable();
            $table->string('sosmedOther')->nullable();
            $table->string('dad')->nullable();
            $table->string('dadJob')->nullable();
            $table->string('mom')->nullable();
            $table->string('momJOb')->nullable();
            $table->string('hp_parent')->nullable();
            $table->string('study')->nullable();
            $table->string('rank')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
