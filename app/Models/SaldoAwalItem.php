<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class SaldoAwalItem extends Model
{
    use HasFactory;
    use LogsActivity;

    protected $fillable = ['tanggal', 'barang_id', 'jumlah', 'harga', 'lokasi'];
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
            ->setDescriptionForEvent(fn(string $eventName) => "Saldo Awal Item {$eventName}")
            ->useLogName('saldo awal item');
    }

    // Log saat model Saldo Awal Item di-create, di-update, dan di-delete
    public static function boot()
    {
        parent::boot();

        // Log saat saldo awal item baru dibuat
        static::created(function ($saldoAwalItem) {
            $user = auth()->user() ? auth()->user()->name : 'System';
            $timestamp = now()->format('Y-m-d H:i:s');

            $logText = "[$timestamp] User: $user\n";
            $logText .= "Saldo Awal Item ID: {$saldoAwalItem->id} - Created\n";
            $logText .= "Tanggal: {$saldoAwalItem->tanggal}\n";
            $logText .= "Barang: {$saldoAwalItem->barang->nama_barang}\n";
            $logText .= "Jumlah: {$saldoAwalItem->jumlah}\n";
            $logText .= "Harga: {$saldoAwalItem->harga}\n";
            $logText .= "Lokasi: {$saldoAwalItem->lokasi}\n";
            $logText .= "-------------------------------------\n";

            // Simpan ke file logs/saldo_awal_item_log.txt
            Storage::append('public/logs/saldo_awal_item_log.txt', $logText);
        });

        // Log saat saldo awal item diupdate
        static::updated(function ($saldoAwalItem) {
            $user = auth()->user() ? auth()->user()->name : 'System';
            $timestamp = now()->format('Y-m-d H:i:s');

            // Dapatkan perubahan atribut (old dan new)
            $changes = $saldoAwalItem->getChanges();

            $logText = "[$timestamp] User: $user\n";
            $logText .= "Saldo Awal Item ID: {$saldoAwalItem->id} - Updated\n";
            foreach ($changes as $key => $values) {
                $logText .= "[$key] {$values['old']} -> {$values['new']}\n";
            }
            $logText .= "-------------------------------------\n";

            // Simpan ke file logs/saldo_awal_item_log.txt
            Storage::append('public/logs/saldo_awal_item_log.txt', $logText);
        });

        // Log saat saldo awal item dihapus
        static::deleted(function ($saldoAwalItem) {
            $user = auth()->user() ? auth()->user()->name : 'System';
            $timestamp = now()->format('Y-m-d H:i:s');

            $logText = "[$timestamp] User: $user\n";
            $logText .= "Saldo Awal Item ID: {$saldoAwalItem->id} - Deleted\n";
            $logText .= "Tanggal: {$saldoAwalItem->tanggal}\n";
            $logText .= "Barang: {$saldoAwalItem->barang->nama_barang}\n";
            $logText .= "Jumlah: {$saldoAwalItem->jumlah}\n";
            $logText .= "Harga: {$saldoAwalItem->harga}\n";
            $logText .= "Lokasi: {$saldoAwalItem->lokasi}\n";
            $logText .= "-------------------------------------\n";

            // Simpan ke file logs/saldo_awal_item_log.txt
            Storage::append('public/logs/saldo_awal_item_log.txt', $logText);
        });
    }
}
