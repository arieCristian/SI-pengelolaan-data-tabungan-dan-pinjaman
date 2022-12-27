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
        Schema::create('tabungan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nasabah_id');
            $table->foreignId('users_id');
            $table->bigInteger('no');
            $table->string('jenis');
            $table->double('bunga');
            $table->bigInteger('setoran_tetap')->nullable();
            $table->date('tgl_setoran')->nullable();
            $table->bigInteger('sudah_setor')->nullable();
            $table->bigInteger('bunga_program')->nullable();
            $table->bigInteger('bunga_diambil')->nullable();
            $table->date('tgl_mulai')->nullable();
            $table->date('tgl_selesai')->nullable();
            $table->bigInteger('lama_program')->nullable();
            $table->string('status')->nullable();
            $table->bigInteger('jum_deposito')->nullable();
            $table->bigInteger('total')->nullable();
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
        Schema::dropIfExists('tabungan');
    }
};
