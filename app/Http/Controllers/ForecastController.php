<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;
use App\Models\BarangKeluar;
use Carbon\Carbon;

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
                'barang' => $barang->nama_barang,
                'satuan' => $barang->satuan->satuan,
                'forecast' => $forecast,
            ];
        }

        return view('forecast.index', compact('forecastResults'));
    }
}
