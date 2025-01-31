<?php

namespace App\Models;

use App\Models\User;
use App\Models\Jenis;
use App\Models\Satuan;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Barang extends Model
{
    use HasFactory;
    use LogsActivity;

    protected $fillable = ['kode_barang', 'nama_barang', 'deskripsi', 'gambar', 'stok_minimum', 'jenis_id', 'stok', 'test_group', 'satuan_id', 'user_id'];
    protected $with = ['jenis', 'satuan'];
    protected $guarded = [''];
    protected $ignoreChangedAttributes = ['updated_at'];

    // Satu Barang dimiliki oleh 1 User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Satu Barang memiliki 1 jenis
    public function jenis()
    {
        return $this->belongsTo(Jenis::class, 'jenis_id');
    }

    // Satu Barang memiliki 1 satuan
    public function satuan()
    {
        return $this->belongsTo(Satuan::class, 'satuan_id');
    }

    // Activity Log
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logUnguarded() // Log semua atribut
            ->setDescriptionForEvent(fn(string $eventName) => "Barang {$eventName} dengan kode {$this->kode_barang}")
            ->useLogName('barang'); // Nama log khusus untuk tabel barang
    }

    // Log saat model Barang di-create, di-update, dan di-delete
    public static function boot()
    {
        parent::boot();

        // Log saat barang baru dibuat
        static::created(function ($barang) {
            $user = auth()->user() ? auth()->user()->name : 'System';
            $timestamp = now()->format('Y-m-d H:i:s');

            $logText = "[$timestamp] User: $user\n";
            $logText .= "Barang ID: {$barang->id} - Created\n";
            $logText .= "Kode Barang: {$barang->kode_barang}\n";
            $logText .= "Nama Barang: {$barang->nama_barang}\n";
            $logText .= "Deskripsi: {$barang->deskripsi}\n";
            $logText .= "Gambar: {$barang->gambar}\n";
            $logText .= "Stok Minimum: {$barang->stok_minimum}\n";
            $logText .= "Jenis: {$barang->jenis->jenis_barang}\n";
            $logText .= "Stok: {$barang->stok}\n";
            $logText .= "Test Group: {$barang->test_group}\n";
            $logText .= "Satuan: {$barang->satuan->satuan}\n";
            $logText .= "-------------------------------------\n";

            // Simpan ke file logs/barang_log.txt
            Storage::append('public/logs/barang_log.txt', $logText);
        });

        // Log saat barang diupdate
        static::updated(function ($barang) {
            $user = auth()->user() ? auth()->user()->name : 'System';
            $timestamp = now()->format('Y-m-d H:i:s');

            // Dapatkan perubahan atribut (old dan new)
            $changes = $barang->getChanges();  // Mengambil perubahan yang terjadi pada model

            // Ambil nilai lama dan baru
            $oldValues = [];
            $newValues = [];
            foreach ($changes as $key => $newValue) {
                $oldValues[$key] = $barang->getOriginal($key);  // Nilai lama (before)
                $newValues[$key] = $newValue;                    // Nilai baru (after)
            }

            // Hanya mencatat perubahan pada atribut selain created_at dan updated_at
            $logText = "[$timestamp] User: $user\n";
            $logText .= "Barang ID: {$barang->id} - Updated\n";

            foreach ($newValues as $key => $newValue) {
                $oldValue = $oldValues[$key] ?? '-';
                if ($key !== 'created_at' && $key !== 'updated_at') {
                    $logText .= "$key: $oldValue ➝ $newValue\n";
                }
            }

            $logText .= "-------------------------------------\n";

            // Simpan ke file logs/barang_log.txt
            Storage::append('public/logs/barang_log.txt', $logText);
        });

        // Log saat barang dihapus
        static::deleted(function ($barang) {
            $user = auth()->user() ? auth()->user()->name : 'System';
            $timestamp = now()->format('Y-m-d H:i:s');

            // Ambil data barang sebelum dihapus
            $logText = "[$timestamp] User: $user\n";
            $logText .= "Barang ID: {$barang->id} - Deleted\n";
            $logText .= "Kode Barang: {$barang->kode_barang}\n";
            $logText .= "Nama Barang: {$barang->nama_barang}\n";
            $logText .= "Deskripsi: {$barang->deskripsi}\n";
            $logText .= "Gambar: {$barang->gambar}\n";
            $logText .= "Stok Minimum: {$barang->stok_minimum}\n";
            $logText .= "Jenis: {$barang->jenis->jenis_barang}\n";
            $logText .= "Stok: {$barang->stok}\n";
            $logText .= "Test Group: {$barang->test_group}\n";
            $logText .= "Satuan: {$barang->satuan->satuan}\n";
            $logText .= "-------------------------------------\n";

            // Simpan ke file logs/barang_log.txt
            Storage::append('public/logs/barang_log.txt', $logText);
        });
    }

}
