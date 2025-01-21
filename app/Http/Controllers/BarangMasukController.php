<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Satuan;
use App\Models\Supplier;
use App\Models\BarangMasuk;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Validated;
use Illuminate\Support\Facades\Validator;

class BarangMasukController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('barang-masuk.index', [
            'barangs'      => Barang::all(),
            'barangsMasuk' => BarangMasuk::all(),
            'suppliers'    => Supplier::all()
        ]);
    }

    public function getDataBarangMasuk()
    {
        return response()->json([
            'success'   => true,
            'data'      => BarangMasuk::all(),
            'barangs'   => Barang::all(),
            'supplier'  => Supplier::all()
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
            'tanggal_masuk'     => 'required|date',
            'tanggal_kadaluarsa'=> 'required|date',
            'jumlah_masuk'      => 'required',
            'jumlah_stok'       => 'required',
            'lokasi'            => 'required',
            'barang_id'         => 'required|exists:barangs,id',
            'supplier_id'       => 'required|exists:suppliers,id'
        ],[
            'tanggal_masuk.required'    => 'Pilih Barang Terlebih Dahulu !',
            'tanggal_kadaluarsa.required' => 'Form Tanggal Kadaluarsa Wajib Di Isi !',
            'jumlah_masuk.required'     => 'Form Jumlah Stok Masuk Wajib Di Isi !',
            'jumlah_stok.required'      => 'Form Jumlah Stok Wajib Di Isi !',
            'lokasi.required'           => 'Form Lokasi Wajib Di Isi !',
            'barang_id.required'        => 'Pilih Barang !',
            'barang_id.exists'          => 'Pilih Barang !',
            'supplier_id.required'      => 'Pilih Supplier !',
            'supplier_id.exists'        => 'Pilih Supplier !'
        ]);


        if($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $barangMasuk = BarangMasuk::create([
            'kode_transaksi'    => $request->kode_transaksi,
            'tanggal_masuk'     => $request->tanggal_masuk,
            'tanggal_kadaluarsa'=> $request->tanggal_kadaluarsa,
            'jumlah_masuk'      => $request->jumlah_masuk,
            'jumlah_stok'       => $request->jumlah_stok,
            'lokasi'            => $request->lokasi,
            'barang_id'         => $request->barang_id,
            'supplier_id'       => $request->supplier_id,
            'user_id'           => auth()->user()->id
        ]); 

        return response()->json([
            'success'   => true,
            'message'   => 'Data Berhasil Disimpan !',
            'data'      => $barangMasuk
        ]);
    }

    public function approve(Request $request, BarangMasuk $barangMasuk)
    {
        $barangMasuk->approved = true;
        $barangMasuk->save();

        // Tambahkan stok barang
        $barang = Barang::where('id', $barangMasuk->barang_id)->first();
        if ($barang) {
            $barang->stok += $barangMasuk->jumlah_masuk;
            $barang->save();
        }

        return redirect()->back()->with('success', 'Barang basuk berhasil disetujui!');
    }

    public function approveAll(Request $request)
    {
        $barangMasuk = BarangMasuk::where('approved', false)->get();
        foreach ($barangMasuk as $bm) {
            $bm->approved = true;
            $bm->save();

            // Tambahkan stok barang
            $barang = Barang::where('id', $bm->barang_id)->first();
            if ($barang) {
                $barang->stok += $bm->jumlah_masuk;
                $barang->save();
            }
        }

        return redirect()->back()->with('success', 'Semua barang basuk berhasil disetujui!');
    }

    /**
     * Display the specified resource.
     */
    public function show(BarangMasuk $barangMasuk)
    {
        abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BarangMasuk $barangMasuk)
    {
        return response()->json([
            'success' => true,
            'message' => 'Edit Data Barang',
            'data'    => $barangMasuk
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BarangMasuk $barangMasuk)
    {
        abort(404);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BarangMasuk $barangMasuk)
    {
        // Periksa apakah stok dari batch ini sudah digunakan
        $stokTersisa = $barangMasuk->jumlah_stok;

        if ($stokTersisa < $barangMasuk->jumlah_masuk) {
            return response()->json([
                'success' => false,
                'message' => 'Data Barang Masuk tidak dapat dihapus karena sudah digunakan dalam transaksi.'
            ], 400);
        }

        // Lanjutkan penghapusan jika stok belum digunakan
        $jumlahMasuk = $barangMasuk->jumlah_masuk;
        $barangMasuk->delete();

        // Kurangi stok barang utama
        $barang = Barang::where('id', $barangMasuk->barang_id)->first();
        if ($barang) {
            $barang->stok -= $jumlahMasuk;
            $barang->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Data Barang Berhasil Dihapus!'
        ]);
    }



    /**
     * Create Autocomplete Data
     */
    public function getAutoCompleteData(Request $request)
    {
        $barang = Barang::where('id', $request->barang_id)->first();;
        if($barang){
            return response()->json([
                'kode_barang'   => $barang->kode_barang,
                'stok'          => $barang->stok,
                'satuan'        => $barang->satuan->satuan,
            ]);
        }
    }

}
