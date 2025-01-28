@extends('layouts.app')

@include('order.create')
@include('order.edit')

@section('content')
    <div class="section-header">
        <h1>Order</h1>
        <div class="ml-auto">
            <a href="javascript:void(0)" class="btn btn-primary" id="button_tambah_order"><i class="fa fa-plus"></i>
                Order</a>
        </div>
    </div>


    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive" style="overflow-x: auto;">
                        <table id="table_id" class="display" style="width: 100%; font-size: 14px;">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kode Transaksi</th>
                                    <th>Tanggal</th>
                                    <th>Nama Barang</th>
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
                    let selectedOption = $(this).find('option:selected');
                    let barang_id = selectedOption.val();

                    $.ajax({
                        url: '/order/get-autocomplete-data',
                        type: 'GET',
                        data: {
                            barang_id: barang_id,
                        },
                        success: function(response) {
                            if (response && response.kode_barang) {
                                let kodeTransaksi = generateKodeTransaksi(response.kode_barang);
                                $('#kode_transaksi').val(kodeTransaksi);
                            } else {
                                $('#kode_transaksi').val('');
                            }
                        },
                    });

                });
            }, 500);
            function generateKodeTransaksi(kodeBarang) {
                let tanggal = new Date().toLocaleDateString('id-ID').split('/').reverse().join('-');
                let randomNumber = Math.floor(Math.random() * 10000).toString().padStart(4, '0');
                let kodeTransaksi = kodeBarang + '-ORDER-' + tanggal + '-' + randomNumber;

                return kodeTransaksi;
            }
        });
    </script>

    <!-- Datatable -->
    <script>
        $(document).ready(function() {
            $('#table_id').DataTable({
                paging: true
            });

            $.ajax({
                url: "/order/get-data",
                type: "GET",
                dataType: 'JSON',
                success: function(response) {
                    let counter = 1;
                    $('#table_id').DataTable().clear();
                    $.each(response.data, function(key, value) {
                        let order = `
                            <tr class="barang-row" id="index_${value.id}">
                                <td>${counter++}</td>   
                                <td>${value.kode_transaksi}</td>
                                <td>${value.tanggal}</td>
                                <td>${value.barang.nama_barang}</td>
                                <td>
                                    ${value.status}
                                </td>
                                <td>
                                    <a href="javascript:void(0)" id="button_edit_order" data-id="${value.id}" class="btn btn-icon btn-warning btn-lg mb-2"><i class="fas fa-pen"></i> </a>
                                    <a href="javascript:void(0)" id="button_hapus_order" data-id="${value.id}" class="btn btn-icon btn-danger btn-lg mb-2"><i class="fas fa-trash"></i> </a>
                                </td>
                            </tr>
                        `;
                        $('#table_id').DataTable().row.add($(order)).draw(false);
                    });
                }
            });
        });
    </script>

    <!-- Show Modal Tambah Barang Masuk -->
    <script>
        $('body').on('click', '#button_tambah_order', function() {
            $('#modal_tambah_order').modal('show');
        });

        $('#store').click(function(e) {
            e.preventDefault();

            let kode_transaksi = $('#kode_transaksi').val();
            let tanggal = $('#tanggal').val();
            let barang_id = $('#barang_id').val();
            let token = $("meta[name='csrf-token']").attr("content");

            let formData = new FormData();
            formData.append('kode_transaksi', kode_transaksi);
            formData.append('tanggal', tanggal);
            formData.append('barang_id', barang_id);
            formData.append('_token', token);

            $.ajax({
                url: '/order',
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
                        url: '/order/get-data',
                        type: "GET",
                        cache: false,
                        success: function(response) {
                            let counter = 1;
                            $('#table_id').DataTable().clear();
                            $.each(response.data, function(key, value) {
                                let order = `
                                    <tr class="barang-row" id="index_${value.id}">
                                        <td>${counter++}</td>   
                                        <td>${value.kode_transaksi}</td>
                                        <td>${value.tanggal}</td>
                                        <td>${value.barang.nama_barang}</td>
                                        <td>
                                            ${value.status}
                                        </td>
                                        <td>
                                            <a href="javascript:void(0)" id="button_edit_order" data-id="${value.id}" class="btn btn-icon btn-warning btn-lg mb-2"><i class="fas fa-pen"></i> </a>
                                            <a href="javascript:void(0)" id="button_hapus_order" data-id="${value.id}" class="btn btn-icon btn-danger btn-lg mb-2"><i class="fas fa-trash"></i> </a>
                                        </td>
                                    </tr>
                                `;
                                $('#table_id').DataTable().row.add($(order)).draw(false);
                            });

                            $('#kode_transaksi').val('');
                            $('#barang_id').val('').prop('selectedIndex', 0).trigger('change');

                            $('#modal_tambah_order').modal('hide');

                            $('#alert-kode_transaksi').removeClass('d-block').addClass('d-none');
                            $('#alert-tanggal').removeClass('d-block').addClass('d-none');
                            $('#alert-barang_id').removeClass('d-block').addClass('d-none');

                            let table = $('#table_id').DataTable();
                            table.draw(); // memperbarui Datatables
                        },
                        error: function(error) {
                            console.log(error);
                        }
                    })
                },

                error: function(error) {
                    if (error.responseJSON && error.responseJSON.kode_transaksi && error.responseJSON
                        .kode_transaksi[0]) {
                        $('#alert-kode_transaksi').removeClass('d-none').addClass('d-block');
                        $('#alert-kode_transaksi').html(error.responseJSON.kode_transaksi[0]);
                    } else {
                        $('#alert-kode_transaksi').removeClass('d-block').addClass('d-none');
                    }

                    if (error.responseJSON && error.responseJSON.tanggal && error.responseJSON
                        .tanggal[0]) {
                        $('#alert-tanggal').removeClass('d-none').addClass('d-block');
                        $('#alert-tanggal').html(error.responseJSON.tanggal[0]);
                    } else {
                        $('#alert-tanggal').removeClass('d-block').addClass('d-none');
                    }

                    if (error.responseJSON && error.responseJSON.barang_id && error.responseJSON
                        .barang_id[0]) {
                        $('#alert-barang_id').removeClass('d-none').addClass('d-block');
                        $('#alert-barang_id').html(error.responseJSON.barang_id[0]);
                    } else {
                        $('#alert-barang_id').removeClass('d-block').addClass('d-none');
                    }
                }
            });
        });
    </script>

    <!-- Show Modal Ubah Status -->
    <script>
        $('body').on('click', '#button_edit_order', function() {
            let order_id = $(this).data('id');
            let token = $("meta[name='csrf-token']").attr("content");

            $.ajax({
                url: `/order/${order_id}/edit`,
                type: "GET",
                cache: false,
                success: function(response) {
                    $('#modal_edit_order').modal('show');
                    $('#order_id').val(response.data.id);
                    $('#status').val(response.data.status).trigger('change');
                },
                error: function(error) {
                    console.log(error);
                }
            });
        });

        $('#update').click(function(e) {
            e.preventDefault();

            let order_id = $('#order_id').val();
            let status = $('#status').val();
            let token = $("meta[name='csrf-token']").attr("content");

            $.ajax({
                url: `/order/${order_id}`,
                type: "PUT",
                cache: false,
                data: {
                    status: status,
                    _token: token
                },
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: `${response.message}`,
                        showConfirmButton: true,
                        timer: 3000
                    });

                    $('#modal_edit_order').modal('hide');

                    $.ajax({
                        url: "/order/get-data",
                        type: "GET",
                        dataType: 'JSON',
                        success: function(response) {
                            let counter = 1;
                            $('#table_id').DataTable().clear();
                            $.each(response.data, function(key, value) {
                                let order = `
                                    <tr class="barang-row" id="index_${value.id}">
                                        <td>${counter++}</td>   
                                        <td>${value.kode_transaksi}</td>
                                        <td>${value.tanggal}</td>
                                        <td>${value.barang.nama_barang}</td>
                                        <td>
                                            ${value.status}
                                        </td>
                                        <td>
                                            <a href="javascript:void(0)" id="button_edit_order" data-id="${value.id}" class="btn btn-icon btn-warning btn-lg mb-2"><i class="fas fa-pen"></i> </a>
                                            <a href="javascript:void(0)" id="button_hapus_order" data-id="${value.id}" class="btn btn-icon btn-danger btn-lg mb-2"><i class="fas fa-trash"></i> </a>
                                        </td>
                                    </tr>
                                `;
                                $('#table_id').DataTable().row.add($(order)).draw(false);
                            });
                        }
                    });
                }
            });
        });
    </script>

    <!-- Hapus Data Barang -->
    <script>
        $('body').on('click', '#button_hapus_order', function() {
            let order_id = $(this).data('id');
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
                        url: `/order/${order_id}`,
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

                            // Hapus data dari cache DataTables
                            $('#table_id').DataTable().clear().draw();

                            $.ajax({
                                url: "/order/get-data",
                                type: "GET",
                                dataType: 'JSON',
                                success: function(response) {
                                    let counter = 1;
                                    $('#table_id').DataTable().clear();
                                    $.each(response.data, function(key, value) {
                                        let order = `
                                            <tr class="barang-row" id="index_${value.id}">
                                                <td>${counter++}</td>   
                                                <td>${value.kode_transaksi}</td>
                                                <td>${value.tanggal}</td>
                                                <td>${value.barang.nama_barang}</td>
                                                <td>
                                                    ${value.status}
                                                </td>
                                                <td>
                                                    <a href="javascript:void(0)" id="button_edit_order" data-id="${value.id}" class="btn btn-icon btn-warning btn-lg mb-2"><i class="fas fa-pen"></i> </a>
                                                    <a href="javascript:void(0)" id="button_hapus_order" data-id="${value.id}" class="btn btn-icon btn-danger btn-lg mb-2"><i class="fas fa-trash"></i> </a>
                                                </td>
                                            </tr>
                                        `;
                                        $('#table_id').DataTable().row.add($(order)).draw(false);
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
                    });
                }
            });
        });
    </script>

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
        document.getElementById('tanggal').value = formattedDate;
    </script>
@endsection
