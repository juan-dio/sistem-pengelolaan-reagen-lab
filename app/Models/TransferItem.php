<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class TransferItem extends Model
{
    use HasFactory;
    use LogsActivity;

    protected $fillable = ['previous_location', 'new_location', 'keterangan', 'approved', 'barang_masuk_id', 'user_id'];
    protected $with = ['barang_masuk', 'user'];
    protected $guarded = [''];
    protected $ignoreChangedAttributes = ['updated_at'];

    // Satu Barang dimiliki oleh 1 User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Satu Barang memiliki 1 jenis
    public function barang_masuk()
    {
        return $this->belongsTo(BarangMasuk::class, 'barang_masuk_id');
    }

    // Activity Log
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logUnguarded()
            ->setDescriptionForEvent(fn(string $eventName) => "Transfer Item {$eventName}")
            ->useLogName('transfer item');
    }

    // Log saat model Transfer item di-create, di-update, dan di-delete
    public static function boot()
    {
        parent::boot();

        // Log saat transfer item baru dibuat
        static::created(function ($transferitem) {
            $user = auth()->user() ? auth()->user()->name : 'System';
            $timestamp = now()->format('Y-m-d H:i:s');

            $logText = "[$timestamp] User: $user\n";
            $logText .= "Transfer Item ID: {$transferitem->id} - Created\n";
            $logText .= "Barang Masuk: {$transferitem->barang_masuk->kode_transaksi}\n";
            $logText .= "Lokasi Sebelumnya: {$transferitem->previous_location}\n";
            $logText .= "Lokasi Baru: {$transferitem->new_location}\n";
            $logText .= "Keterangan: {$transferitem->keterangan}\n";
            $logText .= "-------------------------------------\n";

            // Simpan ke file logs/transferitem_log.txt
            Storage::append('public/logs/transferitem_log.txt', $logText);
        });

        // Log saat transfer item diupdate
        static::updated(function ($transferitem) {
            $user = auth()->user() ? auth()->user()->name : 'System';
            $timestamp = now()->format('Y-m-d H:i:s');

            // Dapatkan perubahan atribut (old dan new)
            $changes = $transferitem->getChanges();  // Mengambil perubahan yang terjadi pada model

            // Ambil nilai lama dan baru
            $oldValues = [];
            $newValues = [];
            foreach ($changes as $key => $newValue) {
                $oldValues[$key] = $transferitem->getOriginal($key);  // Nilai lama (before)
                $newValues[$key] = $newValue;                    // Nilai baru (after)
            }

            // Hanya mencatat perubahan pada atribut selain created_at dan updated_at
            $logText = "[$timestamp] User: $user\n";
            $logText .= "Transfer Item ID: {$transferitem->id} - Updated\n";

            foreach ($newValues as $key => $newValue) {
                $oldValue = $oldValues[$key] ?? '-';
                if ($key !== 'created_at' && $key !== 'updated_at') {
                    $logText .= "$key: $oldValue ➝ $newValue\n";
                }
            }

            $logText .= "-------------------------------------\n";

            // Simpan ke file logs/transferitem_log.txt
            Storage::append('public/logs/transferitem_log.txt', $logText);
        });

        // Log saat transfer item dihapus
        static::deleted(function ($transferitem) {
            $user = auth()->user() ? auth()->user()->name : 'System';
            $timestamp = now()->format('Y-m-d H:i:s');

            // Ambil data transferitem sebelum dihapus
            $logText = "[$timestamp] User: $user\n";
            $logText .= "Transfer Item ID: {$transferitem->id} - Deleted\n";
            $logText .= "Barang Masuk: {$transferitem->barang_masuk->kode_transaksi}\n";
            $logText .= "Lokasi Sebelumnya: {$transferitem->previous_location}\n";
            $logText .= "Lokasi Baru: {$transferitem->new_location}\n";
            $logText .= "Keterangan: {$transferitem->keterangan}\n";
            $logText .= "-------------------------------------\n";

            // Simpan ke file logs/transferitem_log.txt
            Storage::append('public/logs/transferitem_log.txt', $logText);
        });
    }
}
