<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class AlatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('alat.index', [
            'alats' => Alat::all()
        ]);
    }

    public function getDataAlat()
    {
        return response()->json([
            'success'   => true,
            'data'      => Alat::all()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('alat.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'alat'  => 'required',
        ],[
            'alat.required' => 'Form Alat Wajib Di Isi !',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 422);
        }

        $alat = Alat::create([
            'alat'  => $request->alat,
            'user_id'   => auth()->user()->id
        ]);

        return response()->json([
            'success'   => true,
            'message'   => 'Data Berhasil Disimpan !',
            'data'      => $alat
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Alat $alat)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Alat $alat)
    {
        return response()->json([
            'success' => true,
            'message' => 'Edit Data Barang',
            'data'    => $alat
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Alat $alat)
    {
        $validator = Validator::make($request->all(), [
            'alat'  => 'required',
        ],[
            'alat.required' => 'Form Alat Wajib Di Isi !',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 422);
        }

        $alat->update([
            'alat'  => $request->alat,
            'user_id'   => auth()->user()->id
        ]);

        return response()->json([
            'success'   => true,
            'message'   => 'Data Berhasil Terupdate',
            'data'      => $alat
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Alat $alat)
    {
        Alat::destroy($alat->id);
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Dihapus'
        ]);
    }
}
