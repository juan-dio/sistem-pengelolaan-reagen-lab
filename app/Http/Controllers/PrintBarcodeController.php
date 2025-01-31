<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\BarangMasuk;
use App\Models\Satuan;
use Illuminate\Contracts\Cache\Store;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PrintBarcodeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('print-barcode.index', [
			'barangs' => Barang::all(),
		]);
    }

	public function printOne(Request $request) {
		$barang = Barang::find($request->barang_id);
		$jumlah = $request->jumlah;

		return view('print-barcode.print-out-one', [
			'barang' => $barang,
			'jumlah' => $jumlah,
		]);
	}

	public function printSome(Request $request) {
		$print_barangs = [];
        foreach ($request->some_barang_id as $key => $barang_id) {
            $barang = Barang::find($barang_id);
            $jumlah = $request->some_jumlah[$key];
            $print_barangs[] = [
                'barang' => $barang,
                'jumlah' => $jumlah,
            ];
        }
        
        return view('print-barcode.print-out-some', [
            'print_barangs' => $print_barangs,
        ]);
	}

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // return view('barang.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        
    }

    /**
     * Display the specified resource.
     */
    public function show(Barang $barang)
    {

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Barang $barang)
    {
        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Barang $barang)
    {
        
    }
    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Barang $barang)
    {
        
    }
}
