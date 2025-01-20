<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\StokOpname;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
        $validator = Validator::make($request->all(), [
            'barang_id' => 'required|exists:barangs,id',
            'stok_aktual' => 'required|integer|min:0',
        ], [
            'barang_id.required' => 'Barang harus dipilih.',
            'barang_id.exists' => 'Barang tidak ditemukan.',
            'stok_aktual.required' => 'Stok aktual harus diisi.',
            'stok_aktual.integer' => 'Stok aktual harus berupa angka.',
            'stok_aktual.min' => 'Stok aktual tidak boleh kurang dari 0.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $barang = Barang::findOrFail($request->barang_id);
        $stokSistem = $barang->stok;

        $stokOpname = StokOpname::create([
            'barang_id' => $request->barang_id,
            'stok_aktual' => $request->stok_aktual,
            'stok_sistem' => $stokSistem,
            'keterangan' => $request->keterangan,
            'adjusted' => false,
            'user_id' => auth()->user()->id,
        ]);

        return redirect()->back()->with('success', 'Stok opname berhasil disimpan.');
    }

    public function stokAdjustment(Request $request)
    {
        $stokAdjustments = StokOpname::where('adjusted', false)->latest()->get();
        
        $adjustedStokOpnames = StokOpname::where('adjusted', true)->latest()->get();

        return view('stok-adjustment.index', [
            'stokAdjustments' => $stokAdjustments,
            'adjustedStokOpnames' => $adjustedStokOpnames,
        ]);
    }

    public function adjust(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'stok_opname_id' => 'required|exists:stok_opnames,id',
        ], [
            'stok_opname_id.required' => 'Stok opname harus dipilih.',
            'stok_opname_id.exists' => 'Stok opname tidak ditemukan.',
        ]);

        $stokOpname = StokOpname::findOrFail($request->stok_opname_id);

        $stokOpname->adjusted = true;
        $stokOpname->save();

        // Update stok barang
        $barang = Barang::find($stokOpname->barang_id);
        $barang->stok = $stokOpname->stok_aktual;
        $barang->save();

        return redirect()->back()->with('success', 'Stok berhasil disesuaikan.');
    }

    public function getDataStok(Request $request)
    {
        $barang = Barang::where('id', $request->barang_id)->first();

        return response()->json($barang);
    }

    public function getDataStokOpname(Request $request)
    {
        $stokOpname = StokOpname::where('id', $request->stok_opname_id)->first();

        return response()->json($stokOpname);
    }

}
