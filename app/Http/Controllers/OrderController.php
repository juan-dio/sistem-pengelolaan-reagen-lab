<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\BarangMasuk;
use App\Models\Order;
use Illuminate\Auth\Events\Validated;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('order.index', [
            'barangs'      => Barang::all(),
        ]);
    }

    public function getDataOrder()
    {
        return response()->json([
            'success'   => true,
            'data'      => Order::all(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // return view('barang-masuk.create', [
        //     'barangs'   => Barang::all()
        // ]);

        abort(404);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    { 
        $validator = Validator::make($request->all(), [
            'kode_transaksi'    => 'required|unique:orders,kode_transaksi',
            'tanggal'           => 'required|date',
            'barang_id'         => 'required|exists:barangs,id',
        ],[
            'kode_transaksi.required'   => 'Kode Transaksi Harus Diisi !',
            'kode_transaksi.unique'     => 'Kode Transaksi Sudah Ada !',
            'tanggal.required'          => 'Tanggal Harus Diisi !',
            'tanggal.date'              => 'Tanggal Harus Berupa Tanggal !',
            'barang_id.required'        => 'Barang Harus Diisi !',
            'barang_id.exists'          => 'Barang Tidak Ditemukan !',
        ]);

        if($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $order = Order::create([
            'kode_transaksi'    => $request->kode_transaksi,
            'tanggal'           => $request->tanggal,
            'status'            => 'proses kirim',
            'barang_id'         => $request->barang_id,
            'user_id'           => auth()->user()->id
        ]); 

        return response()->json([
            'success'   => true,
            'message'   => 'Data Berhasil Disimpan !',
            'data'      => $order
        ]);
    }

    public function edit(Order $order)
    {
        return response()->json([
            'success' => true,
            'message' => 'Edit Data Order',
            'data'    => $order
        ]);
    }

    public function update(Request $request, Order $order)
    {
        $validator = Validator::make($request->all(), [
            'status'            => 'required',
        ],[
            'status.required'   => 'Status Harus Diisi !',
        ]);

        if($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $order->update([
            'status'    => $request->status,
        ]);
    
        return response()->json([
            'success'   => true,
            'message'   => 'Status Berhasil Diubah!',
            'data'      => $order
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        $barangMasuk = BarangMasuk::where('order_id', $order->id);
        if($barangMasuk->count() > 0){
            return response()->json([
                'success' => false,
                'message' => 'Data Order Tidak Bisa Dihapus Karena Barang Sudah Masuk!'
            ]);
        }

        $order->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data Order Berhasil Dihapus!'
        ]);
    }

    public function getAutoCompleteData(Request $request)
    {
        $barang = Barang::where('id', $request->barang_id)->first();;
        if($barang){
            return response()->json([
                'kode_barang'   => $barang->kode_barang,
            ]);
        }
    }

}
