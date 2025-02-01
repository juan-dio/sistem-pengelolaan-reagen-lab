<?php

namespace App\Http\Controllers;

use Dompdf\Dompdf;
use App\Models\Alat;
use App\Models\BarangKeluar;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LaporanBarangKeluarController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('laporan-barang-keluar.index');
    }

    /**
     * Get Data 
     */
    public function getData(Request $request)
    {
        $tanggalMulai = $request->input('tanggal_mulai');
        $tanggalSelesai = $request->input('tanggal_selesai');
    
        $barangKeluar = BarangKeluar::query();
    
        if ($tanggalMulai && $tanggalSelesai) {
            $barangKeluar->whereBetween('tanggal_keluar', [$tanggalMulai, $tanggalSelesai])->where('approved', 1);
        }
    
        $data = $barangKeluar->get();

        if (empty($tanggalMulai) && empty($tanggalSelesai)) {
            $data = BarangKeluar::all()->where('approved', 1);
        }
    
        return response()->json($data);
    }

    /**
     * Print DomPDF
     */
    public function printBarangKeluar(Request $request)
    {
        $tanggalMulai = $request->input('tanggal_mulai');
        $tanggalSelesai = $request->input('tanggal_selesai');
    
        $barangKeluar = BarangKeluar::query();
    
        if ($tanggalMulai && $tanggalSelesai) {
            $barangKeluar->whereBetween('tanggal_keluar', [$tanggalMulai, $tanggalSelesai])->where('approved', 1);
        }
    
        if ($tanggalMulai !== null && $tanggalSelesai !== null) {
            $data = $barangKeluar->get();
        } else {
            $data = BarangKeluar::all()->where('approved', 1);
        }
        
        return view('laporan-barang-keluar.print-barang-keluar', compact('data', 'tanggalMulai', 'tanggalSelesai'));
    }

    /**
     * Export Excel
     */
    public function exportExcel(Request $request)
    {
        $tanggalMulai = $request->input('tanggal_mulai');
        $tanggalSelesai = $request->input('tanggal_selesai');

        $barangKeluar = BarangKeluar::query();

        if ($tanggalMulai && $tanggalSelesai) {
            $barangKeluar->whereBetween('tanggal_keluar', [$tanggalMulai, $tanggalSelesai])->where('approved', 1);
        }

        $data = $barangKeluar->get();

        if (empty($tanggalMulai) && empty($tanggalSelesai)) {
            $data = BarangKeluar::all()->where('approved', 1);
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Kode Transaksi');
        $sheet->setCellValue('C1', 'Tanggal Keluar');
        $sheet->setCellValue('D1', 'Kode Barang');
        $sheet->setCellValue('E1', 'Nama Barang');
        $sheet->setCellValue('F1', 'Satuan');
        $sheet->setCellValue('G1', 'Jumlah');
        $sheet->setCellValue('H1', 'Alat');

        $no = 1;
        $row = 2;
        foreach ($data as $item) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $item->kode_transaksi);
            $sheet->setCellValue('C' . $row, $item->tanggal_keluar);
            $sheet->setCellValue('D' . $row, $item->barang->kode_barang);
            $sheet->setCellValue('E' . $row, $item->barang->nama_barang);
            $sheet->setCellValue('F' . $row, $item->barang->satuan->satuan);
            $sheet->setCellValue('G' . $row, $item->jumlah_keluar);
            $sheet->setCellValue('H' . $row, $item->alat->alat);
            $row++;
        }

        $writer = new Xlsx($spreadsheet);
    
        $filename = 'laporan-barang-keluar.xlsx';
    
        $response = new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        });

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment;filename="' . $filename . '"');
        $response->headers->set('Cache-Control', 'max-age=0');

        return $response;
    }
}
