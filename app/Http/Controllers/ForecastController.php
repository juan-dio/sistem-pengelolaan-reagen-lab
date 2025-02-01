<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;
use App\Models\BarangKeluar;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ForecastController extends Controller
{
    public function index()
    {
        // Ambil data barang keluar berdasarkan bulan
        $barangKeluar = BarangKeluar::selectRaw('barang_id, YEAR(created_at) as tahun, MONTH(created_at) as bulan, SUM(jumlah_keluar) as total')
            ->where('approved', 1)
            ->groupBy('barang_id', 'tahun', 'bulan')
            ->orderBy('tahun', 'desc')
            ->orderBy('bulan', 'desc')
            ->get();

        // Simpan hasil forecast
        $forecastResults = [];

        foreach ($barangKeluar->groupBy('barang_id') as $barangId => $data) {
            $data = $data->sortBy('tahun')->sortBy('bulan')->values(); // Urutkan data berdasarkan bulan

            // Ambil data 6 bulan terakhir
            $jumlahKeluar = $data->pluck('total')->toArray();
            $barang = Barang::find($barangId);

            // Hitung SMA (Simple Moving Average)
            $forecast = [];
            $periode = 3; // Gunakan rata-rata 3 bulan terakhir untuk forecast

            for ($i = 0; $i < 6; $i++) {
                if (count($jumlahKeluar) >= $periode) {
                    $rataRata = array_sum(array_slice($jumlahKeluar, -$periode)) / $periode;
                } else {
                    $rataRata = array_sum($jumlahKeluar) / max(count($jumlahKeluar), 1);
                }
                $forecast[] = round($rataRata);
                $jumlahKeluar[] = $rataRata;
            }

            $forecastResults[] = [
                'kode_barang' => $barang->kode_barang,
                'barang' => $barang->nama_barang,
                'satuan' => $barang->satuan->satuan,
                'forecast' => $forecast,
            ];
        }

        return view('forecast.index', compact('forecastResults'));
    }

    public function printForecast() {
        // Ambil data barang keluar berdasarkan bulan
        $barangKeluar = BarangKeluar::selectRaw('barang_id, YEAR(created_at) as tahun, MONTH(created_at) as bulan, SUM(jumlah_keluar) as total')
            ->where('approved', 1)
            ->groupBy('barang_id', 'tahun', 'bulan')
            ->orderBy('tahun', 'desc')
            ->orderBy('bulan', 'desc')
            ->get();

        // Simpan hasil forecast
        $forecastResults = [];

        foreach ($barangKeluar->groupBy('barang_id') as $barangId => $data) {
            $data = $data->sortBy('tahun')->sortBy('bulan')->values(); // Urutkan data berdasarkan bulan

            // Ambil data 6 bulan terakhir
            $jumlahKeluar = $data->pluck('total')->toArray();
            $barang = Barang::find($barangId);

            // Hitung SMA (Simple Moving Average)
            $forecast = [];
            $periode = 3; // Gunakan rata-rata 3 bulan terakhir untuk forecast

            for ($i = 0; $i < 6; $i++) {
                if (count($jumlahKeluar) >= $periode) {
                    $rataRata = array_sum(array_slice($jumlahKeluar, -$periode)) / $periode;
                } else {
                    $rataRata = array_sum($jumlahKeluar) / max(count($jumlahKeluar), 1);
                }
                $forecast[] = round($rataRata);
                $jumlahKeluar[] = $rataRata;
            }

            $forecastResults[] = [
                'kode_barang' => $barang->kode_barang,
                'barang' => $barang->nama_barang,
                'satuan' => $barang->satuan->satuan,
                'forecast' => $forecast,
            ];
        }

        return view('forecast.print-forecast', compact('forecastResults'));
    }

    public function exportExcel(Request $request)
    {
        // Ambil data barang keluar berdasarkan bulan
        $barangKeluar = BarangKeluar::selectRaw('barang_id, YEAR(created_at) as tahun, MONTH(created_at) as bulan, SUM(jumlah_keluar) as total')
            ->where('approved', 1)
            ->groupBy('barang_id', 'tahun', 'bulan')
            ->orderBy('tahun', 'desc')
            ->orderBy('bulan', 'desc')
            ->get();

        // Simpan hasil forecast
        $forecastResults = [];

        foreach ($barangKeluar->groupBy('barang_id') as $barangId => $data) {
            $data = $data->sortBy('tahun')->sortBy('bulan')->values(); // Urutkan data berdasarkan bulan

            // Ambil data 6 bulan terakhir
            $jumlahKeluar = $data->pluck('total')->toArray();
            $barang = Barang::find($barangId);

            // Hitung SMA (Simple Moving Average)
            $forecast = [];
            $periode = 3; // Gunakan rata-rata 3 bulan terakhir untuk forecast

            for ($i = 0; $i < 6; $i++) {
                if (count($jumlahKeluar) >= $periode) {
                    $rataRata = array_sum(array_slice($jumlahKeluar, -$periode)) / $periode;
                } else {
                    $rataRata = array_sum($jumlahKeluar) / max(count($jumlahKeluar), 1);
                }
                $forecast[] = round($rataRata);
                $jumlahKeluar[] = $rataRata;
            }

            $forecastResults[] = [
                'kode_barang' => $barang->kode_barang,
                'barang' => $barang->nama_barang,
                'satuan' => $barang->satuan->satuan,
                'forecast' => $forecast,
            ];
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Kode Barang');
        $sheet->setCellValue('C1', 'Nama Barang');
        $sheet->setCellValue('D1', 'Satuan');
        $sheet->setCellValue('E1', now()->addMonths(1)->format('F Y'));
        $sheet->setCellValue('F1', now()->addMonths(2)->format('F Y'));
        $sheet->setCellValue('G1', now()->addMonths(3)->format('F Y'));
        $sheet->setCellValue('H1', now()->addMonths(4)->format('F Y'));
        $sheet->setCellValue('I1', now()->addMonths(5)->format('F Y'));
        $sheet->setCellValue('J1', now()->addMonths(6)->format('F Y'));

        $no = 1;
        $row = 2;

        foreach ($forecastResults as $item) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $item['kode_barang']);
            $sheet->setCellValue('C' . $row, $item['barang']);
            $sheet->setCellValue('D' . $row, $item['satuan']);
            $sheet->setCellValue('E' . $row, $item['forecast'][0]);
            $sheet->setCellValue('F' . $row, $item['forecast'][1]);
            $sheet->setCellValue('G' . $row, $item['forecast'][2]);
            $sheet->setCellValue('H' . $row, $item['forecast'][3]);
            $sheet->setCellValue('I' . $row, $item['forecast'][4]);
            $sheet->setCellValue('J' . $row, $item['forecast'][5]);
            $row++;
        }

        $writer = new Xlsx($spreadsheet);
    
        $filename = 'forecast.xlsx';
    
        $response = new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        });

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment;filename="' . $filename . '"');
        $response->headers->set('Cache-Control', 'max-age=0');

        return $response;
    }
}
