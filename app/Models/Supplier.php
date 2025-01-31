<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Supplier extends Model
{
    use HasFactory;
    use LogsActivity;

    protected $fillable = ['supplier', 'alamat', 'user_id'];
    protected $guarded = [''];
    protected $ignoreChangedAttributes = ['updated_at'];

    // 1 Supplier memiliki banyak barangMasuk
    public function barangMasuks()
    {
        return $this->hasMany(BarangMasuk::class);
    }

    // Activity Log
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logUnguarded()
            ->setDescriptionForEvent(fn(string $eventName) => "Supplier {$eventName}")
            ->useLogName('supplier');
    }

    // Log saat model Supplier di-create, di-update, dan di-delete
    public static function boot()
    {
        parent::boot();

        // Log saat supplier baru dibuat
        static::created(function ($supplier) {
            $user = auth()->user() ? auth()->user()->name : 'System';
            $timestamp = now()->format('Y-m-d H:i:s');

            $logText = "[$timestamp] User: $user\n";
            $logText .= "Supplier ID: {$supplier->id} - Created\n";
            $logText .= "Supplier: {$supplier->supplier}\n";
            $logText .= "Alamat: {$supplier->alamat}\n";
            $logText .= "-------------------------------------\n";

            // Simpan ke file logs/supplier_log.txt
            Storage::append('public/logs/supplier_log.txt', $logText);
        });

        // Log saat supplier diupdate
        static::updated(function ($supplier) {
            $user = auth()->user() ? auth()->user()->name : 'System';
            $timestamp = now()->format('Y-m-d H:i:s');

            // Dapatkan perubahan atribut (old dan new)
            $changes = $supplier->getChanges();  // Mengambil perubahan yang terjadi pada model

            // Ambil nilai lama dan baru
            $oldValues = [];
            $newValues = [];
            foreach ($changes as $key => $newValue) {
                $oldValues[$key] = $supplier->getOriginal($key);  // Nilai lama (before)
                $newValues[$key] = $newValue;                    // Nilai baru (after)
            }

            // Hanya mencatat perubahan pada atribut selain created_at dan updated_at
            $logText = "[$timestamp] User: $user\n";
            $logText .= "Supplier ID: {$supplier->id} - Updated\n";

            foreach ($newValues as $key => $newValue) {
                $oldValue = $oldValues[$key] ?? '-';
                if ($key !== 'created_at' && $key !== 'updated_at') {
                    $logText .= "$key: $oldValue ➝ $newValue\n";
                }
            }

            $logText .= "-------------------------------------\n";

            // Simpan ke file logs/supplier_log.txt
            Storage::append('public/logs/supplier_log.txt', $logText);
        });

        // Log saat supplier dihapus
        static::deleted(function ($supplier) {
            $user = auth()->user() ? auth()->user()->name : 'System';
            $timestamp = now()->format('Y-m-d H:i:s');

            // Ambil data supplier sebelum dihapus
            $logText = "[$timestamp] User: $user\n";
            $logText .= "Supplier ID: {$supplier->id} - Deleted\n";
            $logText .= "Supplier: {$supplier->supplier}\n";
            $logText .= "Alamat: {$supplier->alamat}\n";
            $logText .= "-------------------------------------\n";

            // Simpan ke file logs/supplier_log.txt
            Storage::append('public/logs/supplier_log.txt', $logText);
        });
    }
}
