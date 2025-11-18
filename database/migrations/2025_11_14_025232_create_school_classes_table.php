<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('school_classes', function (Blueprint $table) { // <-- Nama tabel berubah
            $table->id();
            $table->string('jurusan');
            $table->string('name');
            $table->string('slug')->unique();
            $table->integer('tagihan_nominal');
            $table->enum('tagihan_tipe', ['harian', 'mingguan', 'bulanan']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('school_classes');
    }
};
