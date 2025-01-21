@extends('layouts.app')

@section('content')
    <div class="section-header">
        <h1>Stok Adjustment</h1>
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

                    <form action="/stok-adjustment" method="POST">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="stok_opname_id">Pilih Barang</label>
                                <select name="stok_opname_id" id="stok_opname_id" class="js-example-basic-single form-control" style="width: 100%">
                                    <option value="">Pilih Barang</option>
                                    @foreach($stokAdjustments as $stok)
                                        <option value="{{ $stok->id }}">{{ $stok->barang->nama_barang }}</option>
                                    @endforeach
                                </select>
                                @error('stok_opname_id')
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
                                <input type="text" name="stok_fisik" id="stok_fisik" class="form-control" min="0" readonly>
                                @error('stok_fisik')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Sesuaikan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h3>Riwayat Stok Adjustment</h3>
                </div>
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
                                    <th>Stok Akhir</th>
                                    <th>Keterangan</th>
                                    {{-- <th>Status</th> --}}
                                    <th>Tanggal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($adjustedStokOpnames as $stok)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $stok->barang->nama_barang }}</td>
                                        <td>{{ $stok->stok_sistem }} {{ $stok->barang->satuan->satuan }}</td>
                                        <td>{{ $stok->stok_fisik }} {{ $stok->barang->satuan->satuan }}</td>
                                        <td>{{ $stok->stok_sistem - $stok->stok_fisik }} {{ $stok->barang->satuan->satuan }}</td>
                                        <td>{{ $stok->stok_sistem }} {{ $stok->barang->satuan->satuan }}</td>
                                        <td>{{ $stok->keterangan }}</td>
                                        {{-- <td>{{ ($stok->adjusted) ? 'Adjusted' : 'Not yet adjusted' }}</td> --}}
                                        <td>{{ $stok->updated_at }}</td>
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

            $('#stok_opname_id').on('change', function() {
                let stok_opname_id = $(this).val();
                $.ajax({
                    url: '/stok-adjustment/get-data',
                    type: 'GET',
                    data: {
                        stok_opname_id: stok_opname_id
                    },
                    success: function(response) {
                        $('#stok_sistem').val(`${response.stok_sistem} ${response.barang.satuan.satuan}`);
                        $('#stok_fisik').val(`${response.stok_fisik} ${response.barang.satuan.satuan}`);
                    }
                });
            });
        });
    </script>

@endsection