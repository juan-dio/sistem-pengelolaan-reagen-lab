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
            'stok_fisik' => 'required|integer|min:0',
        ], [
            'barang_id.required' => 'Barang harus dipilih.',
            'barang_id.exists' => 'Barang tidak ditemukan.',
            'stok_fisik.required' => 'Stok aktual harus diisi.',
            'stok_fisik.integer' => 'Stok aktual harus berupa angka.',
            'stok_fisik.min' => 'Stok aktual tidak boleh kurang dari 0.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $barang = Barang::findOrFail($request->barang_id);
        $stokSistem = $barang->stok;

        $stokOpname = StokOpname::create([
            'barang_id' => $request->barang_id,
            'stok_fisik' => $request->stok_fisik,
            'stok_sistem' => $stokSistem,
            'keterangan' => $request->keterangan,
            'user_id' => auth()->user()->id,
        ]);

        return redirect()->back()->with('success', 'Stok opname berhasil disimpan.');
    }

    public function approve(Request $request, StokOpname $stokOpname)
    {

        $stokOpname->approved = true;
        $stokOpname->save();

        return redirect()->back()->with('success', 'Stok opname berhasil disetujui!');
    }

    public function approveAll(Request $request)
    {
        $stokOpnames = StokOpname::where('approved', false)->get();

        foreach ($stokOpnames as $stokOpname) {
            $stokOpname->approved = true;
            $stokOpname->save();
        }

        return redirect()->back()->with('success', 'Semua stok opname berhasil disetujui!');
    }

    public function stokAdjustment(Request $request)
    {
        $stokAdjustments = StokOpname::where('adjusted', false)->where('approved', 1)->latest()->get();
        
        $adjustedStokOpnames = StokOpname::where('adjusted', true)->latest()->get();

        return view('stok-adjustment.index', [
            'stokAdjustments' => $stokAdjustments,
            'adjustedStokOpnames' => $adjustedStokOpnames,
        ]);
    }

    public function adjust(Request $request)
    {
        $stokOpname = StokOpname::findOrFail($request->stok_opname_id);

        if ($stokOpname->approved == 0) {
            return redirect()->back()->with('error', 'Stok opname belum disetujui.');
        }

        $stokOpname->adjusted = true;
        $stokOpname->save();

        // Update stok barang
        $barang = Barang::find($stokOpname->barang_id);
        $barang->stok = $stokOpname->stok_fisik;
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
