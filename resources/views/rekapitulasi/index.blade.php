@extends('layouts.app')

@section('content')

<div class="section-header">
    <h1>Rekapitulasi</h1>
    <div class="ml-auto">
        <a href="javascript:void(0)" class="btn btn-danger" id="print-rekapitulasi"><i class="fa fa-sharp fa-light fa-print"></i> Print PDF</a>
    </div>
</div>

<div class="row">
    <div class="col">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="table_id" class="display">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kode Barang</th>
                                <th>Nama Barang</th>
                                <th>Jenis</th>
                                <th>Stok</th>
                                <th>Outstanding</th>
                                <th>Masuk</th>
                                <th>Keluar</th>
                                {{-- <th>Satuan</th> --}}
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($barangRekap as $key => $barang)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $barang->kode_barang }}</td>
                                <td>{{ $barang->nama_barang }}</td>
                                <td>{{ $barang->jenis->jenis_barang ?? '-' }}</td>
                                <td>{{ $barang->stok }} {{ $barang->satuan->satuan }}</td>
                                <td>{{ $barangOutstanding[$barang->id]->total_outstanding ?? 0 }} {{ $barang->satuan->satuan }}</td>
                                <td>{{ $barangMasuk[$barang->id]->total_masuk ?? 0 }} {{ $barang->satuan->satuan }}</td>
                                <td>{{ $barangKeluar[$barang->id]->total_keluar ?? 0 }} {{ $barang->satuan->satuan }}</td>
                                {{-- <td>{{ $barang->satuan->satuan ?? '-' }}</td> --}}
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        let table = $('#table_id').DataTable({
            paging: true
        });
    });
</script>
@endsection
