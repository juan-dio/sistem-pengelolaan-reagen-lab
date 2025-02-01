<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;
use App\Models\BarangKeluar;
use App\Models\BarangMasuk;
use Carbon\Carbon;

class RekapitulasiController extends Controller
{
    public function index()
    {
        // Ambil data rekapitulasi barang
        $barangRekap = Barang::with(['jenis', 'satuan'])
            ->select('id', 'kode_barang', 'nama_barang', 'stok', 'stok_minimum', 'jenis_id', 'satuan_id')
            ->get();

        // Ambil data barang masuk dan keluar
        $barangMasuk = BarangMasuk::selectRaw('barang_id, SUM(jumlah_masuk) as total_masuk, SUM(outstanding) as total_outstanding')
            ->groupBy('barang_id')
            ->get()
            ->keyBy('barang_id');

        $barangKeluar = BarangKeluar::selectRaw('barang_id, SUM(jumlah_keluar) as total_keluar')
            ->groupBy('barang_id')
            ->get()
            ->keyBy('barang_id');

        return view('rekapitulasi.index', compact('barangRekap', 'barangMasuk', 'barangKeluar'));
    }
}
