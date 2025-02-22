@extends('layouts.app')

@section('content')

<div class="section-header">
    <h1>Laporan Barang Keluar</h1>
    <div class="ml-auto">
        <a href="javascript:void(0)" class="btn btn-success" id="excel-barang-keluar"><i class="fa fa-table"></i> Export Excel</a>
        <a href="javascript:void(0)" class="btn btn-danger" id="print-barang-keluar"><i class="fa fa-sharp fa-light fa-print"></i> Print PDF</a>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="form-group">
                    <form id="filter_form" action="/laporan-barang-keluar/get-data" method="GET">
                        <div class="row">
                            <div class="col-md-5">
                                <label>Pilih Tanggal Mulai :</label>
                                <input type="date" class="form-control" name="tanggal_mulai" id="tanggal_mulai">
                            </div>
                            <div class="col-md-5">
                                <label>Pilih Tanggal Selesai :</label>
                                <input type="date" class="form-control" name="tanggal_selesai" id="tanggal_selesai">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary">Filter</button>
                                <button type="button" class="btn btn-danger" id="refresh_btn">Refresh</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="table_id" class="display">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kode Transaksi</th>
                                <th>Tanggal Keluar</th>
                                <th>Kode Barang</th>
                                <th>Nama Barang</th>
                                <th>Jumlah Keluar</th>
                                <th>Alat</th>
                            </tr>
                        </thead>
                        <tbody id="tabel-laporan-barang-keluar">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Script Get Data -->
<script>
    $(document).ready(function() {
        let table = $('#table_id').DataTable({ paging: true});

        loadData(); // Panggil fungsi loadData saat halaman dimuat

        $('#filter_form').submit(function(event) {
            event.preventDefault();
            loadData(); // Panggil fungsi loadData saat tombol filter ditekan
        });

        $('#refresh_btn').on('click', function() {
            refreshTable();
        });

        //Fungsi load data berdasarkan range tanggal_mulai dan tanggal_selesai
        function loadData() {
            let tanggalMulai = $('#tanggal_mulai').val();
            let tanggalSelesai = $('#tanggal_selesai').val();
            
            $.ajax({
                url: '/laporan-barang-keluar/get-data',
                type: 'GET',
                dataType: 'json',
                data: {
                    tanggal_mulai: tanggalMulai,
                    tanggal_selesai: tanggalSelesai
                },
                success: function(response) {
                    table.clear().draw();

                    if (response.length > 0) {
                        $.each(response, function(index, item) {
                            let row = [
                                (index + 1),
                                item.kode_transaksi,
                                item.tanggal_keluar,
                                item.barang.kode_barang,
                                item.barang.nama_barang,
                                `${item.jumlah_keluar} ${item.barang.satuan.satuan}`,
                                item.alat.alat
                            ];
                            table.row.add(row).draw(false);
                        });
                    } else {
                        // let emptyRow = ['','Tidak ada data yang tersedia.', '', '', '', ''];
                        table.row.add(emptyRow).draw(false); // Tambahkan baris kosong ke DataTable
                    }
                },
                error: function(xhr, status, error) {
                    console.log(error);
                }
            });
        }

        //Fungsi Refresh Tabel
        function refreshTable(){
            $('#filter_form')[0].reset();
            loadData();
        }

        //Print barang keluar
        $('#print-barang-keluar').on('click', function(){
            let tanggalMulai    = $('#tanggal_mulai').val();
            let tanggalSelesai  = $('#tanggal_selesai').val();
            
            let url = '/laporan-barang-keluar/print-barang-keluar';

            if(tanggalMulai && tanggalSelesai){
                url += '?tanggal_mulai=' + tanggalMulai + '&tanggal_selesai=' + tanggalSelesai;
            }

            window.location.href = url;
        });

        //Export Excel barang keluar
        $('#excel-barang-keluar').on('click', function(){
            let tanggalMulai    = $('#tanggal_mulai').val();
            let tanggalSelesai  = $('#tanggal_selesai').val();
            
            let url = '/laporan-barang-keluar/excel';

            if(tanggalMulai && tanggalSelesai){
                url += '?tanggal_mulai=' + tanggalMulai + '&tanggal_selesai=' + tanggalSelesai;
            }

            window.location.href = url;
        });

    });
</script>



@endsection