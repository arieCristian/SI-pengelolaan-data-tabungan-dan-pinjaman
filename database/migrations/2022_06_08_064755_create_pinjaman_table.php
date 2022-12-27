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
        Schema::create('pinjaman', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nasabah_id');
            $table->bigInteger('pinjaman');
            $table->double('bunga');
            $table->bigInteger('bunga_dibayar');
            $table->bigInteger('angsuran_pokok');
            $table->bigInteger('sisa_pinjaman');
            $table->integer('lama_angsuran');
            $table->date('tgl_angsuran')->nullable();
            $table->integer('sudah_mengangsur');
            $table->string('jaminan');
            $table->string('ktp');
            $table->string('kk');
            $table->string('status');
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
        Schema::dropIfExists('pinjaman');
    }
};
