<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class BarangKeluar extends Model
{
    use HasFactory;
    use LogsActivity;

    protected $fillable = ['kode_transaksi', 'tanggal_keluar', 'jumlah_keluar', 'barang_id', 'alat_id', 'user_id'];
    protected $guarded = [''];
    protected $ignoreChangedAttributes = ['updated_at'];

    public function getActivitylogAttributes(): array
    {
        return array_diff($this->fillable, $this->ignoreChangedAttributes);
    }    

    // Activity Log
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logUnguarded()
            ->logOnlyDirty();
    }

    // 1 barang keluar memiliki satu barang masuk
    public function barang()
    {
        return $this->belongsTo(BarangMasuk::class, 'barang_id');
    }

    // 1 barang keluar hanya memiliki satu alat
    public function alat()
    {
        return $this->belongsTo(Alat::class, 'alat_id');
    }
}
