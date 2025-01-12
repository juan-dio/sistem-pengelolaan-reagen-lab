<?php

namespace App\Http\Controllers;

use App\Models\Jenis;
use App\Models\Barang;
use Illuminate\Http\File;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Satuan;
use Illuminate\Contracts\Cache\Store;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;



class BarangController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('barang.index', [
            'barangs'         => Barang::all(),
            'jenis_barangs'   => Jenis::all(),
            'satuans'         => Satuan::all()
        ]);
    }

    public function getDataBarang()
    {
        $barangs = Barang::all();
        
        return response()->json([
            'success'   => true,
            'data'      => $barangs
        ]);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('barang.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_barang'   => 'required',
            'kode_barang'   => 'required|unique:barangs,kode_barang',
            'deskripsi'     => 'required',
            'gambar'        => 'required|mimes:jpeg,png,jpg',
            'stok_minimum'  => 'required|numeric',
            'jenis_id'      => 'required',
            'satuan_id'     => 'required'
        ], [
            'nama_barang.required'  => 'Form Nama Barang Wajib Di Isi !',
            'kode_barang.required'  => 'Form Kode Barang Wajib Di Isi !',
            'kode_barang.unique'    => 'Kode Barang Sudah Ada, Gunakan Kode Barang Lain !',
            'deskripsi.required'    => 'Form Deskripsi Wajib Di Isi !',
            'gambar.required'       => 'Tambahkan Gambar !',
            'gambar.mimes'          => 'Gunakan Gambar Yang Memiliki Format jpeg, png, jpg !',
            'stok_minimum.required' => 'Form Stok Minimum Wajib Di Isi !',
            'stok_minimum.numeric'  => 'Gunakan Angka Untuk Mengisi Form Ini !',
            'jenis_id.required'     => 'Pilih Jenis Barang !',
            'satuan_id.required'    => 'Pilih Jenis Barang !'
        ]);

        if($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $gambar = null;
        if ($request->hasFile('gambar')) {
            $path       = 'gambar-barang/';
            $file       = $request->file('gambar');
            $fileName   = $file->getClientOriginalName();
            $gambar     = $file->storeAs($path, $fileName, 'public');
        }

        $barang = Barang::create([
            'nama_barang' => $request->nama_barang,
            'kode_barang' => $request->kode_barang,
            'stok_minimum'=> $request->stok_minimum,
            'deskripsi'   => $request->deskripsi,
            'gambar'      => $gambar,
            'user_id'     => auth()->user()->id,
            'jenis_id'    => $request->jenis_id,
            'satuan_id'   => $request->satuan_id
        ]);

        return response()->json([
            'success'   => true,
            'message'   => 'Data Berhasil Disimpan !',
            'data'      => $barang
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Barang $barang)
    {
        return response()->json([
            'success' => true,
            'message' => 'Detail Data Barang',
            'data'    => $barang
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Barang $barang)
    {
        return response()->json([
            'success' => true,
            'message' => 'Edit Data Barang',
            'data'    => $barang
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Barang $barang)
    {
        $validator = Validator::make($request->all(), [
            'nama_barang'   => 'required',
            'kode_barang'   => 'required|unique:barangs,kode_barang,' . $barang->id,
            'deskripsi'     => 'required',
            'gambar'        => 'nullable|mimes:jpeg,png,jpg',
            'stok_minimum'  => 'required|numeric',
            'jenis_id'      => 'required',
            'satuan_id'     => 'required'
        ], [
            'nama_barang.required'  => 'Form Nama Barang Wajib Di Isi !',
            'kode_barang.required'  => 'Form Kode Barang Wajib Di Isi !',
            'kode_barang.unique'    => 'Kode Barang Sudah Ada, Gunakan Kode Barang Lain !',
            'deskripsi.required'    => 'Form Deskripsi Wajib Di Isi !',
            'gambar.mimes'          => 'Gunakan Gambar Yang Memiliki Format jpeg, png, jpg !',
            'stok_minimum.required' => 'Form Stok Minimum Wajib Di Isi !',
            'stok_minimum.numeric'  => 'Gunakan Angka Untuk Mengisi Form Ini !',
            'jenis_id.required'     => 'Pilih Jenis Barang !',
            'satuan_id.required'    => 'Pilih Satuan Barang !'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 422);
        }

        $gambar = $barang->gambar;
        if($request->hasFile('gambar')) {
            if($barang->gambar) {
                unlink('.'.Storage::url($barang->gambar));
            }
            $path       = 'gambar-barang/';
            $file       = $request->file('gambar');
            $fileName   = $file->getClientOriginalName();
            $gambar     = $file->storeAs($path, $fileName, 'public');
        } 
    
        $barang->update([
            'nama_barang'   => $request->nama_barang,
            'kode_barang'   => $request->kode_barang,
            'stok_minimum'  => $request->stok_minimum, 
            'deskripsi'     => $request->deskripsi,
            'user_id'       => auth()->user()->id,
            'gambar'        => $gambar,
            'jenis_id'      => $request->jenis_id,
            'satuan_id'     => $request->satuan_id
        ]);
    
        return response()->json([
            'success'   => true,
            'message'   => 'Data Berhasil Terupdate',
            'data'      => $barang
        ]);
    }
    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Barang $barang)
    {
        unlink('.'.Storage::url($barang->gambar));
    
        Barang::destroy($barang->id);

        return response()->json([
            'success' => true,
            'message' => 'Data Barang Berhasil Dihapus!'
        ]);
    }
}
