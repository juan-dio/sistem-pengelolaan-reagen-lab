<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\BarangMasuk;
use App\Models\BarangKeluar;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\StokOpname;
use App\Models\TransferItem;
use Illuminate\Auth\Events\Validated;
use Illuminate\Support\Facades\Validator;

class VerifikasiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('verifikasi.index', [
            'barangMasuk' => BarangMasuk::where('approved', 0)->get(),
            'barangKeluar' => BarangKeluar::where('approved', 0)->get(),
            'stokOpname' => StokOpname::where('approved', 0)->get(),
            'transferItem' => TransferItem::where('approved', 0)->get(),
        ]);
    }

}
