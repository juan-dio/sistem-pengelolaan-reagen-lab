@extends('layouts.app')

@section('content')
    <div class="section-header">
        <h1>Verifikasi</h1>
    </div>
    
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    @if($barangMasuk->count() == 0 && $barangKeluar->count() == 0 && $stokOpname->count() == 0)
        <div class="alert alert-info">
            Tidak ada data yang perlu diverifikasi
        </div>
    @endif

    @if ($barangMasuk->count() > 0)    
        <div class="accordion" id="accordionBarangMasuk">
            <div class="card" style="margin-bottom: 0;">
                <div class="card-header" id="headingBarangMasuk">
                    <button class="btn btn-link btn-block text-left expand d-flex align-items-center" type="button" data-toggle="collapse" data-target="#collapseBarangMasuk" aria-expanded="true" aria-controls="collapseBarangMasuk" style="text-decoration: none">
                        <h4 class="mr-2">Barang Masuk ({{ $barangMasuk->count() }})</h4>
                        <i class="fa-solid fa-chevron-down" style="font-size: 18px"></i>
                    </button>
                    <form action="/verifikasi-barang-masuk" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-lg btn-primary text-white" style="border-radius: .3rem; padding: .55rem 1.5rem;">
                            Approve
                        </button>
                    </form>
                </div>
            
                <div id="collapseBarangMasuk" class="collapse" aria-labelledby="headingBarangMasuk" data-parent="#accordionBarangMasuk">
                    <div class="card-body">
                        <div class="table table-responsive" style="overflow-x: auto;">
                            <table id="table_id" class="display" style="width: 100%; font-size: 14px;">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Kode</th>
                                        <th>Tanggal Masuk</th>
                                        <th>Tanggal Kadaluarsa</th>
                                        <th>Nama</th>
                                        <th>Jumlah Masuk</th>
                                        <th>Lokasi</th>
                                        <th>Supplier</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($barangMasuk as $key => $value)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $value->kode_transaksi }}</td>
                                            <td>{{ $value->tanggal_masuk }}</td>
                                            <td>{{ $value->tanggal_kadaluarsa }}</td>
                                            <td>{{ $value->barang->nama_barang }}</td>
                                            <td>{{ $value->jumlah_masuk }} {{ $value->barang->satuan->satuan }}</td>
                                            <td>{{ $value->lokasi }}</td>
                                            <td>{{ $value->supplier->supplier }}</td>
                                            <td>
                                                @if($value->approved == 0)
                                                    <span class="badge bg-warning text-white">pending</span>
                                                @else
                                                    <span class="badge bg-success text-white">approved</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if ($barangKeluar->count() > 0)
        <div class="accordion" id="accordionBarangKeluar">
            <div class="card" style="margin-bottom: 0;">
                <div class="card-header" id="headingBarangKeluar">
                    <button class="btn btn-link btn-block text-left expand d-flex align-items-center" type="button" data-toggle="collapse" data-target="#collapseBarangKeluar" aria-expanded="true" aria-controls="collapseBarangKeluar" style="text-decoration: none">
                        <h4 class="mr-2">Barang Keluar ({{ $barangKeluar->count() }})</h4>
                        <i class="fa-solid fa-chevron-down" style="font-size: 18px"></i>
                    </button>
                    <form action="/verifikasi-barang-keluar" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-lg btn-primary text-white" style="border-radius: .3rem; padding: .55rem 1.5rem;">
                            Approve
                        </button>
                    </form>
                </div>
            
                <div id="collapseBarangKeluar" class="collapse" aria-labelledby="headingBarangKeluar" data-parent="#accordionBarangKeluar">
                    <div class="card-body">
                        <div class="table table-responsive" style="overflow-x: auto;">
                            <table id="table_id" class="display" style="width: 100%; font-size: 14px;">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Kode</th>
                                        <th>Tanggal</th>
                                        <th>Nama</th>
                                        <th>Jumlah Keluar</th>
                                        <th>Alat</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($barangKeluar as $key => $value)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $value->kode_transaksi }}</td>
                                            <td>{{ $value->tanggal_keluar }}</td>
                                            <td>{{ $value->barang->nama_barang }}</td>
                                            <td>{{ $value->jumlah_keluar }} {{ $value->barang->satuan->satuan }}</td>
                                            <td>{{ $value->alat->alat }}</td>
                                            <td>
                                                @if($value->approved == 0)
                                                    <span class="badge bg-warning text-white">pending</span>
                                                @else
                                                    <span class="badge bg-success text-white">approved</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if ($stokOpname->count() > 0)
        <div class="accordion" id="accordionStokOpname">
            <div class="card" style="margin-bottom: 0;">
                <div class="card-header" id="headingStokOpname">
                    <button class="btn btn-link btn-block text-left expand d-flex align-items-center" type="button" data-toggle="collapse" data-target="#collapseStokOpname" aria-expanded="true" aria-controls="collapseStokOpname" style="text-decoration: none">
                        <h4 class="mr-2">Stok Opname ({{ $stokOpname->count() }})</h4>
                        <i class="fa-solid fa-chevron-down" style="font-size: 18px"></i>
                    </button>
                    <form action="/verifikasi-stok-opname" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-lg btn-primary text-white" style="border-radius: .3rem; padding: .55rem 1.5rem;">
                            Approve
                        </button>
                    </form>
                </div>
            
                <div id="collapseStokOpname" class="collapse" aria-labelledby="headingStokOpname" data-parent="#accordionStokOpname">
                    <div class="card-body">
                        <div class="table table-responsive" style="overflow-x: auto;">
                            <table id="table_id" class="display" style="width: 100%; font-size: 14px;">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama</th>
                                        <th>Stok Sistem</th>
                                        <th>Stok Fisik</th>
                                        <th>Selisih</th>
                                        <th>Tanggal</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($stokOpname as $key => $value)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $value->barang->nama_barang }}</td>
                                            <td>{{ $value->stok_sistem }} {{ $value->barang->satuan->satuan }}</td>
                                            <td>{{ $value->stok_fisik }} {{ $value->barang->satuan->satuan }}</td>
                                            <td>{{ $value->stok_sistem - $value->stok_fisik }} {{ $value->barang->satuan->satuan }}</td>
                                            <td>{{ $value->created_at }}</td>
                                            <td>
                                                @if($value->approved == 0)
                                                <span class="badge bg-warning text-white">pending</span>
                                                @else
                                                <span class="badge bg-success text-white">approved</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

@endsection