<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Satuan;
use App\Models\Alat;
use App\Models\BarangKeluar;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\BarangMasuk;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BarangKeluarController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('barang-keluar.index', [
            'barangs'           => Barang::all(),
            'barangKeluar'      => BarangKeluar::all(),
            'alats'             => Alat::all()
        ]);
    }

    public function getDataBarangKeluar()
    {
        return response()->json([
            'success'       => true,
            'data'          => BarangKeluar::all(),
            'barangs'       => Barang::all(),
            'alats'         => Alat::all()
        ]);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // return view('barang-keluar.create', [
        //     'barangs' => Barang::all(),
        //     'alats' => Alat::all()
        // ]);

        abort(404);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tanggal_keluar' => 'required|date',
            'barang_id'      => 'required|exists:barangs,id',
            'alat_id'        => 'required|exists:alats,id',
            'jumlah_keluar'  => [
                'required',
                'integer',
                function ($attribute, $value, $fail) use ($request) {
                    $barang = Barang::find($request->barang_id);
                    if (!$barang) {
                        $fail("Barang tidak ditemukan!");
                        return;
                    }
                    if ($value > $barang->stok) {
                        $fail("Stok barang tidak cukup!");
                    }
                },
            ],
        ], [
            'tanggal_keluar.required' => 'Form Tanggal Keluar Wajib Diisi !',
            'barang_id.required'      => 'Pilih Barang !',
            'barang_id.exists'        => 'Pilih Barang !',
            'alat_id.required'        => 'Pilih Alat !',
            'alat_id.exists'          => 'Pilih Alat !',
            'jumlah_keluar.required'  => 'Form Jumlah Keluar Wajib Diisi !',
        ]);

        if($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }


        DB::beginTransaction();

        try {
            $barangId = $request->barang_id;
            $jumlahKeluar = $request->jumlah_keluar;

            // FIFO: Ambil batch barang masuk berdasarkan tanggal kedaluwarsa terdekat
            $batches = BarangMasuk::where('barang_id', $barangId)
                ->where('jumlah_stok', '>', 0)
                ->orderBy('tanggal_kadaluarsa')
                ->get();

            $jumlahKeluarTemp = $jumlahKeluar;

            foreach ($batches as $batch) {
                if ($jumlahKeluarTemp <= 0) {
                    break;
                }

                $stokBatch = $batch->jumlah_stok;

                if ($stokBatch >= $jumlahKeluarTemp) {
                    $batch->jumlah_stok -= $jumlahKeluarTemp;
                    $batch->save();
                    $jumlahKeluarTemp = 0;
                } else {
                    $jumlahKeluarTemp -= $stokBatch;
                    $batch->jumlah_stok = 0;
                    $batch->save();
                }
            }

            if ($jumlahKeluarTemp > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stok tidak mencukupi berdasarkan batch yang tersedia!',
                ], 422);
            }

            // Simpan data ke tabel barang_keluars
            $barangKeluar = BarangKeluar::create([
                'tanggal_keluar' => $request->tanggal_keluar,
                'kode_transaksi' => $request->kode_transaksi,
                'jumlah_keluar'  => $jumlahKeluar,
                'barang_id'      => $barangId,
                'alat_id'        => $request->alat_id,
                'user_id'        => auth()->user()->id,
            ]);

            // Update stok total di tabel barang
            $barang = Barang::find($barangId);
            $barang->stok -= $jumlahKeluar;
            $barang->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil disimpan!',
                'data'    => $barangKeluar,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(BarangKeluar $barangKeluar)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BarangKeluar $barangKeluar)
    {
        // return response()->json([
        //     'success' => true,
        //     'message' => 'Edit Data Barang',
        //     'data'    => $barangKeluar
        // ]);

        abort(404);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BarangKeluar $barangKeluar)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BarangKeluar $barangKeluar)
    {
        DB::beginTransaction();

        try {
            $jumlahKeluar = $barangKeluar->jumlah_keluar;

            // Cari batch barang_masuk yang terkait dengan barang_id
            $barangMasuks = BarangMasuk::where('barang_id', $barangKeluar->barang_id)
                ->orderBy('tanggal_kadaluarsa', 'asc')
                ->get();

            $jumlahRestore = $jumlahKeluar;

            foreach ($barangMasuks as $batch) {
                // Jika stok batch ini habis, tambahkan stok kembali
                $stokSebelumnya = $batch->jumlah_stok;

                if ($stokSebelumnya < $batch->jumlah_masuk) {
                    $ruangKosong = $batch->jumlah_masuk - $stokSebelumnya;

                    if ($ruangKosong >= $jumlahRestore) {
                        $batch->jumlah_stok += $jumlahRestore;
                        $batch->save();
                        $jumlahRestore = 0;
                        break;
                    } else {
                        $batch->jumlah_stok += $ruangKosong;
                        $jumlahRestore -= $ruangKosong;
                        $batch->save();
                    }
                }
            }

            // Jika stok batch belum mencukupi jumlah keluar
            if ($jumlahRestore > 0) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Stok batch tidak cukup untuk mengembalikan transaksi ini!',
                ], 422);
            }

            // Hapus data barang keluar
            $barangKeluar->delete();

            // Update stok total barang
            $barang = Barang::find($barangKeluar->barang_id);
            if ($barang) {
                $barang->stok += $jumlahKeluar;
                $barang->save();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil dihapus!',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }


    /**
     * Create Autocomplete Data
     */
    public function getAutoCompleteData(Request $request)
    {
        $barang = Barang::where('id', $request->barang_id)->first();

        if($barang){
            return response()->json([
                'barang'        => $barang,
                'stok'          => $barang->stok,
                'satuan_id'     => $barang->satuan_id,
            ]);
        }
    }

    /**
     * Create Autocomplete Data In Update Method
     */

    public function getStok(Request $request)
    {
        $namaBarang = $request->input('nama_barang');
        $barang = Barang::where('nama_barang', $namaBarang)->select('stok', 'satuan_id')->first();

        $response = [
            'stok'          => $barang->stok,
            'satuan_id'     => $barang->satuan_id
        ];

        return response()->json($response);
    }

    public function getSatuan()
    {
        $satuans = Satuan::all();
        
        return response()->json($satuans);
    }

    public function getBarangs(Request $request)
    {
        if ($request->has('q')) {
            $barangs = Barang::where('nama_barang', 'like', '%' . $request->input('q') . '%')->get();
            return response()->json($barangs);
        }

        return response()->json([]);
    }



}
