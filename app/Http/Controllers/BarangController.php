<?php

namespace App\Http\Controllers;

use App\Models\Jenis;
use App\Models\Barang;
use Illuminate\Http\File;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\BarangMasuk;
use App\Models\Satuan;
use Illuminate\Contracts\Cache\Store;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;

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
            'satuans'         => Satuan::all(),
            'test_group'      => [
                'HM' => 'Hematology (HM)',
                'IM' => 'Immunology (IM)',
                'SR' => 'Serology (SR)',
                'UR' => 'Urine (UR)',
                'CH'   => 'Chemistry (CH)',
            ]
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
            'gambar'        => 'required|mimes:jpeg,png,jpg',
            'stok_minimum'  => 'required|numeric',
            'test_group'    => 'required',
            'jenis_id'      => 'required',
            'satuan_id'     => 'required'
        ], [
            'nama_barang.required'  => 'Form Nama Barang Wajib Di Isi !',
            'kode_barang.required'  => 'Form Kode Barang Wajib Di Isi !',
            'gambar.required'       => 'Tambahkan Gambar !',
            'gambar.mimes'          => 'Gunakan Gambar Yang Memiliki Format jpeg, png, jpg !',
            'stok_minimum.required' => 'Form Stok Minimum Wajib Di Isi !',
            'stok_minimum.numeric'  => 'Gunakan Angka Untuk Mengisi Form Ini !',
            'test_group.required'   => 'Pilih Test Group !',
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
            'test_group'  => $request->test_group,
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
            'kode_barang'   => 'required|unique:barangs,kode_barang,'.$barang->id,
            'gambar'        => 'nullable|mimes:jpeg,png,jpg',
            'stok_minimum'  => 'required|numeric',
            'test_group'    => 'required',
            'jenis_id'      => 'required',
            'satuan_id'     => 'required'
        ], [
            'nama_barang.required'  => 'Form Nama Barang Wajib Di Isi !',
            'kode_barang.required'  => 'Form Kode Barang Wajib Di Isi !',
            'kode_barang.unique'    => 'Kode Barang Sudah Ada !',
            'gambar.mimes'          => 'Gunakan Gambar Yang Memiliki Format jpeg, png, jpg !',
            'stok_minimum.required' => 'Form Stok Minimum Wajib Di Isi !',
            'stok_minimum.numeric'  => 'Gunakan Angka Untuk Mengisi Form Ini !',
            'test_group.required'   => 'Pilih Test Group !',
            'jenis_id.required'     => 'Pilih Jenis Barang !',
            'satuan_id.required'    => 'Pilih Satuan Barang !'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 422);
        }

        $gambar = $barang->gambar;
        if($request->hasFile('gambar')) {
            if($barang->gambar) {
                try {
                    unlink('.'.Storage::url($barang->gambar));
                } catch (\Throwable $th) {
                    //throw $th;
                }
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
            'test_group'    => $request->test_group, 
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
        $barangMasuks = BarangMasuk::where('barang_id', $barang->id)->get();

        if($barangMasuks->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Data Barang Tidak Bisa Dihapus Karena Terkait Dengan Data Barang Masuk !'
            ]);
        }

        unlink('.'.Storage::url($barang->gambar));
    
        Barang::destroy($barang->id);

        return response()->json([
            'success' => true,
            'message' => 'Data Barang Berhasil Dihapus!'
        ]);
    }

    public function downloadExcelTemplate() {
        $template = public_path('storage/excel/template.xlsx');

        // Log path file
        Log::info('Path to template: ' . $template);

        if (!file_exists($template)) {
            Log::error('File not found at: ' . $template);
            return response()->json(['error' => 'File not found.'], 404);
        }

        $headers = ['Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];

        return response()->download($template, 'template.xlsx', $headers);
    }

    public function readExcel(Request $request) {
        // cek database harus kosong
        if(Barang::count() > 0) {
            return response()->json([
                'success' => false,
                'excel' => ['Data Reagen Harus Kosong !']
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'excel' => 'required|mimes:xlsx,xls'
        ], [
            'excel.required' => 'Pilih File Excel !',
            'excel.mimes'    => 'Gunakan File Dengan Ekstensi xlsx, xls !'
        ]);

        if($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $excel = $request->file('excel');
        $nama_file = $excel->getClientOriginalName();
        $excel->storeAs('excel', $nama_file, 'public');

        $excel = public_path('storage/excel/' . $nama_file);
        $loadExcel = IOFactory::load($excel);
        $sheet = $loadExcel->getActiveSheet();

        $data = [];

        // Membaca data dari sheet
        foreach ($sheet->getRowIterator() as $row) {
            $rowData = [];
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false); // Mengatur agar semua sel dibaca, termasuk yang kosong

            // foreach ($cellIterator as $cell) {
            //     $rowData[] = $cell->getValue(); // Mendapatkan nilai dari setiap sel
            // }

            // Mendapatkan nilai dari setiap sel
            $rowData['kode_barang'] = $sheet->getCell('A' . $row->getRowIndex())->getValue();
            $rowData['nama_barang'] = $sheet->getCell('B' . $row->getRowIndex())->getValue();
            $rowData['stok_minimum'] = $sheet->getCell('C' . $row->getRowIndex())->getValue();
            $rowData['test_group'] = $sheet->getCell('D' . $row->getRowIndex())->getValue();
            $rowData['deskripsi'] = $sheet->getCell('E' . $row->getRowIndex())->getValue();
            $rowData['jenis_id'] = (strtolower($sheet->getCell('F' . $row->getRowIndex())->getValue()) == 'dingin') ? 1 : 2;
            $rowData['satuan_id'] = (strtolower($sheet->getCell('G' . $row->getRowIndex())->getValue()) == 'ml') ? 1 : 2;

            $data[] = $rowData;
        }

        // Menghapus file excel yang sudah di upload
        try {
            unlink($excel);
        } catch (\Throwable $th) {
            //throw $th;
        }

        return response()->json([
            'data' => $data,
            'success' => true,
            'message' => 'Data Berhasil Di Import !'
        ]);
    }

    public function printBarcode($id) {
        $barang = Barang::find($id);

        return view('barang.print', [
            'barang' => $barang
        ]);
    }
}
