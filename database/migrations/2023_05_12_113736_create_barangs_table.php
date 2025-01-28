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
        Schema::create('barangs', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('kode_barang')->unique();
            $table->string('nama_barang');
            $table->string('deskripsi')->nullable();
            $table->string('gambar')->nullable();
            $table->integer('stok_minimum');
            $table->integer('stok')->nullable()->default(0);
            $table->string('control')->nullable();
            $table->string('test_group');
            $table->foreignId('user_id');
            $table->foreignId('jenis_id')->constrained();
            $table->foreignId('satuan_id')->constrained();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barangs');
    }
};
