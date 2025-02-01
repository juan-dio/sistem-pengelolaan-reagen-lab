@extends('layouts.app')

@section('content')

<div class="section-header">
    <h1>Saldo Awal Item</h1>
    <div class="ml-auto">
        <a href="javascript:void(0)" class="btn btn-success" id="excel-saldo-awal"><i class="fa fa-table"></i> Export Excel</a>
        <a href="javascript:void(0)" class="btn btn-danger" id="print-saldo-awal"><i class="fa fa-sharp fa-light fa-print"></i> Print PDF</a>
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
                                <th>Tanggal</th>
                                <th>Kode Item</th>
                                <th>Nama Item</th>
                                <th>Jumlah</th>
                                <th>Harga</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody id="tabel-saldo-awal-item">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Dropdown -->
<script>
    $(document).ready(function() {
        let table = $('#table_id').DataTable({
            paging: true
        });

        loadData();

        function loadData() {
            $.ajax({
                url: '/saldo-awal-item/get-data',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    table.clear().draw(); // Hapus data yang sudah ada dari DataTable sebelum menambahkan data yang baru

                    if (response.length > 0) {
                        $.each(response, function(index, item) {
                            let row = [
                                (index + 1),
                                item.tanggal,
                                item.barang.kode_barang,
                                item.barang.nama_barang,
                                `${item.jumlah} ${item.barang.satuan.satuan}`,
                                `Rp ${formatNumber(item.harga.toString())},00`,
                                `Rp ${formatNumber((item.harga * item.jumlah).toString())},00`
                            ];
                            table.row.add(row).draw(false); // Tambahkan data yang baru ke DataTable
                        });
                    } else {
                        // let emptyRow = ['','Tidak ada data yang tersedia.', '', '', '', '', ''];
                        table.row.add(emptyRow).draw(false); // Tambahkan baris kosong ke DataTable
                    }
                },
                error: function(error) {
                    console.log(error);
                }
            });
        }

        function formatNumber(n) {
            // format number 1000000 to 1.234.567
            return n.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ".")
        }

        $('#print-saldo-awal').on('click', function(){
            window.location.href = '/saldo-awal-item/print-saldo-awal-item';
        });

        $('#excel-saldo-awal').on('click', function(){            
            window.location.href = '/saldo-awal-item/excel';
        });
    });
</script>

@endsection