<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\StokOpname;
use Illuminate\Http\Request;

class StokOpnameController extends Controller
{
    public function index()
    {
        return view('stok-opname.index', [
            'stokOpnames' => StokOpname::latest()->get(),
            'barangs' => Barang::all()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'barang_id' => 'required|exists:barangs,id',
            'stok_aktual' => 'required|integer|min:0',
        ]);

        $barang = Barang::findOrFail($request->barang_id);
        $stokSistem = $barang->stok;

        $stokOpname = StokOpname::create([
            'barang_id' => $request->barang_id,
            'stok_aktual' => $request->stok_aktual,
            'stok_sistem' => $stokSistem,
            'keterangan' => $request->keterangan,
            'user_id' => auth()->user()->id,
        ]);

        // Update stok barang jika diperlukan
        $barang->stok = $request->stok_aktual;
        $barang->save();

        return redirect()->back()->with('success', 'Stok opname berhasil disimpan.');
    }
}
