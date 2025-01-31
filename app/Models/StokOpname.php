<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class StokOpname extends Model
{
    use HasFactory;
    use LogsActivity;

    protected $fillable = ['barang_id', 'stok_fisik', 'stok_sistem', 'keterangan', 'user_id'];
    protected $guarded = [''];
    protected $ignoreChangedAttributes = ['updated_at'];
    protected $with = ['barang'];

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }

    // Activity Log
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logUnguarded()
            ->setDescriptionForEvent(fn(string $eventName) => "Stok Opname {$eventName}")
            ->useLogName('stok opname');
    }

    // Log saat model Stok Opname di-create, di-update, dan di-delete
    public static function boot()
    {
        parent::boot();

        // Log saat stok opname baru dibuat
        static::created(function ($stokopname) {
            $user = auth()->user() ? auth()->user()->name : 'System';
            $timestamp = now()->format('Y-m-d H:i:s');

            $logText = "[$timestamp] User: $user\n";
            $logText .= "Stok Opname ID: {$stokopname->id} - Created\n";
            $logText .= "Barang: {$stokopname->barang->nama_barang}\n";
            $logText .= "Stok Fisik: {$stokopname->stok_fisik}\n";
            $logText .= "Stok Sistem: {$stokopname->stok_sistem}\n";
            $logText .= "Keterangan: {$stokopname->keterangan}\n";
            $logText .= "-------------------------------------\n";

            // Simpan ke file logs/stokopname_log.txt
            Storage::append('public/logs/stokopname_log.txt', $logText);
        });

        // Log saat stok opname diupdate
        static::updated(function ($stokopname) {
            $user = auth()->user() ? auth()->user()->name : 'System';
            $timestamp = now()->format('Y-m-d H:i:s');

            // Dapatkan perubahan atribut (old dan new)
            $changes = $stokopname->getChanges();  // Mengambil perubahan yang terjadi pada model

            // Ambil nilai lama dan baru
            $oldValues = [];
            $newValues = [];
            foreach ($changes as $key => $newValue) {
                $oldValues[$key] = $stokopname->getOriginal($key);  // Nilai lama (before)
                $newValues[$key] = $newValue;                    // Nilai baru (after)
            }

            // Hanya mencatat perubahan pada atribut selain created_at dan updated_at
            $logText = "[$timestamp] User: $user\n";
            $logText .= "Stok Opname ID: {$stokopname->id} - Updated\n";

            foreach ($newValues as $key => $newValue) {
                $oldValue = $oldValues[$key] ?? '-';
                if ($key !== 'created_at' && $key !== 'updated_at') {
                    $logText .= "$key: $oldValue ➝ $newValue\n";
                }
            }

            $logText .= "-------------------------------------\n";

            // Simpan ke file logs/stokopname_log.txt
            Storage::append('public/logs/stokopname_log.txt', $logText);
        });

        // Log saat stok opname dihapus
        static::deleted(function ($stokopname) {
            $user = auth()->user() ? auth()->user()->name : 'System';
            $timestamp = now()->format('Y-m-d H:i:s');

            // Ambil data stokopname sebelum dihapus
            $logText = "[$timestamp] User: $user\n";
            $logText .= "Stok Opname ID: {$stokopname->id} - Deleted\n";
            $logText .= "Barang: {$stokopname->barang->nama_barang}\n";
            $logText .= "Stok Fisik: {$stokopname->stok_fisik}\n";
            $logText .= "Stok Sistem: {$stokopname->stok_sistem}\n";
            $logText .= "Keterangan: {$stokopname->keterangan}\n";
            $logText .= "-------------------------------------\n";

            // Simpan ke file logs/stokopname_log.txt
            Storage::append('public/logs/stokopname_log.txt', $logText);
        });
    }
}
