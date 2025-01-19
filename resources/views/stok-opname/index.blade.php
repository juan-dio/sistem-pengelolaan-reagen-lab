@extends('layouts.app')

@section('content')
    <div class="section-header">
        <h1>Stok Opname</h1>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form action="/stok-opname" method="POST">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="barang_id">Pilih Barang</label>
                                <select name="barang_id" id="barang_id" class="form-control">
                                    <option value="">Pilih Barang</option>
                                    @foreach($barangs as $barang)
                                        <option value="{{ $barang->id }}">{{ $barang->nama_barang }}</option>
                                    @endforeach
                                </select>
                                @error('barang_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="stok_aktual">Stok Aktual</label>
                                <input type="number" name="stok_aktual" id="stok_aktual" class="form-control" min="0">
                                @error('stok_aktual')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="keterangan">Keterangan</label>
                                <input type="text" name="keterangan" id="keterangan" class="form-control">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="table_id" class="display">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Barang</th>
                                    <th>Stok Sistem</th>
                                    <th>Stok Aktual</th>
                                    <th>Selisih</th>
                                    <th>Keterangan</th>
                                    <th>Tanggal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($stokOpnames as $stokOpname)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $stokOpname->barang->nama_barang }}</td>
                                        <td>{{ $stokOpname->barang->stok }} {{ $stokOpname->barang->satuan->satuan }}</td>
                                        <td>{{ $stokOpname->stok_aktual }} {{ $stokOpname->barang->satuan->satuan }}</td>
                                        <td>{{ $stokOpname->barang->stok - $stokOpname->stok_aktual }} {{ $stokOpname->barang->satuan->satuan }}</td>
                                        <td>{{ $stokOpname->keterangan }}</td>
                                        <td>{{ $stokOpname->created_at->format('d-m-Y') }}</td>
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
            $('#table_id').DataTable({
                paging: true
            });
        });
    </script>

@endsection