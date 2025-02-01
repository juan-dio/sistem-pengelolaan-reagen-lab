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
            'kode_transaksi'    => 'required|unique:barang_masuks,kode_transaksi',
            'lot'               => 'required',
            'tanggal_masuk'     => 'required|date',
            'tanggal_kadaluarsa'=> 'required|date',
            'jumlah_masuk'      => 'required|numeric',
            'outstanding'       => 'required|numeric',
            'jumlah_stok'       => 'required|numeric',
            'harga'             => 'required|numeric|min:0',
            'lokasi'            => 'required',
            'barang_id'         => 'required|exists:barangs,id',
            'supplier_id'       => 'required|exists:suppliers,id'
        ],[
            'kode_transaksi.required'   => 'Form Kode Transaksi Wajib Di Isi !',
            'kode_transaksi.unique'     => 'Kode Transaksi Sudah Ada !',
            'lot.required'              => 'Form Lot Wajib Di Isi !',
            'tanggal_masuk.required'    => 'Pilih Barang Terlebih Dahulu !',
            'tanggal_kadaluarsa.required' => 'Form Tanggal Kadaluarsa Wajib Di Isi !',
            'jumlah_masuk.required'     => 'Form Jumlah Stok Masuk Wajib Di Isi !',
            'jumlah_masuk.numeric'      => 'Form Jumlah Stok Masuk Harus Berupa Angka !',
            'outstanding.required'      => 'Form Outstanding Wajib Di Isi !',
            'outstanding.numeric'       => 'Form Outstanding Harus Berupa Angka !',
            'jumlah_stok.required'      => 'Form Jumlah Stok Wajib Di Isi !',
            'jumlah_stok.numeric'       => 'Form Jumlah Stok Harus Berupa Angka !',
            'harga.required'            => 'Form Harga Wajib Di Isi !',
            'harga.numeric'             => 'Form Harga Harus Berupa Angka !',
            'harga.min'                 => 'Form Harga Minimal 0 !',
            'lokasi.required'           => 'Form Lokasi Wajib Di Isi !',
            'barang_id.required'         => 'Pilih Barang !',
            'barang_id.exists'           => 'Pilih Barang !',
            'supplier_id.required'      => 'Pilih Supplier !',
            'supplier_id.exists'        => 'Pilih Supplier !'
        ]);


        if($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $barangMasuk = BarangMasuk::create([
            'kode_transaksi'    => $request->kode_transaksi,
            'lot'               => $request->lot,
            'tanggal_masuk'     => $request->tanggal_masuk,
            'tanggal_kadaluarsa'=> $request->tanggal_kadaluarsa,
            'jumlah_masuk'      => $request->jumlah_masuk,
            'outstanding'       => $request->outstanding,
            'jumlah_stok'       => $request->jumlah_stok,
            'harga'             => $request->harga,
            'lokasi'            => $request->lokasi,
            'barang_id'          => $request->barang_id,
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

    public function updateOutstanding(Request $request, BarangMasuk $barangMasuk)
    {
        $validator = Validator::make($request->all(), [
            'intransit' => 'required|numeric|min:0',
            'received'  => 'required|numeric|min:0'
        ],[
            'intransit.required'    => 'Form Intransit Wajib Di Isi !',
            'intransit.numeric'     => 'Form Intransit Harus Berupa Angka !',
            'intransit.min'         => 'Form Intransit Minimal 0 !',
            'received.required'     => 'Form Received Wajib Di Isi !',
            'received.numeric'      => 'Form Received Harus Berupa Angka !',
            'received.min'          => 'Form Received Minimal 0 !'
        ]);

        if($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if(!$barangMasuk->approved) {
            // $oldOutstanding = $barangMasuk->outstanding;
            // $oldJumlahMasuk = $barangMasuk->jumlah_masuk;
            // $oldJumlahStok = $barangMasuk->jumlah_stok;
            
            // $barangMasuk->outstanding = $request->outstanding;
            // $barangMasuk->jumlah_masuk = $oldJumlahMasuk + ($oldOutstanding - $request->outstanding);
            // $barangMasuk->jumlah_stok = $oldJumlahStok + ($oldOutstanding - $request->outstanding);

            $barangMasuk->outstanding = $request->intransit;
            $barangMasuk->jumlah_masuk = $barangMasuk->jumlah_masuk + $request->received;
            $barangMasuk->jumlah_stok = $barangMasuk->jumlah_stok + $request->received;
            $barangMasuk->save();

            return response()->json([
                'success'   => true,
                'message'   => 'Data Berhasil Di Update !',
                'data'      => $barangMasuk
            ]);
        } else if ($barangMasuk->approved) {
            // $oldOutstanding = $barangMasuk->outstanding;
            // $oldJumlahMasuk = $barangMasuk->jumlah_masuk;
            // $oldJumlahStok = $barangMasuk->jumlah_stok;
            
            // $barangMasuk->outstanding = $request->outstanding;
            // $barangMasuk->jumlah_masuk = $oldJumlahMasuk + ($oldOutstanding - $request->outstanding);
            // $barangMasuk->jumlah_stok = $oldJumlahStok + ($oldOutstanding - $request->outstanding);
            // $barangMasuk->save();

            // // Tambahkan stok barang
            // $barang = Barang::where('id', $barangMasuk->barang_id)->first();
            // if ($barang) {
            //     $barang->stok += ($oldOutstanding - $request->outstanding);
            //     $barang->save();
            // }

            $barangMasuk->outstanding = $request->intransit;
            $barangMasuk->jumlah_masuk = $barangMasuk->jumlah_masuk + $request->received;
            $barangMasuk->jumlah_stok = $barangMasuk->jumlah_stok + $request->received;
            $barangMasuk->save();

            // Tambahkan stok barang
            $barang = Barang::where('id', $barangMasuk->barang_id)->first();
            if ($barang) {
                $barang->stok += $request->received;
                $barang->save();
            }

            return response()->json([
                'success'   => true,
                'message'   => 'Data Berhasil Di Update !',
                'data'      => $barangMasuk
            ]);
        }
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
        if($barangMasuk->approved) {
            return response()->json([
                'success' => false,
                'message' => 'Data barang masuk yang sudah disetujui tidak dapat dihapus!'
            ], 400);
        }

        $barangMasuk->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil dihapus!'
        ]);

        // if(!$barangMasuk->approved) {
        //     $barangMasuk->delete();

        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Data berhasil dihapus!'
        //     ]);
        // }

        // // Periksa apakah stok dari batch ini sudah digunakan
        // $stokTersisa = $barangMasuk->jumlah_stok;

        // if ($stokTersisa < $barangMasuk->jumlah_masuk) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Data Barang Masuk tidak dapat dihapus karena sudah digunakan dalam transaksi.'
        //     ], 400);
        // }

        // // Lanjutkan penghapusan jika stok belum digunakan
        // $jumlahMasuk = $barangMasuk->jumlah_masuk;
        // $barangMasuk->delete();

        // // Kurangi stok barang utama
        // $barang = Barang::where('id', $barangMasuk->barang_id)->first();
        // if ($barang) {
        //     $barang->stok -= $jumlahMasuk;
        //     $barang->save();
        // }

        // return response()->json([
        //     'success' => true,
        //     'message' => 'Data Barang Berhasil Dihapus!'
        // ]);
    }



    /**
     * Create Autocomplete Data
     */
    public function getAutoCompleteData(Request $request)
    {
        $barang = Barang::where('id', $request->barang_id)->first();
        
        if($barang){
            return response()->json([
                'kode_barang'   => $barang->kode_barang,
                'stok'          => $barang->stok,
                'satuan'        => $barang->satuan->satuan,
            ]);
        }

        return response()->json([
            'message' => 'Data Tidak Ditemukan !'
        ]);
    }

}
