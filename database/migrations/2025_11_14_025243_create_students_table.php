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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            // Ganti 'class_id' menjadi 'school_class_id' dan arahkan ke tabel 'school_classes'
            $table->foreignId('school_class_id')->constrained('school_classes')->onDelete('cascade');
            $table->integer('nomor_absen');
            $table->string('nisn')->unique();
            $table->string('name');
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
        Schema::dropIfExists('students');
    }
};
