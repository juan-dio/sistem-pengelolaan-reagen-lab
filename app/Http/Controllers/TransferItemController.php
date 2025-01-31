<?php

namespace App\Http\Controllers;

use App\Models\TransferItem;
use App\Http\Controllers\Controller;
use App\Models\BarangMasuk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
        $validator = Validator::make($request->all(), [
            'barang_masuk_id' => 'required|exists:barang_masuks,id',
            'previous_location' => 'required',
            'new_location' => 'required',
        ], [
            'barang_masuk_id.required' => 'Barang masuk harus dipilih.',
            'barang_masuk_id.exists' => 'Barang masuk tidak ditemukan.',
            'previous_location.required' => 'Lokasi sebelumnya harus diisi.',
            'new_location.required' => 'Lokasi baru harus diisi.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $transferItem = TransferItem::create([
            'barang_masuk_id' => $request->barang_masuk_id,
            'previous_location' => $request->previous_location,
            'new_location' => $request->new_location,
            'keterangan' => $request->keterangan,
            'user_id' => auth()->user()->id,
        ]);

        return redirect()->back()->with('success', 'Transfer item berhasil disimpan.');
    }

    public function approve(TransferItem $transferItem)
    {
        $transferItem->barang_masuk->update([
            'lokasi' => $transferItem->new_location,
        ]);

        $transferItem->update([
            'approved' => 1,
        ]);

        return redirect()->back()->with('success', 'Transfer item berhasil disetujui.');
    }

    public function approveAll(Request $request)
    {
        $transferItems = TransferItem::where('approved', 0)->get();

        foreach ($transferItems as $transferItem) {
            $transferItem->barang_masuk->update([
                'lokasi' => $transferItem->new_location,
            ]);

            $transferItem->update([
                'approved' => 1,
            ]);
        }

        return redirect()->back()->with('success', 'Semua transfer item berhasil disetujui.');
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
        if($transferItem->approved == 1) {
            return redirect()->back()->with('error', 'Transfer item yang sudah disetujui tidak dapat dihapus.');
        }

        $transferItem->delete();

        return redirect()->back()->with('success', 'Transfer item berhasil dihapus.');
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
