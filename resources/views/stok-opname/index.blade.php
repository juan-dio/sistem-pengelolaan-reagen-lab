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
                                <select name="barang_id" id="barang_id" class="js-example-basic-single form-control" style="width: 100%">
                                    <option selected>Pilih Barang</option>
                                    @foreach($barangs as $barang)
                                        <option value="{{ $barang->id }}">{{ $barang->nama_barang }}</option>
                                    @endforeach
                                </select>
                                @error('barang_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="stok_sistem">Stok Sistem</label>
                                <input type="text" name="stok_sistem" id="stok_sistem" class="form-control" min="0" readonly>
                                @error('stok_sistem')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="stok_fisik">Stok Fisik</label>
                                <input type="number" name="stok_fisik" id="stok_fisik" class="form-control" min="0">
                                @error('stok_fisik')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col">
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
                <div class="card-header">
                    <h3>Riwayat Stok Opname</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="table_id" class="display">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Barang</th>
                                    <th>Stok Sistem</th>
                                    <th>Stok Fisik</th>
                                    <th>Selisih</th>
                                    <th>Keterangan</th>
                                    <th>Tanggal</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($stokOpnames as $stokOpname)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $stokOpname->barang->nama_barang }}</td>
                                        <td>{{ $stokOpname->stok_sistem }} {{ $stokOpname->barang->satuan->satuan }}</td>
                                        <td>{{ $stokOpname->stok_fisik }} {{ $stokOpname->barang->satuan->satuan }}</td>
                                        <td>{{ $stokOpname->stok_sistem - $stokOpname->stok_fisik }} {{ $stokOpname->barang->satuan->satuan }}</td>
                                        <td>{{ $stokOpname->keterangan }}</td>
                                        <td>{{ $stokOpname->created_at }}</td>
                                        <td>
                                            @if($stokOpname->approved == 0)
                                                <span class="badge badge-warning">Pending</span>
                                            @else
                                                <span class="badge badge-success">Approved</span>
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

    <script>
        $(document).ready(function() {
            $('#table_id').DataTable({
                paging: true
            });

            setTimeout(function() {
                $('.js-example-basic-single').select2();
            }, 500);

            $('#barang_id').on('change', function() {
                let barang_id = $(this).val();
                $.ajax({
                    url: '/stok-opname/get-data',
                    type: 'GET',
                    data: {
                        barang_id: barang_id
                    },
                    success: function(response) {
                        $('#stok_sistem').val(`${response.stok} ${response.satuan.satuan}`);
                    }
                });
            });

        });
    </script>

@endsection