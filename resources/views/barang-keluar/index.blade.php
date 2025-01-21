@extends('layouts.app')

@include('barang-keluar.create')

@section('content')
    <div class="section-header">
        <h1>Barang Keluar</h1>
        <div class="ml-auto">
            <a href="javascript:void(0)" class="btn btn-primary" id="button_tambah_barangKeluar"><i class="fa fa-plus"></i>
                Barang Keluar</a>
        </div>
    </div>


    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="table_id" class="display" style="width: 100%; font-size: 14px;">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kode Transaksi</th>
                                    <th>Tanggal Keluar</th>
                                    <th>Nama Barang</th>
                                    <th>Jumlah Keluar</th>
                                    <th>Alat</th>
                                    <th>Status</th>
                                    <th>Opsi</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Disable enter key on form if not in textarea or button -->
    <script>
        $(document).ready(function() {
            $(window).keydown(function(event) {
                if (event.keyCode == 13) {
                    if (event.target.tagName != 'TEXTAREA' && event.target.tagName != 'BUTTON') {
                        event.preventDefault();
                        return false;
                    }
                }
            });
        });
    </script>

    <!-- Select2 Autocomplete -->
    <script>
        $(document).ready(function() {
            setTimeout(function() {
                $('.js-example-basic-single').select2();

                $('#barang_id').on('change', function() {
                    var selectedOption = $(this).find('option:selected');
                    var barang_id = selectedOption.val();

                    $.ajax({
                        url: '/barang-keluar/get-autocomplete-data',
                        type: 'GET',
                        data: {
                            barang_id: barang_id,
                        },
                        success: function(response) {
                            if (response && response.kode_barang) {
                                let kodeTransaksi = generateKodeTransaksi(response.kode_barang);
                                $('#kode_transaksi').val(kodeTransaksi);
                            }
                            if (response && (response.stok || response.stok === 0) && response.satuan) {
                                $('#stok').val(response.stok);
                                $('#satuan_id').val(response.satuan);
                            } else if (response && response.stok === 0) {
                                $('#stok').val(0);
                                $('#satuan_id').val('');
                            }
                        },
                    });

                    function generateKodeTransaksi(kodeBarang) {
                        let tanggal = new Date().toLocaleDateString('id-ID').split('/').reverse().join('-');
                        let randomNumber = Math.floor(Math.random() * 10000).toString().padStart(4, '0');
                        let kodeTransaksi = kodeBarang + '-OUT-' + tanggal + '-' + randomNumber;

                        return kodeTransaksi;
                    }
                });
            }, 500);
        });
    </script>

    <!-- Datatable -->
    <script>
        $(document).ready(function() {
            $('#table_id').DataTable({
                paging: true
            });

            $.ajax({
                url: "/barang-keluar/get-data",
                type: "GET",
                dataType: 'JSON',
                success: function(response) {
                    console.log(response.data);
                    let counter = 1;
                    $('#table_id').DataTable().clear();
                    $.each(response.data, function(key, value) {
                        let barangKeluar = `
                            <tr class="barang-row" id="index_${value.id}">
                                <td>${counter++}</td>   
                                <td>${value.kode_transaksi}</td>
                                <td>${value.tanggal_keluar}</td>
                                <td>${value.barang.nama_barang}</td>
                                <td>${value.jumlah_keluar} ${value.barang.satuan.satuan}</td>
                                <td>${value.alat.alat}</td>
                                <td>
                                    ${value.approved == 0 ? '<span class="badge bg-warning text-white">pending</span>' : '<span class="badge bg-success text-white">approved</span>'}
                                <td>   
                                    <a href="javascript:void(0)" id="button_hapus_barangKeluar" data-id="${value.id}" class="btn btn-icon btn-danger btn-lg mb-2"><i class="fas fa-trash"></i> </a>
                                </td>
                            </tr>
                        `;
                        $('#table_id').DataTable().row.add($(barangKeluar)).draw(false);
                    });
                }
            });
        });
    </script>

    <!-- Show Modal Tambah Barang Keluar -->
    <script>
        $('body').on('click', '#button_tambah_barangKeluar', function() {
            $('#modal_tambah_barangKeluar').modal('show');
        });

        $('#store').click(function(e) {
            e.preventDefault();

            let kode_transaksi = $('#kode_transaksi').val();
            let tanggal_keluar = $('#tanggal_keluar').val();
            let barang_id = $('#barang_id').val();
            let jumlah_keluar = $('#jumlah_keluar').val();
            let alat_id = $('#alat_id').val();
            let token = $("meta[name='csrf-token']").attr("content");

            let formData = new FormData();
            formData.append('kode_transaksi', kode_transaksi);
            formData.append('tanggal_keluar', tanggal_keluar);
            formData.append('barang_id', barang_id);
            formData.append('jumlah_keluar', jumlah_keluar);
            formData.append('alat_id', alat_id);
            formData.append('_token', token);

            $.ajax({
                url: '/barang-keluar',
                type: "POST",
                cache: false,
                data: formData,
                contentType: false,
                processData: false,

                success: function(response) {

                    Swal.fire({
                        icon: 'success',
                        title: `${response.message}`,
                        showConfirmButton: true,
                        timer: 3000
                    });

                    $.ajax({
                        url: '/barang-keluar/get-data',
                        type: "GET",
                        cache: false,
                        success: function(response) {
                            $('#table-barangs').html('');

                            let counter = 1;
                            $('#table_id').DataTable().clear();
                            $.each(response.data, function(key, value) {
                                let barangKeluar = `
                                    <tr class="barang-row" id="index_${value.id}">
                                        <td>${counter++}</td>   
                                        <td>${value.kode_transaksi}</td>
                                        <td>${value.tanggal_keluar}</td>
                                        <td>${value.barang.nama_barang}</td>
                                        <td>${value.jumlah_keluar} ${value.barang.satuan.satuan}</td>
                                        <td>${value.alat.alat}</td>
                                        <td>
                                            ${value.approved == 0 ? '<span class="badge bg-warning text-white">pending</span>' : '<span class="badge bg-success text-white">approved</span>'}
                                        </td>
                                        <td>
                                            <a href="javascript:void(0)" id="button_hapus_barangKeluar" data-id="${value.id}" class="btn btn-icon btn-danger btn-lg mb-2"><i class="fas fa-trash"></i> </a>
                                        </td>
                                    </tr>
                                `;
                                $('#table_id').DataTable().row.add($(barangKeluar))
                                    .draw(false);
                            });

                            $('#kode_transaksi').val('');
                            $('#barang_id').val('').prop('selectedIndex', 0).trigger('change');
                            $('#alat_id').val('').prop('selectedIndex', 0).trigger('change');
                            $('#jumlah_keluar').val('');
                            $('#stok').val('');

                            $('#modal_tambah_barangKeluar').modal('hide');

                            $('#alert-kode_transaksi').removeClass('d-block').addClass('d-none');
                            $('#alert-tanggal_keluar').removeClass('d-block').addClass('d-none');
                            $('#alert-barang_id').removeClass('d-block').addClass('d-none');
                            $('#alert-jumlah_keluar').removeClass('d-block').addClass('d-none');
                            $('#alert-alat_id').removeClass('d-block').addClass('d-none');

                            let table = $('#table_id').DataTable();
                            table.draw(); // memperbarui Datatables
                        },
                    })
                },

                error: function(error) {
                    // Menyembunyikan semua alert error
                    $('#modal_tambah_barangKeluar').on('hidden.bs.modal', function() {
                        // Mengatur ulang tampilan alert error
                        $('.alert').removeClass('d-block').addClass('d-none');
                    });


                    if (error.responseJSON && error.responseJSON.kode_transaksi && error.responseJSON
                        .kode_transaksi[0]) {
                        $('#alert-kode_transaksi').removeClass('d-none').addClass('d-block');
                        $('#alert-kode_transaksi').html(error.responseJSON.kode_transaksi[0]);
                    } else {
                        $('#alert-kode_transaksi').removeClass('d-block').addClass('d-none');
                    }

                    if (error.responseJSON && error.responseJSON.tanggal_keluar && error.responseJSON
                        .tanggal_keluar[0]) {
                        $('#alert-tanggal_keluar').removeClass('d-none').addClass('d-block');
                        $('#alert-tanggal_keluar').html(error.responseJSON.tanggal_keluar[0]);
                    } else {
                        $('#alert-tanggal_keluar').removeClass('d-block').addClass('d-none');
                    }

                    if (error.responseJSON && error.responseJSON.barang_id && error.responseJSON
                        .barang_id[0]) {
                        $('#alert-barang_id').removeClass('d-none').addClass('d-block');
                        $('#alert-barang_id').html(error.responseJSON.barang_id[0]);
                    } else {
                        $('#alert-barang_id').removeClass('d-block').addClass('d-none');
                    }

                    if (error.responseJSON && error.responseJSON.jumlah_keluar && error.responseJSON
                        .jumlah_keluar[0]) {
                        $('#alert-jumlah_keluar').removeClass('d-none').addClass('d-block');
                        $('#alert-jumlah_keluar').html(error.responseJSON.jumlah_keluar[0]);
                    } else {
                        $('#alert-jumlah_keluar').removeClass('d-block').addClass('d-none');
                    }

                    if (error.responseJSON && error.responseJSON.alat_id && error.responseJSON
                        .alat_id[0]) {
                        $('#alert-alat_id').removeClass('d-none').addClass('d-block');
                        $('#alert-alat_id').html(error.responseJSON.alat_id[0]);
                    } else {
                        $('#alert-alat_id').removeClass('d-block').addClass('d-none');
                    }

                    // Menampilkan kembali modal tambah jika terjadi error
                    $('#modal_tambah_barangKeluar').modal('show');

                }

            });
        });
    </script>


    <!-- Hapus Data Barang -->
    <script>
        $('body').on('click', '#button_hapus_barangKeluar', function() {
            let barangKeluar_id = $(this).data('id');
            let token = $("meta[name='csrf-token']").attr("content");

            Swal.fire({
                title: 'Apakah Kamu Yakin?',
                text: "ingin menghapus data ini !",
                icon: 'warning',
                showCancelButton: true,
                cancelButtonText: 'TIDAK',
                confirmButtonText: 'YA, HAPUS!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/barang-keluar/${barangKeluar_id}`,
                        type: "DELETE",
                        cache: false,
                        data: {
                            "_token": token
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: `${response.message}`,
                                showConfirmButton: true,
                                timer: 3000
                            });
                            $(`#index_${barangKeluar_id}`).remove();

                            $.ajax({
                                url: "/barang-keluar/get-data",
                                type: "GET",
                                dataType: 'JSON',
                                success: function(response) {
                                    let counter = 1;
                                    $('#table_id').DataTable().clear();
                                    $.each(response.data, function(key, value) {
                                        let barangKeluar = `
                                            <tr class="barang-row" id="index_${value.id}">
                                                <td>${counter++}</td>   
                                                <td>${value.kode_transaksi}</td>
                                                <td>${value.tanggal_keluar}</td>
                                                <td>${value.barang.nama_barang}</td>
                                                <td>${value.jumlah_keluar} ${value.barang.satuan.satuan}</td>
                                                <td>${value.alat.alat}</td>
                                                <td>
                                                    ${value.approved == 0 ? '<span class="badge bg-warning text-white">pending</span>' : '<span class="badge bg-success text-white">approved</span>'}
                                                </td>
                                                <td>       
                                                    <a href="javascript:void(0)" id="button_hapus_barangKeluar" data-id="${value.id}" class="btn btn-icon btn-danger btn-lg mb-2"><i class="fas fa-trash"></i> </a>
                                                </td>
                                            </tr>
                                        `;
                                        $('#table_id').DataTable().row.add(
                                            $(barangKeluar)).draw(false);
                                    });
                                }
                            });
                        },
                        error: function(error) {
                            Swal.fire({
                                icon: 'error',
                                title: `${error.responseJSON.message}`,
                                showConfirmButton: true,
                                timer: 3000
                            });
                        }
                    })
                }
            });
        });
    </script>

    <!-- Create Tanggal -->
    <script>
        // Mendapatkan tanggal hari ini
        var today = new Date();

        // Mendapatkan nilai tahun, bulan, dan tanggal
        var year = today.getFullYear();
        var month = (today.getMonth() + 1).toString().padStart(2, '0'); // Ditambahkan +1 karena indeks bulan dimulai dari 0
        var day = today.getDate().toString().padStart(2, '0');

        // Menggabungkan nilai tahun, bulan, dan tanggal menjadi format "YYYY-MM-DD"
        var formattedDate = year + '-' + month + '-' + day;

        // Mengisi nilai input field dengan tanggal hari ini
        document.getElementById('tanggal_keluar').value = formattedDate;
    </script>
@endsection
