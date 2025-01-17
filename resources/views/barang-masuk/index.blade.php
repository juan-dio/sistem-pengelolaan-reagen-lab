@extends('layouts.app')

@include('barang-masuk.create')

@section('content')
    <div class="section-header">
        <h1>Barang Masuk</h1>
        <div class="ml-auto">
            <a href="javascript:void(0)" class="btn btn-primary" id="button_tambah_barangMasuk"><i class="fa fa-plus"></i>
                Barang Masuk</a>
        </div>
    </div>


    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive" style="overflow-x: auto;">
                        <table id="table_id" class="display">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kode Transaksi</th>
                                    <th>Tanggal Masuk</th>
                                    <th>Tanggal Kadaluarsa</th>
                                    <th>Nama Barang</th>
                                    <th>Jumlah Masuk</th>
                                    <th>Stok</th>
                                    <th>Lokasi</th>
                                    <th>Supplier</th>
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
                        url: '/api/barang-masuk',
                        type: 'GET',
                        data: {
                            barang_id: barang_id,
                        },
                        success: function(response) {
                            if (response && response.barang) {
                                let kodeTransaksi = generateKodeTransaksi(response.barang.kode_barang);
                                $('#kode_transaksi').val(kodeTransaksi);
                            }
                            if (response && (response.stok || response.stok === 0) && response.satuan_id) {
                                $('#stok').val(response.stok);
                                getSatuanName(response.satuan_id, function(satuan) {
                                    $('#satuan_id').val(satuan);
                                });
                            } else if (response && response.stok === 0) {
                                $('#stok').val(0);
                                $('#satuan_id').val('');
                            }
                        },
                    });

                    function getSatuanName(satuanId, callback) {
                        $.getJSON('{{ url('api/satuan') }}', function(satuans) {
                            let satuan = satuans.find(function(s) {
                                return s.id === satuanId;
                            });
                            callback(satuan ? satuan.satuan : '');
                        });
                    }

                    function generateKodeTransaksi(kodeBarang) {
                        let tanggal = new Date().toLocaleDateString('id-ID').split('/').reverse().join('-');
                        let randomNumber = Math.floor(Math.random() * 10000).toString().padStart(4, '0');
                        let kodeTransaksi = kodeBarang + '-IN-' + tanggal + '-' + randomNumber;

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
                url: "/barang-masuk/get-data",
                type: "GET",
                dataType: 'JSON',
                success: function(response) {
                    let counter = 1;
                    $('#table_id').DataTable().clear();
                    $.each(response.data, function(key, value) {
                        let barang = getBarangName(response.barangs, value.barang_id);
                        let supplier = getSupplierName(response.supplier, value.supplier_id);
                        let barangMasuk = `
                <tr class="barang-row" id="index_${value.id}">
                    <td>${counter++}</td>   
                    <td>${value.kode_transaksi}</td>
                    <td>${value.tanggal_masuk}</td>
                    <td>${value.tanggal_kadaluarsa}</td>
                    <td>${barang}</td>
                    <td>${value.jumlah_masuk}</td>
                    <td>${value.jumlah_stok}</td>
                    <td>${value.lokasi}</td>
                    <td>${supplier}</td>
                    <td>
                        <a href="javascript:void(0)" id="button_hapus_barangMasuk" data-id="${value.id}" class="btn btn-icon btn-danger btn-lg mb-2"><i class="fas fa-trash"></i> </a>
                    </td>
                </tr>
            `;
                        $('#table_id').DataTable().row.add($(barangMasuk)).draw(false);
                    });

                    function getBarangName(barangs, barangId) {
                        let barang = barangs.find(b => b.id === barangId);
                        return barang ? barang.nama_barang : '';
                    }

                    function getSupplierName(suppliers, supplierId) {
                        let supplier = suppliers.find(s => s.id === supplierId);
                        return supplier ? supplier.supplier : '';
                    }
                }
            });
        });
    </script>

    <!-- Generate Kode Transaksi Otomatis -->
    <script>
        // function generateKodeTransaksi() {
        //     var tanggal = new Date().toLocaleDateString('id-ID').split('/').reverse().join('-');
        //     var randomNumber = Math.floor(Math.random() * 10000).toString().padStart(4, '0');
        //     var kodeTransaksi = 'TRX-IN-' + tanggal + '-' + randomNumber;

        //     $('#kode_transaksi').val(kodeTransaksi);
        //     return kodeTransaksi;
        // }

        // $(document).ready(function() {
        //     generateKodeTransaksi();
        // });
    </script>

    <!-- Show Modal Tambah Barang Masuk -->
    <script>
        $('body').on('click', '#button_tambah_barangMasuk', function() {
            $('#modal_tambah_barangMasuk').modal('show');
            // $('#kode_transaksi').val(generateKodeTransaksi());
        });

        $('#store').click(function(e) {
            e.preventDefault();

            let kode_transaksi = $('#kode_transaksi').val();
            let tanggal_masuk = $('#tanggal_masuk').val();
            let tanggal_kadaluarsa = $('#tanggal_kadaluarsa').val();
            let jumlah_masuk = $('#jumlah_masuk').val();
            let jumlah_stok = jumlah_masuk;
            let lokasi = $('#lokasi').val();
            let barang_id = $('#barang_id').val();
            let supplier_id = $('#supplier_id').val();
            let token = $("meta[name='csrf-token']").attr("content");

            let formData = new FormData();
            formData.append('kode_transaksi', kode_transaksi);
            formData.append('tanggal_masuk', tanggal_masuk);
            formData.append('tanggal_kadaluarsa', tanggal_kadaluarsa);
            formData.append('jumlah_masuk', jumlah_masuk);
            formData.append('jumlah_stok', jumlah_stok);
            formData.append('lokasi', lokasi);
            formData.append('barang_id', barang_id);
            formData.append('supplier_id', supplier_id);
            formData.append('_token', token);

            $.ajax({
                url: '/barang-masuk',
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
                        url: '/barang-masuk/get-data',
                        type: "GET",
                        cache: false,
                        success: function(response) {
                            $('#table-barangs').html('');

                            let counter = 1;
                            $('#table_id').DataTable().clear();
                            $.each(response.data, function(key, value) {
                                let barang = getBarangName(response.barangs, value.barang_id);
                                let supplier = getSupplierName(response.supplier, value.supplier_id);
                                let barangMasuk = `
                                <tr class="barang-row" id="index_${value.id}">
                                    <td>${counter++}</td>   
                                    <td>${value.kode_transaksi}</td>
                                    <td>${value.tanggal_masuk}</td>
                                    <td>${value.tanggal_kadaluarsa}</td>
                                    <td>${barang}</td>
                                    <td>${value.jumlah_masuk}</td>
                                    <td>${value.jumlah_stok}</td>
                                    <td>${value.lokasi}</td>
                                    <td>${supplier}</td>
                                    <td>
                                        <a href="javascript:void(0)" id="button_hapus_barangMasuk" data-id="${value.id}" class="btn btn-icon btn-danger btn-lg mb-2"><i class="fas fa-trash"></i> </a>
                                    </td>
                                </tr>
                            `;
                                $('#table_id').DataTable().row.add($(barangMasuk))
                                    .draw(false);
                            });

                            $('#kode_transaksi').val('');
                            $('#barang_id').val('').prop('selectedIndex', 0).trigger('change');
                            $('#supplier_id').val('').prop('selectedIndex', 0).trigger('change');
                            $('#jumlah_masuk').val('');
                            $('#stok').val('');

                            $('#modal_tambah_barangMasuk').modal('hide');

                            $('#alert-kode_transaksi').removeClass('d-block').addClass('d-none');
                            $('#alert-tanggal_masuk').removeClass('d-block').addClass('d-none');
                            $('#alert-tanggal_kadaluarsa').removeClass('d-block').addClass('d-none');
                            $('#alert-barang_id').removeClass('d-block').addClass('d-none');
                            $('#alert-jumlah_masuk').removeClass('d-block').addClass('d-none');
                            $('#alert-supplier_id').removeClass('d-block').addClass('d-none');
                            $('#alert-lokasi').removeClass('d-block').addClass('d-none');

                            let table = $('#table_id').DataTable();
                            table.draw(); // memperbarui Datatables

                            function getBarangName(barangs, barangId) {
                                let barang = barangs.find(b => b.id === barangId);
                                return barang ? barang.nama_barang : '';
                            }

                            function getSupplierName(suppliers, supplierId) {
                                let supplier = suppliers.find(s => s.id === supplierId);
                                return supplier ? supplier.supplier : '';
                            }
                        },
                        error: function(error) {
                            console.log(error);
                        }
                    })
                },

                error: function(error) {
                    if (error.responseJSON && error.responseJSON.kode_transaksi && error.responseJSON
                        .kode_transaksi[0]) {
                        $('#alert-kode_transaksi').removeClass('d-none');
                        $('#alert-kode_transaksi').addClass('d-block');

                        $('#alert-kode_transaksi').html(error.responseJSON.kode_transaksi[0]);
                    } else {
                        $('#alert-kode_transaksi').removeClass('d-block');
                        $('#alert-kode_transaksi').addClass('d-none');
                    }

                    if (error.responseJSON && error.responseJSON.tanggal_masuk && error.responseJSON
                        .tanggal_masuk[0]) {
                        $('#alert-tanggal_masuk').removeClass('d-none');
                        $('#alert-tanggal_masuk').addClass('d-block');

                        $('#alert-tanggal_masuk').html(error.responseJSON.tanggal_masuk[0]);
                    } else {
                        $('#alert-tanggal_masuk').removeClass('d-block');
                        $('#alert-tanggal_masuk').addClass('d-none');
                    }

                    if (error.responseJSON && error.responseJSON.tanggal_kadaluarsa && error.responseJSON
                        .tanggal_kadaluarsa[0]) {
                        $('#alert-tanggal_kadaluarsa').removeClass('d-none');
                        $('#alert-tanggal_kadaluarsa').addClass('d-block');

                        $('#alert-tanggal_kadaluarsa').html(error.responseJSON.tanggal_kadaluarsa[0]);
                    } else {
                        $('#alert-tanggal_kadaluarsa').removeClass('d-block');
                        $('#alert-tanggal_kadaluarsa').addClass('d-none');
                    }

                    if (error.responseJSON && error.responseJSON.barang_id && error.responseJSON
                        .barang_id[0]) {
                        $('#alert-barang_id').removeClass('d-none');
                        $('#alert-barang_id').addClass('d-block');

                        $('#alert-barang_id').html(error.responseJSON.barang_id[0]);
                    } else {
                        $('#alert-barang_id').removeClass('d-block');
                        $('#alert-barang_id').addClass('d-none');
                    }

                    if (error.responseJSON && error.responseJSON.jumlah_masuk && error.responseJSON
                        .jumlah_masuk[0]) {
                        $('#alert-jumlah_masuk').removeClass('d-none');
                        $('#alert-jumlah_masuk').addClass('d-block');

                        $('#alert-jumlah_masuk').html(error.responseJSON.jumlah_masuk[0]);
                    } else {
                        $('#alert-jumlah_masuk').removeClass('d-block');
                        $('#alert-jumlah_masuk').addClass('d-none');
                    }

                    if (error.responseJSON && error.responseJSON.supplier_id && error.responseJSON
                        .supplier_id[0]) {
                        $('#alert-supplier_id').removeClass('d-none');
                        $('#alert-supplier_id').addClass('d-block');

                        $('#alert-supplier_id').html(error.responseJSON.supplier_id[0]);
                    } else {
                        $('#alert-supplier_id').removeClass('d-block');
                        $('#alert-supplier_id').addClass('d-none');
                    }

                    if (error.responseJSON && error.responseJSON.lokasi && error.responseJSON
                        .lokasi[0]) {
                        $('#alert-lokasi').removeClass('d-none');
                        $('#alert-lokasi').addClass('d-block');

                        $('#alert-lokasi').html(error.responseJSON.lokasi[0]);
                    } else {
                        $('#alert-lokasi').removeClass('d-block');
                        $('#alert-lokasi').addClass('d-none');
                    }
                }
            });
        });
    </script>


    <!-- Hapus Data Barang -->
    <script>
        $('body').on('click', '#button_hapus_barangMasuk', function() {
            let barangMasuk_id = $(this).data('id');
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
                        url: `/barang-masuk/${barangMasuk_id}`,
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
                            $(`#index_${barangMasuk_id}`).remove();

                            $.ajax({
                                url: "/barang-masuk/get-data",
                                type: "GET",
                                dataType: 'JSON',
                                success: function(response) {
                                    let counter = 1;
                                    $('#table_id').DataTable().clear();
                                    $.each(response.data, function(key, value) {
                                        let barang = getBarangName(response.barangs, value.barang_id);
                                        let supplier = getSupplierName(response.supplier, value.supplier_id);
                                        let barangMasuk = `
                                        <tr class="barang-row" id="index_${value.id}">
                                            <td>${counter++}</td>   
                                            <td>${value.kode_transaksi}</td>
                                            <td>${value.tanggal_masuk}</td>
                                            <td>${value.tanggal_kadaluarsa}</td>
                                            <td>${barang}</td>
                                            <td>${value.jumlah_masuk}</td>
                                            <td>${value.jumlah_stok}</td>
                                            <td>${value.lokasi}</td>
                                            <td>${supplier}</td>
                                            <td>
                                                <a href="javascript:void(0)" id="button_hapus_barangMasuk" data-id="${value.id}" class="btn btn-icon btn-danger btn-lg mb-2"><i class="fas fa-trash"></i> </a>
                                            </td>
                                        </tr>
                                    `;
                                        $('#table_id').DataTable().row.add(
                                            $(barangMasuk)).draw(false);
                                    });

                                    function getBarangName(barangs, barangId) {
                                        let barang = barangs.find(b => b.id === barangId);
                                        return barang ? barang.nama_barang : '';
                                    }

                                    function getSupplierName(suppliers, supplierId) {
                                        let supplier = suppliers.find(s => s.id === supplierId);
                                        return supplier ? supplier.supplier : '';
                                    }
                                }
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
        document.getElementById('tanggal_masuk').value = formattedDate;
    </script>
@endsection
