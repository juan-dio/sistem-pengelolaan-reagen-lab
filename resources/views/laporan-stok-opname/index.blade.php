@extends('layouts.app')

@section('content')

<div class="section-header">
    <h1>Laporan Stok Opname</h1>
    <div class="ml-auto">
        <a href="javascript:void(0)" class="btn btn-success" id="excel-stok-opname"><i class="fa fa-table"></i> Export Excel</a>
        <a href="javascript:void(0)" class="btn btn-danger" id="print-stok-opname"><i class="fa fa-sharp fa-light fa-print"></i> Print PDF</a>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="form-group">
                    <form id="filter_form" action="/laporan-stok-opname/get-data" method="GET">
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
                                <th>Barang</th>
                                <th>Stok Sistem</th>
                                <th>Stok Fisik</th>
                                <th>Selisih</th>
                                <th>Keterangan</th>
                                <th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody id="tabel-laporan-stok-opname">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        let table = $('#table_id').DataTable({ paging: true }); // Simpan objek DataTable dalam letiabel

        loadData(); // Panggil fungsi loadData saat halaman dimuat

        $('#filter_form').submit(function(event) {
            event.preventDefault();
            loadData(); // Panggil fungsi loadData saat tombol filter ditekan
        });

        $('#refresh_btn').on('click', function() {
            refreshTable();
        });

        // Fungsi load data berdasarkan range tanggal_mulai dan tanggal_selesai
        function loadData() {
            let tanggalMulai = $('#tanggal_mulai').val();
            let tanggalSelesai = $('#tanggal_selesai').val();

            $.ajax({
                url: '/laporan-stok-opname/get-data',
                type: 'GET',
                dataType: 'json',
                data: {
                    tanggal_mulai: tanggalMulai,
                    tanggal_selesai: tanggalSelesai
                },
                success: function(response) {
                    table.clear().draw(); // Hapus data yang sudah ada dari DataTable sebelum menambahkan data yang baru

                    if (response.length > 0) {
                        $.each(response, function(index, item) {
                            let row = [
                                (index + 1),
                                item.barang.nama_barang,
                                `${item.barang.stok} ${item.barang.satuan.satuan}`,
                                `${item.stok_fisik} ${item.barang.satuan.satuan}`,
                                `${item.stok_sistem - item.stok_fisik} ${item.barang.satuan.satuan}`,
                                item.keterangan,
                                item.created_at.split('T')[0]
                            ];
                            table.row.add(row).draw(false); // Tambahkan data yang baru ke DataTable
                        });
                    } else {
                        // let emptyRow = ['','Tidak ada data yang tersedia.', '', '', '', '', ''];
                        table.row.add(emptyRow).draw(false); // Tambahkan baris kosong ke DataTable
                    }
                },
                error: function(xhr, status, error) {
                    console.log(error);
                }
            });
        }

        // Fungsi Refresh Tabel
        function refreshTable() {
            $('#filter_form')[0].reset();
            loadData();
        }

        // Print barang masuk
        $('#print-stok-opname').on('click', function() {
            let tanggalMulai = $('#tanggal_mulai').val();
            let tanggalSelesai = $('#tanggal_selesai').val();

            let url = '/laporan-stok-opname/print-stok-opname';

            if (tanggalMulai && tanggalSelesai) {
                url += '?tanggal_mulai=' + tanggalMulai + '&tanggal_selesai=' + tanggalSelesai;
            }

            window.location.href = url;
        });

        $('#excel-stok-opname').on('click', function(){
            let tanggalMulai    = $('#tanggal_mulai').val();
            let tanggalSelesai  = $('#tanggal_selesai').val();
            
            let url = '/laporan-stok-opname/excel';

            if(tanggalMulai && tanggalSelesai){
                url += '?tanggal_mulai=' + tanggalMulai + '&tanggal_selesai=' + tanggalSelesai;
            }

            window.location.href = url;
        });
    });

</script>
@endsection