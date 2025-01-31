<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;


class Jenis extends Model
{
    use HasFactory;
    use LogsActivity;

    protected $fillable = ['jenis_barang', 'user_id'];
    protected $guarded = [''];
    protected $ignoreChangedAttributes = ['updated_at'];

    // 1 Jenis, dimiliki oleh banyak barang
    public function barangs()
    {
        return $this->hasMany(Barang::class);
    }

    // Activity Log
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logUnguarded()
            ->setDescriptionForEvent(fn(string $eventName) => "Jenis {$eventName}")
            ->useLogName('jenis');
    }

    // Log saat model Jenis di-create, di-update, dan di-delete
    public static function boot()
    {
        parent::boot();

        // Log saat jenis baru dibuat
        static::created(function ($jenis) {
            $user = auth()->user() ? auth()->user()->name : 'System';
            $timestamp = now()->format('Y-m-d H:i:s');

            $logText = "[$timestamp] User: $user\n";
            $logText .= "Jenis ID: {$jenis->id} - Created\n";
            $logText .= "Jenis: {$jenis->jenis_barang}\n";
            $logText .= "-------------------------------------\n";

            // Simpan ke file logs/jenis_log.txt
            Storage::append('public/logs/jenis_log.txt', $logText);
        });

        // Log saat jenis diupdate
        static::updated(function ($jenis) {
            $user = auth()->user() ? auth()->user()->name : 'System';
            $timestamp = now()->format('Y-m-d H:i:s');

            // Dapatkan perubahan atribut (old dan new)
            $changes = $jenis->getChanges();  // Mengambil perubahan yang terjadi pada model

            // Ambil nilai lama dan baru
            $oldValues = [];
            $newValues = [];
            foreach ($changes as $key => $newValue) {
                $oldValues[$key] = $jenis->getOriginal($key);  // Nilai lama (before)
                $newValues[$key] = $newValue;                    // Nilai baru (after)
            }

            // Hanya mencatat perubahan pada atribut selain created_at dan updated_at
            $logText = "[$timestamp] User: $user\n";
            $logText .= "Jenis ID: {$jenis->id} - Updated\n";

            foreach ($newValues as $key => $newValue) {
                $oldValue = $oldValues[$key] ?? '-';
                if ($key !== 'created_at' && $key !== 'updated_at') {
                    $logText .= "$key: $oldValue ➝ $newValue\n";
                }
            }

            $logText .= "-------------------------------------\n";

            // Simpan ke file logs/jenis_log.txt
            Storage::append('public/logs/jenis_log.txt', $logText);
        });

        // Log saat jenis dihapus
        static::deleted(function ($jenis) {
            $user = auth()->user() ? auth()->user()->name : 'System';
            $timestamp = now()->format('Y-m-d H:i:s');

            // Ambil data jenis sebelum dihapus
            $logText = "[$timestamp] User: $user\n";
            $logText .= "Jenis ID: {$jenis->id} - Deleted\n";
            $logText .= "Jenis: {$jenis->jenis_barang}\n";
            $logText .= "-------------------------------------\n";

            // Simpan ke file logs/jenis_log.txt
            Storage::append('public/logs/jenis_log.txt', $logText);
        });
    }
}
