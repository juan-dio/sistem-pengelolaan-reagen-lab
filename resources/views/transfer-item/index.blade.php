@extends('layouts.app')

@section('content')
    <div class="section-header">
        <h1>Transfer Item</h1>
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

                    <form action="/transfer-item" method="POST">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="barang_masuk_id">Pilih Barang</label>
                                <select name="barang_masuk_id" id="barang_masuk_id" class="js-example-basic-single form-control" style="width: 100%">
                                    <option selected>Pilih Barang</option>
                                    @foreach($barangMasuks as $barangMasuk)
                                        <option value="{{ $barangMasuk->id }}">{{ $barangMasuk->barang->nama_barang }} {{ $barangMasuk->kode_transaksi }}</option>
                                    @endforeach
                                </select>
                                @error('barang_masuk_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="previous_location">Lokasi Saat Ini</label>
                                <input type="text" name="previous_location" id="previous_location" class="form-control" min="0" readonly>
                                @error('previous_location')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="new_location">Transfer</label>
                                <input type="number" name="new_location" id="new_location" class="form-control" min="0">
                                @error('new_location')
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
                    <h3>Riwayat Transfer Item</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="table_id" class="display">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Barang</th>
                                    <th>Lokasi Saat Ini</th>
                                    <th>Transfer</th>
                                    <th>Keterangan</th>
                                    <th>Tanggal</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transferItems as $transferItem)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $transferItem->barang_masuk->barang->nama_barang }} {{ $transferItem->barangMasuk->kode_transaksi }}</td>
                                        <td>{{ $transferItem->previous_location }}</td>
                                        <td>{{ $transferItem->new_location }}</td>
                                        <td>{{ $transferItem->keterangan }}</td>
                                        <td>{{ $transferItem->created_at }}</td>
                                        <td>
                                            @if($transferItem->approved == 0)
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

            $('#barang_masuk_id').on('change', function() {
                let barang_masuk_id = $(this).val();
                $.ajax({
                    url: '/transfer-item/get-data',
                    type: 'GET',
                    data: {
                        barang_masuk_id: barang_masuk_id
                    },
                    success: function(response) {
                        $('#previous_location').val(`${response.lokasi}`);
                    }
                });
            });

        });
    </script>

@endsection