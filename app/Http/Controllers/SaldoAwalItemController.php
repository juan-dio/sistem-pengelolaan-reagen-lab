<?php

namespace App\Http\Controllers;

use App\Models\saldoAwalItem;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SaldoAwalItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('saldo-awal-item.index',[
            'saldoAwalItems' => saldoAwalItem::all()
        ]);
    }

    public function getData()
    {
        $saldoAwalItems = saldoAwalItem::all();
        return response()->json($saldoAwalItems);
    }

    public function printSaldoAwalItem(Request $request)
    {
        $data = saldoAwalItem::all();

        return view('saldo-awal-item.print-saldo-awal', compact('data'));
    }

    public function exportExcel(Request $request)
    {
        $data = SaldoAwalItem::all();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Tanggal');
        $sheet->setCellValue('C1', 'Kode Barang');
        $sheet->setCellValue('D1', 'Nama Barang');
        $sheet->setCellValue('E1', 'Satuan');
        $sheet->setCellValue('F1', 'Jumlah');
        $sheet->setCellValue('G1', 'Harga');
        $sheet->setCellValue('H1', 'Total');

        $no = 1;
        $row = 2;

        foreach ($data as $item) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $item->tanggal);
            $sheet->setCellValue('C' . $row, $item->barang->kode_barang);
            $sheet->setCellValue('D' . $row, $item->barang->nama_barang);
            $sheet->setCellValue('E' . $row, $item->barang->satuan->satuan);
            $sheet->setCellValue('F' . $row, $item->jumlah);
            $sheet->setCellValue('G' . $row, $item->harga);
            $sheet->setCellValue('H' . $row, ($item->jumlah * $item->harga));
            $row++;
        }

        $writer = new Xlsx($spreadsheet);
    
        $filename = 'saldo-awal-item.xlsx';
    
        $response = new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        });

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment;filename="' . $filename . '"');
        $response->headers->set('Cache-Control', 'max-age=0');

        return $response;
    }
}
