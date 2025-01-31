<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Alat extends Model
{
    use HasFactory;
    use LogsActivity;

    protected $fillable = ['alat', 'user_id'];
    protected $guarded = [''];

    protected $ignoreChangedAttributes = ['updated_at'];

    // 1 custommer memiliki banyak barangKeluar
    public function BarangKeluars()
    {
        return $this->hasMany(BarangKeluar::class);
    }

    // Activity Log
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logUnguarded()
            ->setDescriptionForEvent(fn(string $eventName) => "Alat {$eventName}")
            ->useLogName('alat');
    }

    // Log saat model Alat di-create, di-update, dan di-delete
    public static function boot()
    {
        parent::boot();

        // Log saat alat baru dibuat
        static::created(function ($alat) {
            $user = auth()->user() ? auth()->user()->name : 'System';
            $timestamp = now()->format('Y-m-d H:i:s');

            $logText = "[$timestamp] User: $user\n";
            $logText .= "Alat ID: {$alat->id} - Created\n";
            $logText .= "Alat: {$alat->alat}\n";
            $logText .= "-------------------------------------\n";

            // Simpan ke file logs/alat_log.txt
            Storage::append('public/logs/alat_log.txt', $logText);
        });

        // Log saat alat diupdate
        static::updated(function ($alat) {
            $user = auth()->user() ? auth()->user()->name : 'System';
            $timestamp = now()->format('Y-m-d H:i:s');

            // Dapatkan perubahan atribut (old dan new)
            $changes = $alat->getChanges();  // Mengambil perubahan yang terjadi pada model

            // Ambil nilai lama dan baru
            $oldValues = [];
            $newValues = [];
            foreach ($changes as $key => $newValue) {
                $oldValues[$key] = $alat->getOriginal($key);  // Nilai lama (before)
                $newValues[$key] = $newValue;                    // Nilai baru (after)
            }

            // Hanya mencatat perubahan pada atribut selain created_at dan updated_at
            $logText = "[$timestamp] User: $user\n";
            $logText .= "Alat ID: {$alat->id} - Updated\n";

            foreach ($newValues as $key => $newValue) {
                $oldValue = $oldValues[$key] ?? '-';
                if ($key !== 'created_at' && $key !== 'updated_at') {
                    $logText .= "$key: $oldValue ➝ $newValue\n";
                }
            }

            $logText .= "-------------------------------------\n";

            // Simpan ke file logs/alat_log.txt
            Storage::append('public/logs/alat_log.txt', $logText);
        });

        // Log saat alat dihapus
        static::deleted(function ($alat) {
            $user = auth()->user() ? auth()->user()->name : 'System';
            $timestamp = now()->format('Y-m-d H:i:s');

            // Ambil data alat sebelum dihapus
            $logText = "[$timestamp] User: $user\n";
            $logText .= "Alat ID: {$alat->id} - Deleted\n";
            $logText .= "Alat: {$alat->alat}\n";
            $logText .= "-------------------------------------\n";

            // Simpan ke file logs/alat_log.txt
            Storage::append('public/logs/alat_log.txt', $logText);
        });
    }
}
