<?php

namespace App\Http\Controllers;

use Dompdf\Dompdf;
use App\Models\Supplier;
use App\Models\Barang;
use App\Models\BarangMasuk;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LaporanBarangMasukController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('laporan-barang-masuk.index');
    }

    /**
     * Get Data 
     */
    public function getData(Request $request)
    {
        $tanggalMulai = $request->input('tanggal_mulai');
        $tanggalSelesai = $request->input('tanggal_selesai');
    
        $barangMasuk = BarangMasuk::query();
    
        if ($tanggalMulai && $tanggalSelesai) {
            $barangMasuk->whereBetween('tanggal_masuk', [$tanggalMulai, $tanggalSelesai])->where('approved', 1);
        }
    
        $data = $barangMasuk->get();

        if (empty($tanggalMulai) && empty($tanggalSelesai)) {
            $data = BarangMasuk::all()->where('approved', 1);
        }
    
        return response()->json($data);
    }
    
    /**
     * Print DomPDF
     */
    public function printBarangMasuk(Request $request)
    {
        $tanggalMulai = $request->input('tanggal_mulai');
        $tanggalSelesai = $request->input('tanggal_selesai');
    
        $barangMasuk = BarangMasuk::query();
    
        if ($tanggalMulai && $tanggalSelesai) {
            $barangMasuk->whereBetween('tanggal_masuk', [$tanggalMulai, $tanggalSelesai])->where('approved', 1);
        }
    
        if ($tanggalMulai !== null && $tanggalSelesai !== null) {
            $data = $barangMasuk->get();
        } else {
            $data = BarangMasuk::all()->where('approved', 1);
        }
        
        return view('laporan-barang-masuk.print-barang-masuk', compact('data', 'tanggalMulai', 'tanggalSelesai'));
    }
    

    public function exportExcel(Request $request)
    {
        $tanggalMulai = $request->input('tanggal_mulai');
        $tanggalSelesai = $request->input('tanggal_selesai');
    
        $barangMasuk = BarangMasuk::query();
    
        if ($tanggalMulai && $tanggalSelesai) {
            $barangMasuk->whereBetween('tanggal_masuk', [$tanggalMulai, $tanggalSelesai])->where('approved', 1);
        }
    
        if ($tanggalMulai !== null && $tanggalSelesai !== null) {
            $data = $barangMasuk->get();
        } else {
            $data = BarangMasuk::all()->where('approved', 1);
        }
    
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
    
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Kode Transaksi');
        $sheet->setCellValue('C1', 'Supplier');
        $sheet->setCellValue('D1', 'Kode Barang');
        $sheet->setCellValue('E1', 'Lot');
        $sheet->setCellValue('F1', 'Nama Barang');
        $sheet->setCellValue('G1', 'Tanggal Masuk');
        $sheet->setCellValue('H1', 'Tanggal Expired');
        $sheet->setCellValue('I1', 'Satuan');
        $sheet->setCellValue('J1', 'Jumlah');
        $sheet->setCellValue('K1', 'Outstanding');
        $sheet->setCellValue('L1', 'Harga');
        $sheet->setCellValue('M1', 'Lokasi');
        $sheet->setCellValue('N1', 'Keterangan');
    
        $no = 1;
        $row = 2;
        foreach ($data as $barangMasuk) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $barangMasuk->kode_transaksi);
            $sheet->setCellValue('C' . $row, $barangMasuk->supplier->supplier);
            $sheet->setCellValue('D' . $row, $barangMasuk->barang->kode_barang);
            $sheet->setCellValue('E' . $row, $barangMasuk->lot);
            $sheet->setCellValue('F' . $row, $barangMasuk->barang->nama_barang);
            $sheet->setCellValue('G' . $row, $barangMasuk->tanggal_masuk);
            $sheet->setCellValue('H' . $row, $barangMasuk->tanggal_kadaluarsa);
            $sheet->setCellValue('I' . $row, $barangMasuk->barang->satuan->satuan);
            $sheet->setCellValue('J' . $row, $barangMasuk->jumlah_masuk);
            $sheet->setCellValue('K' . $row, $barangMasuk->outstanding);
            $sheet->setCellValue('L' . $row, $barangMasuk->harga);
            $sheet->setCellValue('M' . $row, $barangMasuk->lokasi);
            $sheet->setCellValue('N' . $row, $barangMasuk->keterangan);
            $row++;
        }
    
        $writer = new Xlsx($spreadsheet);
    
        $filename = 'laporan-barang-masuk.xlsx';
    
        $response = new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        });

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment;filename="' . $filename . '"');
        $response->headers->set('Cache-Control', 'max-age=0');

        return $response;
    }
}
