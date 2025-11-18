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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            // Ganti 'class_id'
            $table->foreignId('school_class_id')->constrained('school_classes');
            $table->foreignId('student_id')->nullable()->constrained();
            $table->foreignId('user_id');
            $table->enum('type', ['masuk', 'keluar']);
            $table->integer('amount');
            $table->date('date');
            $table->string('description');
            $table->string('proof_image')->nullable();
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
        Schema::dropIfExists('transactions');
    }
};
