<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class BarangMasuk extends Model
{
    use HasFactory;
    use LogsActivity;

    protected $fillable = ['kode_transaksi', 'lot', 'tanggal_masuk', 'tanggal_kadaluarsa', 'jumlah_masuk', 'outstanding', 'jumlah_stok', 'harga', 'lokasi', 'approved', 'barang_id', 'supplier_id', 'user_id'];
    protected $guarded = [''];
    protected $ignoreChangedAttributes = ['updated_at'];

    protected $with = ['supplier', 'barang'];  

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }

    // Activity Log
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logUnguarded()
            ->setDescriptionForEvent(fn(string $eventName) => "Barang Masuk {$eventName} dengan kode {$this->kode_transaksi}")
            ->useLogName('barang masuk');
    }

    // Log saat model Barang Masuk di-create, di-update, dan di-delete
    public static function boot()
    {
        parent::boot();

        // Log saat barang masuk baru dibuat
        static::created(function ($barangmasuk) {
            $user = auth()->user() ? auth()->user()->name : 'System';
            $timestamp = now()->format('Y-m-d H:i:s');

            $logText = "[$timestamp] User: $user\n";
            $logText .= "Barang Masuk ID: {$barangmasuk->id} - Created\n";
            $logText .= "Kode Transaksi: {$barangmasuk->kode_transaksi}\n";
            $logText .= "Lot: {$barangmasuk->lot}\n";
            $logText .= "Tanggal Masuk: {$barangmasuk->tanggal_masuk}\n";
            $logText .= "Tanggal Kadaluarsa: {$barangmasuk->tanggal_kadaluarsa}\n";
            $logText .= "Jumlah Masuk: {$barangmasuk->jumlah_masuk}\n";
            $logText .= "Outstanding: {$barangmasuk->outstanding}\n";
            $logText .= "Jumlah Stok: {$barangmasuk->jumlah_stok}\n";
            $logText .= "Harga: {$barangmasuk->harga}\n";
            $logText .= "Lokasi: {$barangmasuk->lokasi}\n";
            $logText .= "Supplier: {$barangmasuk->supplier->supplier}\n";
            $logText .= "Barang: {$barangmasuk->barang->nama_barang}\n";
            $logText .= "Approved: {$barangmasuk->approved}\n";
            $logText .= "-------------------------------------\n";

            // Simpan ke file logs/barangmasuk_log.txt
            Storage::append('public/logs/barangmasuk_log.txt', $logText);
        });

        // Log saat barang masuk diupdate
        static::updated(function ($barangmasuk) {
            $user = auth()->user() ? auth()->user()->name : 'System';
            $timestamp = now()->format('Y-m-d H:i:s');

            // Dapatkan perubahan atribut (old dan new)
            $changes = $barangmasuk->getChanges();  // Mengambil perubahan yang terjadi pada model

            // Ambil nilai lama dan baru
            $oldValues = [];
            $newValues = [];
            foreach ($changes as $key => $newValue) {
                $oldValues[$key] = $barangmasuk->getOriginal($key);  // Nilai lama (before)
                $newValues[$key] = $newValue;                    // Nilai baru (after)
            }

            // Hanya mencatat perubahan pada atribut selain created_at dan updated_at
            $logText = "[$timestamp] User: $user\n";
            $logText .= "Barang Masuk ID: {$barangmasuk->id} - Updated\n";

            foreach ($newValues as $key => $newValue) {
                $oldValue = $oldValues[$key] ?? '-';
                if ($key !== 'created_at' && $key !== 'updated_at') {
                    $logText .= "$key: $oldValue ➝ $newValue\n";
                }
            }

            $logText .= "-------------------------------------\n";

            // Simpan ke file logs/barangmasuk_log.txt
            Storage::append('public/logs/barangmasuk_log.txt', $logText);
        });

        // Log saat barang masuk dihapus
        static::deleted(function ($barangmasuk) {
            $user = auth()->user() ? auth()->user()->name : 'System';
            $timestamp = now()->format('Y-m-d H:i:s');

            // Ambil data barangmasuk sebelum dihapus
            $logText = "[$timestamp] User: $user\n";
            $logText .= "Barang Masuk ID: {$barangmasuk->id} - Deleted\n";
            $logText .= "Kode Transaksi: {$barangmasuk->kode_transaksi}\n";
            $logText .= "Lot: {$barangmasuk->lot}\n";
            $logText .= "Tanggal Masuk: {$barangmasuk->tanggal_masuk}\n";
            $logText .= "Tanggal Kadaluarsa: {$barangmasuk->tanggal_kadaluarsa}\n";
            $logText .= "Jumlah Masuk: {$barangmasuk->jumlah_masuk}\n";
            $logText .= "Outstanding: {$barangmasuk->outstanding}\n";
            $logText .= "Jumlah Stok: {$barangmasuk->jumlah_stok}\n";
            $logText .= "Harga: {$barangmasuk->harga}\n";
            $logText .= "Lokasi: {$barangmasuk->lokasi}\n";
            $logText .= "Supplier: {$barangmasuk->supplier->supplier}\n";
            $logText .= "Barang: {$barangmasuk->barang->nama_barang}\n";
            $logText .= "Approved: {$barangmasuk->approved}\n";
            $logText .= "-------------------------------------\n";

            // Simpan ke file logs/barangmasuk_log.txt
            Storage::append('public/logs/barangmasuk_log.txt', $logText);
        });
    }
}
