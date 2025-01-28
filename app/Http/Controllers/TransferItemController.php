<?php

namespace App\Http\Controllers;

use App\Models\TransferItem;
use App\Http\Controllers\Controller;
use App\Models\BarangMasuk;
use Illuminate\Http\Request;

class TransferItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('transfer-item.index', [
            'transferItems' => TransferItem::all(),
            'barangMasuks' => BarangMasuk::where('approved', 1)->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(TransferItem $transferItem)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TransferItem $transferItem)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TransferItem $transferItem)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TransferItem $transferItem)
    {
        //
    }

    public function getDataLokasi(Request $request)
    {
        $barangMasuk = BarangMasuk::where('id', $request->barang_masuk_id)->first();

        if ($barangMasuk) {
            return response()->json([
                'lokasi' => $barangMasuk->lokasi,
            ]);
        }

        return response()->json([
            'message' => 'Data Tidak Ditemukan !'
        ]);
    }
}
