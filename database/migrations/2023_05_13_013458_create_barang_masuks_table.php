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
        Schema::create('barang_masuks', function (Blueprint $table) {
            $table->id();
            $table->string('kode_transaksi')->unique();
            $table->string('lot');
            $table->date('tanggal_masuk');
            $table->date('tanggal_kadaluarsa');
            $table->integer('jumlah_masuk');
            $table->integer('outstanding')->default(0);
            $table->integer('jumlah_stok');
            $table->integer('harga');
            $table->string('lokasi');
            $table->string('keterangan')->nullable();
            $table->boolean('approved')->default(false);
            $table->foreignId('barang_id')->constrained();
            $table->foreignId('supplier_id')->constrained();
            $table->foreignId('user_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barang_masuks');
    }
};
