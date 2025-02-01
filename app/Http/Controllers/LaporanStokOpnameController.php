<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\StokOpname;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LaporanStokOpnameController extends Controller
{
    public function index()
    {
        return view('laporan-stok-opname.index');
    }

    public function getData(Request $request)
    {
        $tanggalMulai = $request->input('tanggal_mulai');
        $tanggalSelesai = $request->input('tanggal_selesai');
    
        $stokOpname = StokOpname::query();
    
        if ($tanggalMulai && $tanggalSelesai) {
            $stokOpname->whereBetween('created_at', [$tanggalMulai, $tanggalSelesai])->where('approved', 1);
        }
    
        $data = $stokOpname->get();

        if (empty($tanggalMulai) && empty($tanggalSelesai)) {
            $data = StokOpname::all()->where('approved', 1);
        }
    
        return response()->json($data);
    }

    public function printStokOpname(Request $request)
    {
        $tanggalMulai = $request->input('tanggal_mulai');
        $tanggalSelesai = $request->input('tanggal_selesai');
    
        $stokOpname = StokOpname::query();
    
        if ($tanggalMulai && $tanggalSelesai) {
            $stokOpname->whereBetween('created_at', [$tanggalMulai, $tanggalSelesai])->where('approved', 1);
        }
    
        if ($tanggalMulai !== null && $tanggalSelesai !== null) {
            $data = $stokOpname->get();
        } else {
            $data = StokOpname::all()->where('approved', 1);
        }

        return view('laporan-stok-opname.print-stok-opname', compact('data', 'tanggalMulai', 'tanggalSelesai'));
    }

    public function exportExcel(Request $request)
    {
        $tanggalMulai = $request->input('tanggal_mulai');
        $tanggalSelesai = $request->input('tanggal_selesai');
    
        $stokOpname = StokOpname::query();
    
        if ($tanggalMulai && $tanggalSelesai) {
            $stokOpname->whereBetween('created_at', [$tanggalMulai, $tanggalSelesai])->where('approved', 1);
        }
    
        if ($tanggalMulai !== null && $tanggalSelesai !== null) {
            $data = $stokOpname->get();
        } else {
            $data = StokOpname::all()->where('approved', 1);
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Tanggal');
        $sheet->setCellValue('C1', 'Kode Barang');
        $sheet->setCellValue('D1', 'Nama Barang');
        $sheet->setCellValue('E1', 'Satuan');
        $sheet->setCellValue('F1', 'Stok Sistem');
        $sheet->setCellValue('G1', 'Stok Fisik');
        $sheet->setCellValue('H1', 'Selisih');
        $sheet->setCellValue('I1', 'Keterangan');

        $no = 1;
        $row = 2;

        foreach ($data as $item) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $item->created_at);
            $sheet->setCellValue('C' . $row, $item->barang->kode_barang);
            $sheet->setCellValue('D' . $row, $item->barang->nama_barang);
            $sheet->setCellValue('E' . $row, $item->barang->satuan->satuan);
            $sheet->setCellValue('F' . $row, $item->stok_sistem);
            $sheet->setCellValue('G' . $row, $item->stok_fisik);
            $sheet->setCellValue('H' . $row, ($item->stok_sistem - $item->stok_fisik));
            $sheet->setCellValue('I' . $row, $item->keterangan);
            $row++;
        }

        $writer = new Xlsx($spreadsheet);
    
        $filename = 'laporan-stok-opname.xlsx';
    
        $response = new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        });

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment;filename="' . $filename . '"');
        $response->headers->set('Cache-Control', 'max-age=0');

        return $response;
    }
}
