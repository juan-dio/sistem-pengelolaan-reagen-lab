<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Satuan extends Model
{
    use HasFactory;
    use LogsActivity;

    protected $fillable = ['satuan', 'user_id'];
    protected $guarded = [''];
    protected $ignoreChangedAttributes = ['updated_at'];
    
    // 1 satuan, dimiliki oleh banyak barang
    public function barangs()
    {
        return $this->hasMany(Barang::class);
    }

    // Activity Log
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logUnguarded() // Log semua atribut
            ->setDescriptionForEvent(fn(string $eventName) => "Satuan {$eventName}")
            ->useLogName('satuan'); // Nama log khusus untuk tabel barang
    }

    // Log saat model Satuan di-create, di-update, dan di-delete
    public static function boot()
    {
        parent::boot();

        // Log saat satuan baru dibuat
        static::created(function ($satuan) {
            $user = auth()->user() ? auth()->user()->name : 'System';
            $timestamp = now()->format('Y-m-d H:i:s');

            $logText = "[$timestamp] User: $user\n";
            $logText .= "Satuan ID: {$satuan->id} - Created\n";
            $logText .= "Satuan: {$satuan->satuan}\n";
            $logText .= "-------------------------------------\n";

            // Simpan ke file logs/satuan_log.txt
            Storage::append('public/logs/satuan_log.txt', $logText);
        });

        // Log saat satuan diupdate
        static::updated(function ($satuan) {
            $user = auth()->user() ? auth()->user()->name : 'System';
            $timestamp = now()->format('Y-m-d H:i:s');

            // Dapatkan perubahan atribut (old dan new)
            $changes = $satuan->getChanges();  // Mengambil perubahan yang terjadi pada model

            // Ambil nilai lama dan baru
            $oldValues = [];
            $newValues = [];
            foreach ($changes as $key => $newValue) {
                $oldValues[$key] = $satuan->getOriginal($key);  // Nilai lama (before)
                $newValues[$key] = $newValue;                    // Nilai baru (after)
            }

            // Hanya mencatat perubahan pada atribut selain created_at dan updated_at
            $logText = "[$timestamp] User: $user\n";
            $logText .= "Satuan ID: {$satuan->id} - Updated\n";

            foreach ($newValues as $key => $newValue) {
                $oldValue = $oldValues[$key] ?? '-';
                if ($key !== 'created_at' && $key !== 'updated_at') {
                    $logText .= "$key: $oldValue ➝ $newValue\n";
                }
            }

            $logText .= "-------------------------------------\n";

            // Simpan ke file logs/satuan_log.txt
            Storage::append('public/logs/satuan_log.txt', $logText);
        });

        // Log saat satuan dihapus
        static::deleted(function ($satuan) {
            $user = auth()->user() ? auth()->user()->name : 'System';
            $timestamp = now()->format('Y-m-d H:i:s');

            // Ambil data satuan sebelum dihapus
            $logText = "[$timestamp] User: $user\n";
            $logText .= "Satuan ID: {$satuan->id} - Deleted\n";
            $logText .= "Satuan: {$satuan->satuan}\n";
            $logText .= "-------------------------------------\n";

            // Simpan ke file logs/satuan_log.txt
            Storage::append('public/logs/satuan_log.txt', $logText);
        });
    }
}
