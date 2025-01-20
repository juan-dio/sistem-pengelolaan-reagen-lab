<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\StokOpname;
use Illuminate\Http\Request;

class LaporanStokOpnameController extends Controller
{
    public function index()
    {
        return view('laporan-stok-opname.index');
    }

    public function getData(Request $request)
    {
        $tanggalMulai = $request->input('tanggal_mulai');
        $tanggalSelesai = $request->input('tanggal_selesai');
    
        $stokOpname = StokOpname::query();
    
        if ($tanggalMulai && $tanggalSelesai) {
            $stokOpname->whereBetween('created_at', [$tanggalMulai, $tanggalSelesai]);
        }
    
        $data = $stokOpname->get();

        if (empty($tanggalMulai) && empty($tanggalSelesai)) {
            $data = StokOpname::all();
        }
    
        return response()->json($data);
    }

    public function printStokOpname(Request $request)
    {
        $tanggalMulai = $request->input('tanggal_mulai');
        $tanggalSelesai = $request->input('tanggal_selesai');
    
        $stokOpname = StokOpname::query();
    
        if ($tanggalMulai && $tanggalSelesai) {
            $stokOpname->whereBetween('created_at', [$tanggalMulai, $tanggalSelesai]);
        }
    
        if ($tanggalMulai !== null && $tanggalSelesai !== null) {
            $data = $stokOpname->get();
        } else {
            $data = StokOpname::all();
        }

        return view('laporan-stok-opname.print-stok-opname', compact('data', 'tanggalMulai', 'tanggalSelesai'));
    }
}
