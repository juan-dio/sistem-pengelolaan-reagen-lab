@extends('layouts.app')

@include('barang-masuk.create')
@include('barang-masuk.edit')

@section('content')
    <div class="section-header">
        <h1>Barang Masuk</h1>
        <div class="ml-auto">
            <a href="javascript:void(0)" class="btn btn-primary" id="button_tambah_barangMasuk"><i class="fa fa-plus"></i> Barang Masuk</a>
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
                                    <th>Lot</th>
                                    <th>Tanggal Masuk</th>
                                    <th>Expired</th>
                                    <th>Nama Barang</th>
                                    <th>Jumlah Masuk</th>
                                    <th>Outstanding</th>
                                    <th>Harga</th>
                                    <th>Lokasi</th>
                                    <th>Supplier</th>
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

    <script>
        $(document).ready(function() {
            // Format mata uang.
            $("input[data-type='currency']").on({
                keyup: function() {
                    formatCurrency($(this));
                },
                blur: function() { 
                    formatCurrency($(this), "blur");
                }
            });
        });

        function formatNumber(n) {
            // format number 1000000 to 1.234.567
            return n.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ".")
        }

        function formatCurrency(input, blur) {
            // appends $ to value, validates decimal side
            // and puts cursor back in right position.
            
            // get input value
            var input_val = input.val();
            
            // don't validate empty input
            if (input_val === "") { return; }
            
            // original length
            var original_len = input_val.length;

            // initial caret position 
            var caret_pos = input.prop("selectionStart");
                
            // check for decimal
            if (input_val.indexOf(",") >= 0) {

                // get position of first decimal
                // this prevents multiple decimals from
                // being entered
                var decimal_pos = input_val.indexOf(",");

                // split number by decimal point
                var left_side = input_val.substring(0, decimal_pos);
                var right_side = input_val.substring(decimal_pos);

                // add commas to left side of number
                left_side = formatNumber(left_side);

                // validate right side
                right_side = formatNumber(right_side);
                
                // On blur make sure 2 numbers after decimal
                if (blur === "blur") {
                right_side += "00";
                }
                
                // Limit decimal to only 2 digits
                right_side = right_side.substring(0, 2);

                // join number by ,
                input_val = "Rp" + left_side + "," + right_side;

            } else {
                // no decimal entered
                // add commas to number
                // remove all non-digits
                input_val = formatNumber(input_val);
                input_val = "Rp" + input_val;
                
                // final formatting
                if (blur === "blur") {
                input_val += ",00";
                }
            }
            
            // send updated string to input
            input.val(input_val);

            // put caret back in the right position
            var updated_len = input_val.length;
            caret_pos = updated_len - original_len + caret_pos;
            input[0].setSelectionRange(caret_pos, caret_pos);
        }
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
                        url: '/barang-masuk/get-autocomplete-data',
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
                            if (response && (response.stok || response.stok === 0) && response.satuan) {
                                $('#stok').val(response.stok);
                                $('#satuan_id').val(response.satuan);
                            } else if (response && response.stok === 0) {
                                $('#stok').val(0);
                                $('#satuan_id').val('');
                            } else {
                                $('#stok').val('');
                                $('#satuan_id').val('');
                            }
                        },
                    });

                });
            }, 500);
            function generateKodeTransaksi(kodeBarang) {
                let tanggal = new Date().toLocaleDateString('id-ID').split('/').reverse().join('-');
                let randomNumber = Math.floor(Math.random() * 10000).toString().padStart(4, '0');
                let kodeTransaksi = kodeBarang + '-IN-' + tanggal + '-' + randomNumber;

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
                url: "/barang-masuk/get-data",
                type: "GET",
                dataType: 'JSON',
                success: function(response) {
                    let counter = 1;
                    $('#table_id').DataTable().clear();
                    $.each(response.data, function(key, value) {
                        let barangMasuk = `
                            <tr class="barang-row" id="index_${value.id}">
                                <td>${counter++}</td>   
                                <td>${value.kode_transaksi}</td>
                                <td>${value.lot}</td>
                                <td>${value.tanggal_masuk}</td>
                                <td>${value.tanggal_kadaluarsa}</td>
                                <td>${value.barang.nama_barang}</td>
                                <td>${value.jumlah_masuk} ${value.barang.satuan.satuan}</td>
                                <td>${value.outstanding} ${value.barang.satuan.satuan}</td>
                                <td>Rp${formatNumber(value.harga.toString())},00</td>
                                <td>${value.lokasi}</td>
                                <td>${value.supplier.supplier}</td>
                                <td>
                                    ${value.approved == 0 ? '<span class="badge bg-warning text-white">pending</span>' : '<span class="badge bg-success text-white">approved</span>'}
                                </td>
                                <td>
                                    <a href="javascript:void(0)" id="button_edit_outstanding" data-id="${value.id}" data-outstanding="${value.outstanding}" class="btn btn-icon btn-warning btn-lg mb-2"><i class="far fa-edit"></i> </a>
                                    <a href="javascript:void(0)" id="button_hapus_barangMasuk" data-id="${value.id}" class="btn btn-icon btn-danger btn-lg"><i class="fas fa-trash"></i> </a>
                                </td>
                            </tr>
                        `;
                        $('#table_id').DataTable().row.add($(barangMasuk)).draw(false);
                    });
                }
            });
        });
    </script>

    <!-- Show Modal Tambah Barang Masuk -->
    <script>
        $('body').on('click', '#button_tambah_barangMasuk', function() {
            $('#modal_tambah_barangMasuk').modal('show');
        });

        $('#store').click(function(e) {
            e.preventDefault();

            let kode_transaksi = $('#kode_transaksi').val();
            let lot = $('#lot').val();
            let tanggal_masuk = $('#tanggal_masuk').val();
            let tanggal_kadaluarsa = $('#tanggal_kadaluarsa').val();
            let jumlah_masuk = $('#jumlah_masuk').val();
            let outstanding = $('#outstanding').val();
            let jumlah_stok = jumlah_masuk;
            let harga = $('#harga').val().split(',')[0].replace('Rp', '').replace('.', '');
            let lokasi = $('#lokasi').val();
            let barang_id = $('#barang_id').val();
            let supplier_id = $('#supplier_id').val();
            let token = $("meta[name='csrf-token']").attr("content");

            let formData = new FormData();
            formData.append('kode_transaksi', kode_transaksi);
            formData.append('lot', lot);
            formData.append('tanggal_masuk', tanggal_masuk);
            formData.append('tanggal_kadaluarsa', tanggal_kadaluarsa);
            formData.append('jumlah_masuk', jumlah_masuk);
            formData.append('outstanding', outstanding);
            formData.append('jumlah_stok', jumlah_stok);
            formData.append('harga', harga);
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
                            let counter = 1;
                            $('#table_id').DataTable().clear();
                            $.each(response.data, function(key, value) {
                                let barangMasuk = `
                                    <tr class="barang-row" id="index_${value.id}">
                                        <td>${counter++}</td>   
                                        <td>${value.kode_transaksi}</td>
                                        <td>${value.lot}</td>
                                        <td>${value.tanggal_masuk}</td>
                                        <td>${value.tanggal_kadaluarsa}</td>
                                        <td>${value.barang.nama_barang}</td>
                                        <td>${value.jumlah_masuk} ${value.barang.satuan.satuan}</td>
                                        <td>${value.outstanding} ${value.barang.satuan.satuan}</td>
                                        <td>${value.harga}</td>
                                        <td>${value.lokasi}</td>
                                        <td>${value.supplier.supplier}</td>
                                        <td>
                                            ${value.approved == 0 ? '<span class="badge bg-warning text-white">pending</span>' : '<span class="badge bg-success text-white">approved</span>'}
                                        </td>
                                        <td>
                                            <a href="javascript:void(0)" id="button_edit_outstanding" data-id="${value.id}" data-outstanding="${value.outstanding}" class="btn btn-icon btn-warning btn-lg mb-2"><i class="far fa-edit"></i> </a>
                                            <a href="javascript:void(0)" id="button_hapus_barangMasuk" data-id="${value.id}" class="btn btn-icon btn-danger btn-lg"><i class="fas fa-trash"></i> </a>
                                        </td>
                                    </tr>
                                `;
                                $('#table_id').DataTable().row.add($(barangMasuk)).draw(false);
                            });

                            $('#kode_transaksi').val('');
                            $('#lot').val('');
                            $('#barang_id').val('').prop('selectedIndex', 0).trigger('change');
                            $('#supplier_id').val('').prop('selectedIndex', 0).trigger('change');
                            $('#jumlah_masuk').val('');
                            $('#outstanding').val('');
                            $('#harga').val('');
                            $('#lokasi').val('');
                            $('#stok').val('');

                            $('#modal_tambah_barangMasuk').modal('hide');

                            $('#alert-kode_transaksi').removeClass('d-block').addClass('d-none');
                            $('#alert-lot').removeClass('d-block').addClass('d-none');
                            $('#alert-tanggal_masuk').removeClass('d-block').addClass('d-none');
                            $('#alert-tanggal_kadaluarsa').removeClass('d-block').addClass('d-none');
                            $('#alert-barang_id').removeClass('d-block').addClass('d-none');
                            $('#alert-jumlah_masuk').removeClass('d-block').addClass('d-none');
                            $('#alert-outstanding').removeClass('d-block').addClass('d-none');
                            $('#alert-harga').removeClass('d-block').addClass('d-none');
                            $('#alert-lokasi').removeClass('d-block').addClass('d-none');
                            $('#alert-supplier_id').removeClass('d-block').addClass('d-none');
                            $('#alert-lokasi').removeClass('d-block').addClass('d-none');
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

                    if (error.responseJSON && error.responseJSON.lot && error.responseJSON.lot[0]) {
                        $('#alert-lot').removeClass('d-none').addClass('d-block');
                        $('#alert-lot').html(error.responseJSON.lot[0]);
                    } else {
                        $('#alert-lot').removeClass('d-block').addClass('d-none');
                    }

                    if (error.responseJSON && error.responseJSON.tanggal_masuk && error.responseJSON
                        .tanggal_masuk[0]) {
                        $('#alert-tanggal_masuk').removeClass('d-none').addClass('d-block');
                        $('#alert-tanggal_masuk').html(error.responseJSON.tanggal_masuk[0]);
                    } else {
                        $('#alert-tanggal_masuk').removeClass('d-block').addClass('d-none');
                    }

                    if (error.responseJSON && error.responseJSON.tanggal_kadaluarsa && error.responseJSON
                        .tanggal_kadaluarsa[0]) {
                        $('#alert-tanggal_kadaluarsa').removeClass('d-none').addClass('d-block');
                        $('#alert-tanggal_kadaluarsa').html(error.responseJSON.tanggal_kadaluarsa[0]);
                    } else {
                        $('#alert-tanggal_kadaluarsa').removeClass('d-block').addClass('d-none');
                    }

                    if (error.responseJSON && error.responseJSON.barang_id && error.responseJSON
                        .barang_id[0]) {
                        $('#alert-barang_id').removeClass('d-none').addClass('d-block');
                        $('#alert-barang_id').html(error.responseJSON.barang_id[0]);
                    } else {
                        $('#alert-barang_id').removeClass('d-block').addClass('d-none');
                    }

                    if (error.responseJSON && error.responseJSON.jumlah_masuk && error.responseJSON
                        .jumlah_masuk[0]) {
                        $('#alert-jumlah_masuk').removeClass('d-none').addClass('d-block');
                        $('#alert-jumlah_masuk').html(error.responseJSON.jumlah_masuk[0]);
                    } else {
                        $('#alert-jumlah_masuk').removeClass('d-block').addClass('d-none');
                    }

                    if (error.responseJSON && error.responseJSON.outstanding && error.responseJSON
                        .outstanding[0]) {
                        $('#alert-outstanding').removeClass('d-none').addClass('d-block');
                        $('#alert-outstanding').html(error.responseJSON.outstanding[0]);
                    } else {
                        $('#alert-outstanding').removeClass('d-block').addClass('d-none');
                    }

                    if (error.responseJSON && error.responseJSON.harga && error.responseJSON.harga[0]) {
                        $('#alert-harga').removeClass('d-none').addClass('d-block');
                        $('#alert-harga').html(error.responseJSON.harga[0]);
                    } else {
                        $('#alert-harga').removeClass('d-block').addClass('d-none');
                    }

                    if (error.responseJSON && error.responseJSON.supplier_id && error.responseJSON
                        .supplier_id[0]) {
                        $('#alert-supplier_id').removeClass('d-none').addClass('d-block');
                        $('#alert-supplier_id').html(error.responseJSON.supplier_id[0]);
                    } else {
                        $('#alert-supplier_id').removeClass('d-block').addClass('d-none');
                    }

                    if (error.responseJSON && error.responseJSON.lokasi && error.responseJSON
                        .lokasi[0]) {
                        $('#alert-lokasi').removeClass('d-none').addClass('d-block');
                        $('#alert-lokasi').html(error.responseJSON.lokasi[0]);
                    } else {
                        $('#alert-lokasi').removeClass('d-block').addClass('d-none');
                    }
                }
            });
        });
    </script>

    <!-- Show Modal Edit Outstanding -->
    <script>
        $('body').on('click', '#button_edit_outstanding', function() {
            let barang_masuk_id = $(this).data('id');
            let outstanding = $(this).data('outstanding');
            $('#barang_masuk_id').val(barang_masuk_id);
            $('#edit_outstanding').val(outstanding);
            $('#modal_edit_outstanding').modal('show');
        });

        $('#update').click(function(e) {
            e.preventDefault();

            let barang_masuk_id = $('#barang_masuk_id').val();
            let intransit = $('#intransit').val();
            let received = $('#received').val();
            let token = $("meta[name='csrf-token']").attr("content");

            let formData = new FormData();
            formData.append('barang_masuk_id', barang_masuk_id);
            formData.append('intransit', intransit);
            formData.append('received', received);
            formData.append('_token', token);

            $.ajax({
                url: `/barang-masuk/${barang_masuk_id}/outstanding`,
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
                        url: "/barang-masuk/get-data",
                        type: "GET",
                        dataType: 'JSON',
                        success: function(response) {
                            let counter = 1;
                            $('#table_id').DataTable().clear();
                            $.each(response.data, function(key, value) {
                                let barangMasuk = `
                                    <tr class="barang-row" id="index_${value.id}">
                                        <td>${counter++}</td>   
                                        <td>${value.kode_transaksi}</td>
                                        <td>${value.lot}</td>
                                        <td>${value.tanggal_masuk}</td>
                                        <td>${value.tanggal_kadaluarsa}</td>
                                        <td>${value.barang.nama_barang}</td>
                                        <td>${value.jumlah_masuk} ${value.barang.satuan.satuan}</td>
                                        <td>${value.outstanding} ${value.barang.satuan.satuan}</td>
                                        <td>${value.harga}</td>
                                        <td>${value.lokasi}</td>
                                        <td>${value.supplier.supplier}</td>
                                        <td>
                                            ${value.approved == 0 ? '<span class="badge bg-warning text-white">pending</span>' : '<span class="badge bg-success text-white">approved</span>'}
                                        </td>
                                        <td>
                                            <a href="javascript:void(0)" id="button_edit_outstanding" data-id="${value.id}" data-outstanding="${value.outstanding}" class="btn btn-icon btn-warning btn-lg mb-2"><i class="far fa-edit"></i> </a>
                                            <a href="javascript:void(0)" id="button_hapus_barangMasuk" data-id="${value.id}" class="btn btn-icon btn-danger btn-lg"><i class="fas fa-trash"></i> </a>
                                        </td>
                                    </tr>
                                `;
                                $('#table_id').DataTable().row.add($(barangMasuk)).draw(false);
                            });

                            $('#modal_edit_outstanding').modal('hide');

                            $('#alert-intransit').removeClass('d-block').addClass('d-none');
                            $('#alert-received').removeClass('d-block').addClass('d-none');
                        }
                    });
                },
                error: function(error) {
                    if (error.responseJSON && error.responseJSON.intransit && error.responseJSON.intransit[0]) {
                        $('#alert-intransit').removeClass('d-none').addClass('d-block');
                        $('#alert-intransit').html(error.responseJSON.intransit[0]);
                    } else {
                        $('#alert-intransit').removeClass('d-block').addClass('d-none');
                    }

                    if (error.responseJSON && error.responseJSON.received && error.responseJSON.received[0]) {
                        $('#alert-received').removeClass('d-none').addClass('d-block');
                        $('#alert-received').html(error.responseJSON.received[0]);
                    } else {
                        $('#alert-received').removeClass('d-block').addClass('d-none');
                    }

                    if (error.responseJSON && error.responseJSON.message) {
                        Swal.fire({
                            icon: 'error',
                            title: `${error.responseJSON.message}`,
                            showConfirmButton: true,
                            timer: 3000
                        });
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
                                        let barangMasuk = `
                                            <tr class="barang-row" id="index_${value.id}">
                                                <td>${counter++}</td>   
                                                <td>${value.kode_transaksi}</td>
                                                <td>${value.lot}</td>
                                                <td>${value.tanggal_masuk}</td>
                                                <td>${value.tanggal_kadaluarsa}</td>
                                                <td>${value.barang.nama_barang}</td>
                                                <td>${value.jumlah_masuk} ${value.barang.satuan.satuan}</td>
                                                <td>${value.outstanding} ${value.barang.satuan.satuan}</td>
                                                <td>${value.harga}</td>
                                                <td>${value.lokasi}</td>
                                                <td>${value.supplier.supplier}</td>
                                                <td>
                                                    ${value.approved == 0 ? '<span class="badge bg-warning text-white">pending</span>' : '<span class="badge bg-success text-white">approved</span>'}
                                                </td>
                                                <td>
                                                    <a href="javascript:void(0)" id="button_edit_outstanding" data-id="${value.id}" data-outstanding="${value.outstanding}" class="btn btn-icon btn-warning btn-lg mb-2"><i class="far fa-edit"></i> </a>
                                                    <a href="javascript:void(0)" id="button_hapus_barangMasuk" data-id="${value.id}" class="btn btn-icon btn-danger btn-lg"><i class="fas fa-trash"></i> </a>
                                                </td>
                                            </tr>
                                        `;
                                        $('#table_id').DataTable().row.add($(barangMasuk)).draw(false);
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
        document.getElementById('tanggal_masuk').value = formattedDate;
    </script>
@endsection
