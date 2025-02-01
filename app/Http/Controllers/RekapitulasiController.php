<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;
use App\Models\BarangKeluar;
use App\Models\BarangMasuk;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RekapitulasiController extends Controller
{
    public function index()
    {
        // Ambil data rekapitulasi barang
        $barangRekap = Barang::with(['jenis', 'satuan'])
            ->select('id', 'kode_barang', 'nama_barang', 'stok', 'stok_minimum', 'jenis_id', 'satuan_id')
            ->get();

        // Ambil data barang masuk dan keluar
        $barangMasuk = BarangMasuk::selectRaw('barang_id, SUM(jumlah_masuk) as total_masuk, SUM(outstanding) as total_outstanding')
            ->where('approved', 1)
            ->groupBy('barang_id')
            ->get()
            ->keyBy('barang_id');

        $barangKeluar = BarangKeluar::selectRaw('barang_id, SUM(jumlah_keluar) as total_keluar')
            ->where('approved', 1)
            ->groupBy('barang_id')
            ->get()
            ->keyBy('barang_id');

        return view('rekapitulasi.index', compact('barangRekap', 'barangMasuk', 'barangKeluar'));
    }

    public function printRekapitulasi()
    {
        // Ambil data rekapitulasi barang
        $barangRekap = Barang::with(['jenis', 'satuan'])
            ->select('id', 'kode_barang', 'nama_barang', 'stok', 'stok_minimum', 'jenis_id', 'satuan_id')
            ->get();

        // Ambil data barang masuk dan keluar
        $barangMasuk = BarangMasuk::selectRaw('barang_id, SUM(jumlah_masuk) as total_masuk, SUM(outstanding) as total_outstanding')
            ->where('approved', 1)
            ->groupBy('barang_id')
            ->get()
            ->keyBy('barang_id');

        $barangKeluar = BarangKeluar::selectRaw('barang_id, SUM(jumlah_keluar) as total_keluar')
            ->where('approved', 1)
            ->groupBy('barang_id')
            ->get()
            ->keyBy('barang_id');

        return view('rekapitulasi.print-rekapitulasi', compact('barangRekap', 'barangMasuk', 'barangKeluar'));
    }

    public function exportExcel(Request $request)
    {
        // Ambil data rekapitulasi barang
        $barangRekap = Barang::with(['jenis', 'satuan'])
            ->select('id', 'kode_barang', 'nama_barang', 'stok', 'stok_minimum', 'jenis_id', 'satuan_id')
            ->get();

        // Ambil data barang masuk dan keluar
        $barangMasuk = BarangMasuk::selectRaw('barang_id, SUM(jumlah_masuk) as total_masuk, SUM(outstanding) as total_outstanding')
            ->where('approved', 1)
            ->groupBy('barang_id')
            ->get()
            ->keyBy('barang_id');

        $barangKeluar = BarangKeluar::selectRaw('barang_id, SUM(jumlah_keluar) as total_keluar')
            ->where('approved', 1)
            ->groupBy('barang_id')
            ->get()
            ->keyBy('barang_id');

        // Buat file excel
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Kode Barang');
        $sheet->setCellValue('C1', 'Nama Barang');
        $sheet->setCellValue('D1', 'Jenis');
        $sheet->setCellValue('E1', 'Satuan');
        $sheet->setCellValue('F1', 'Stok');
        $sheet->setCellValue('G1', 'Outstanding');
        $sheet->setCellValue('H1', 'Masuk');
        $sheet->setCellValue('I1', 'Keluar');

        $no = 1;
        $row = 2;

        foreach ($barangRekap as $barang) {
            $masuk = $barangMasuk->get($barang->id);
            $keluar = $barangKeluar->get($barang->id);

            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $barang->kode_barang);
            $sheet->setCellValue('C' . $row, $barang->nama_barang);
            $sheet->setCellValue('D' . $row, $barang->jenis->jenis_barang);
            $sheet->setCellValue('E' . $row, $barang->satuan->satuan);
            $sheet->setCellValue('F' . $row, $barang->stok);
            $sheet->setCellValue('G' . $row, $masuk ? $masuk->total_outstanding : 0);
            $sheet->setCellValue('H' . $row, $masuk ? $masuk->total_masuk : 0);
            $sheet->setCellValue('I' . $row, $keluar ? $keluar->total_keluar : 0);

            $row++;
        }

        $writer = new Xlsx($spreadsheet);
    
        $filename = 'rekapitulasi.xlsx';
    
        $response = new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        });

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment;filename="' . $filename . '"');
        $response->headers->set('Cache-Control', 'max-age=0');

        return $response;
    }
}
