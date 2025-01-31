<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class BarangKeluar extends Model
{
    use HasFactory;
    use LogsActivity;

    protected $fillable = ['kode_transaksi', 'tanggal_keluar', 'jumlah_keluar', 'approved', 'barang_id', 'alat_id', 'user_id'];
    protected $guarded = [''];
    protected $ignoreChangedAttributes = ['updated_at'];
    protected $with = ['barang', 'alat'];

    // 1 barang keluar memiliki satu barang masuk
    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }

    // 1 barang keluar hanya memiliki satu alat
    public function alat()
    {
        return $this->belongsTo(Alat::class, 'alat_id');
    }

    // Activity Log
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logUnguarded()
            ->setDescriptionForEvent(fn(string $eventName) => "Barang Keluar {$eventName} dengan kode {$this->kode_transaksi}")
            ->useLogName('barang keluar');
    }

    // Log saat model Barang Keluar di-create, di-update, dan di-delete
    public static function boot()
    {
        parent::boot();

        // Log saat barang keluar baru dibuat
        static::created(function ($barangkeluar) {
            $user = auth()->user() ? auth()->user()->name : 'System';
            $timestamp = now()->format('Y-m-d H:i:s');

            $logText = "[$timestamp] User: $user\n";
            $logText .= "Barang Keluar ID: {$barangkeluar->id} - Created\n";
            $logText .= "Kode Transaksi: {$barangkeluar->kode_transaksi}\n";
            $logText .= "Tanggal Keluar: {$barangkeluar->tanggal_keluar}\n";
            $logText .= "Jumlah Keluar: {$barangkeluar->jumlah_keluar}\n";
            $logText .= "Barang: {$barangkeluar->barang->nama_barang}\n";
            $logText .= "Alat: {$barangkeluar->alat->alat}\n";
            $logText .= "-------------------------------------\n";

            // Simpan ke file logs/barangkeluar_log.txt
            Storage::append('public/logs/barangkeluar_log.txt', $logText);
        });

        // Log saat barang keluar diupdate
        static::updated(function ($barangkeluar) {
            $user = auth()->user() ? auth()->user()->name : 'System';
            $timestamp = now()->format('Y-m-d H:i:s');

            // Dapatkan perubahan atribut (old dan new)
            $changes = $barangkeluar->getChanges();  // Mengambil perubahan yang terjadi pada model

            // Ambil nilai lama dan baru
            $oldValues = [];
            $newValues = [];
            foreach ($changes as $key => $newValue) {
                $oldValues[$key] = $barangkeluar->getOriginal($key);  // Nilai lama (before)
                $newValues[$key] = $newValue;                    // Nilai baru (after)
            }

            // Hanya mencatat perubahan pada atribut selain created_at dan updated_at
            $logText = "[$timestamp] User: $user\n";
            $logText .= "Barang Keluar ID: {$barangkeluar->id} - Updated\n";

            foreach ($newValues as $key => $newValue) {
                $oldValue = $oldValues[$key] ?? '-';
                if ($key !== 'created_at' && $key !== 'updated_at') {
                    $logText .= "$key: $oldValue ➝ $newValue\n";
                }
            }

            $logText .= "-------------------------------------\n";

            // Simpan ke file logs/barangkeluar_log.txt
            Storage::append('public/logs/barangkeluar_log.txt', $logText);
        });

        // Log saat barang keluar dihapus
        static::deleted(function ($barangkeluar) {
            $user = auth()->user() ? auth()->user()->name : 'System';
            $timestamp = now()->format('Y-m-d H:i:s');

            // Ambil data barangkeluar sebelum dihapus
            $logText = "[$timestamp] User: $user\n";
            $logText .= "Barang Keluar ID: {$barangkeluar->id} - Deleted\n";
            $logText .= "Kode Transaksi: {$barangkeluar->kode_transaksi}\n";
            $logText .= "Tanggal Keluar: {$barangkeluar->tanggal_keluar}\n";
            $logText .= "Jumlah Keluar: {$barangkeluar->jumlah_keluar}\n";
            $logText .= "Barang: {$barangkeluar->barang->nama_barang}\n";
            $logText .= "Alat: {$barangkeluar->alat->alat}\n";
            $logText .= "-------------------------------------\n";

            // Simpan ke file logs/barangkeluar_log.txt
            Storage::append('public/logs/barangkeluar_log.txt', $logText);
        });
    }
}
