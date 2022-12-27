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
        Schema::create('transaksi_pinjaman', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pinjaman_id');
            $table->string('jenis');
            $table->string('keterangan')->nullable();
            $table->bigInteger('jumlah');
            $table->bigInteger('bunga')->nullable();
            $table->bigInteger('angsuran')->nullable();
            $table->bigInteger('sisa_pinjaman');
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
        Schema::dropIfExists('transaksi_pinjaman');
    }
};
