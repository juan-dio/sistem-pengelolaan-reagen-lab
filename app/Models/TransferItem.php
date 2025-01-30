<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
}
