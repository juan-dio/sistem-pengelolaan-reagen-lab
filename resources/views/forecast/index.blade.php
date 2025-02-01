@extends('layouts.app')

@section('content')

<div class="section-header">
    <h1>Perkiraan Penggunaan Barang dalam 6 Bulan ke Depan</h1>
    <div class="ml-auto">
        <a href="javascript:void(0)" class="btn btn-success" id="excel-forecast"><i class="fa fa-table"></i> Export Excel</a>
        <a href="javascript:void(0)" class="btn btn-danger" id="print-forecast"><i class="fa fa-sharp fa-light fa-print"></i> Print PDF</a>
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
                                @for ($i = 1; $i <= 6; $i++)
                                    <th>{{ now()->addMonths($i)->format('F Y') }}</th>
                                @endfor
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($forecastResults as $result)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $result['kode_barang'] }}</td>
                                <td>{{ $result['barang'] }}</td>
                                @foreach ($result['forecast'] as $forecast)
                                    <td>{{ $forecast }} {{ $result['satuan'] }}</td>
                                @endforeach
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

        $('#print-forecast').on('click', function(){
            window.location.href = '/forecast/print-forecast';
        });

        $('#excel-forecast').on('click', function(){
            window.location.href = '/forecast/excel';
        });
    });
</script>
@endsection
