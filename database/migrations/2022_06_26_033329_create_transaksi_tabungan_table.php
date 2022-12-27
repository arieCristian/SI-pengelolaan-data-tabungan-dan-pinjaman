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
        Schema::create('transaksi_tabungan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tabungan_id');
            $table->foreignId('users_id');
            $table->foreignId('bunga_id')->nullable();
            $table->string('jenis')->nullable();
            $table->bigInteger('jumlah');
            $table->bigInteger('bunga')->nullable();
            $table->bigInteger('tabungan_awal')->nullable();
            $table->bigInteger('tabungan_akhir')->nullable();
            $table->string('keterangan')->nullable();
            $table->string('setor')->nullable();
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
        Schema::dropIfExists('transaksi_tabungan');
    }
};
