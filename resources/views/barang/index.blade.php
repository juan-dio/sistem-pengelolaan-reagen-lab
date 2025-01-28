@extends('layouts.app')

@include('barang.create')
@include('barang.edit')
@include('barang.show')
@include('barang.excel')

@section('content')
    <div class="section-header">
        <h1>Data Reagen</h1>
        <div class="ml-auto">
            <a href="javascript:void(0)" class="btn btn-primary" id="button_tambah_barang"><i class="fa fa-plus"></i> Tambah
                Reagen</a>
            <a href="javascript:void(0)" class="btn btn-primary" id="button_tambah_barang_excel"><i class="fa fa-table"></i> Import
                Reagen Excel</a>
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
                                    <th>Barcode</th>
                                    <th>Kode</th>
                                    <th>Nama</th>
                                    <th>Test Group</th>
                                    <th>Stok Minimum</th>
                                    <th colspan="2">Opsi</th>
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

    <!-- Datatables Jquery -->
    <script>
        $(document).ready(function() {
            $('#table_id').DataTable({
                paging: true
            });

            $.ajax({
                url: "/barang/get-data",
                type: "GET",
                dataType: 'JSON',
                success: function(response) {
                    let counter = 1;
                    $('#table_id').DataTable().clear();
                    $.each(response.data, function(key, value) {
                        let barang = `
                            <tr class="barang-row" id="index_${value.id}">
                                <td>${counter++}</td>
                                <td><img src="/storage/${value.gambar}" alt="gambar Barang" style="width: 150px";></td>
                                <td>${value.kode_barang}</td>
                                <td>${value.nama_barang}</td>
                                <td>${value.test_group}</td>
                                <td>${value.stok_minimum}</td>
                                <td style="padding: 8px 6px;">
                                    <a href="javascript:void(0)" id="button_detail_barang" data-id="${value.id}" class="btn btn-icon btn-success btn-lg mb-2"><i class="far fa-eye"></i> </a>
                                    <a href="javascript:void(0)" id="button_edit_barang" data-id="${value.id}" class="btn btn-icon btn-warning btn-lg mb-2"><i class="far fa-edit"></i> </a>
                                    <a href="javascript:void(0)" id="button_hapus_barang" data-id="${value.id}" class="btn btn-icon btn-danger btn-lg mb-2"><i class="fa fa-trash" style="padding: 0 1px;"></i> </a>
                                </td>
                                <td style="padding: 8px 6px;">        
                                    <a href="javascript:void(0)" class="btn-barcode btn btn-icon btn-info btn-lg mb-2">Cetak</a>
                                </td>
                            </tr>
                        `;
                        $('#table_id').DataTable().row.add($(barang)).draw(false);
                    });
                }
            });
        });
    </script>

    <script>
        $('#button_template_excel').click(function(e) {
            e.preventDefault();
            $.ajax({
                url: '/barang/excel', // Sesuaikan dengan route Laravel Anda
                method: 'GET',
                xhrFields: {
                    responseType: 'blob' // Penting untuk mendownload file binary
                },
                success: function (data, status, xhr) {
                    // Mendapatkan nama file dari header 'Content-Disposition' atau fallback ke 'template.xlsx'
                    let filename = xhr.getResponseHeader('Content-Disposition')?.split('filename=')[1]?.replace(/"/g, '') || 'template.xlsx';

                    // Membuat URL dari blob
                    const url = window.URL.createObjectURL(data);
                    const link = document.createElement('a');
                    link.href = url;
                    link.setAttribute('download', filename); // Nama file yang diunduh
                    document.body.appendChild(link);
                    link.click();
                    link.remove();
                },
                error: function (xhr, status, error) {
                    console.error('Error downloading template:', error);
                    alert('Terjadi kesalahan saat mendownload file. Silakan coba lagi.');
                }
            });
        });
    </script>


    <!-- Show Modal Import Excel -->
    <script>
        $('body').on('click', '#button_tambah_barang_excel', function() {
            $('#modal_tambah_barang_excel').modal('show');
        });

        $('#import').click(function(e) {
            e.preventDefault();

            let excel = $('#excel')[0].files[0];
            let token = $("meta[name='csrf-token']").attr("content");

            let formData = new FormData();
            formData.append('excel', excel);
            formData.append('_token', token);

            $.ajax({
                url: '/barang/excel',
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
                    
                    let data = response.data;

                    for(let i = 1; i < data.length; i++) {
                        let gambar = null;
                        if (data[i].kode_barang) {
                            let canvas = document.createElement('canvas');
        
                            JsBarcode(canvas, data[i].kode_barang, {
                                format: "CODE128",
                                displayValue: true,
                                fontSize: 20,
                                width: 2,
                                height: 60
                            });
                            
                            let barcodeDataUrl = canvas.toDataURL('image/png');
            
                            // Konversi base64 menjadi Blob
                            const base64Data = barcodeDataUrl.split(',')[1];
                            const binaryData = atob(base64Data);
                            const arrayBuffer = new Uint8Array(binaryData.length);
            
                            for (let i = 0; i < binaryData.length; i++) {
                                arrayBuffer[i] = binaryData.charCodeAt(i);
                            }
            
                            gambar = new Blob([arrayBuffer], { type: 'image/png' });
                        }

                        let formData = new FormData();
                        formData.append('nama_barang', data[i].nama_barang);
                        formData.append('kode_barang', data[i].kode_barang);
                        if (gambar) {
                            formData.append('gambar', gambar, data[i].kode_barang + '.png');
                        }
                        formData.append('stok_minimum', data[i].stok_minimum);
                        formData.append('test_group', data[i].test_group);
                        formData.append('jenis_id', data[i].jenis_id);
                        formData.append('satuan_id', data[i].satuan_id);
                        formData.append('deskripsi', data[i].deskripsi);
                        formData.append('_token', token);

                        $.ajax({
                            url: '/barang',
                            type: "POST",
                            cache: false,
                            data: formData,
                            contentType: false,
                            processData: false,

                            success: function(response) {
                                $.ajax({
                                    url: '/barang/get-data',
                                    type: "GET",
                                    cache: false,
                                    success: function(response) {
                                        $('#table-barangs').html(''); // kosongkan tabel terlebih dahulu

                                        let counter = 1;
                                        $('#table_id').DataTable().clear();
                                        $.each(response.data, function(key, value) {
                                            let barang = `
                                                <tr class="barang-row" id="index_${value.id}">
                                                    <td>${counter++}</td>
                                                    <td><img src="/storage/${value.gambar}" alt="gambar Barang" style="width: 150px";"></td>
                                                    <td>${value.kode_barang}</td>
                                                    <td>${value.nama_barang}</td>
                                                    <td>${value.test_group}</td>
                                                    <td>${value.stok_minimum}</td>
                                                    <td style="padding: 8px 6px;">
                                                        <a href="javascript:void(0)" id="button_detail_barang" data-id="${value.id}" class="btn btn-icon btn-success btn-lg mb-2"><i class="far fa-eye"></i> </a>
                                                        <a href="javascript:void(0)" id="button_edit_barang" data-id="${value.id}" class="btn btn-icon btn-warning btn-lg mb-2"><i class="far fa-edit"></i> </a>
                                                        <a href="javascript:void(0)" id="button_hapus_barang" data-id="${value.id}" class="btn btn-icon btn-danger btn-lg mb-2"><i class="fas fa-trash" style="padding: 0 1px;"></i> </a>
                                                    </td>
                                                    <td style="padding: 8px 6px;">        
                                                        <a href="javascript:void(0)" class="btn-barcode btn btn-icon btn-info btn-lg mb-2">Cetak</a>
                                                    </td>
                                                </tr>
                                            `;
                                            $('#table_id').DataTable().row.add($(barang)).draw(false);
                                        });

                                        // $('#nama_barang').val('');
                                        // $('#kode_barang').val('');
                                        // $('#stok_minimum').val('');
                                        // $('#deskripsi').val('');

                                        // $('#modal_tambah_barang').modal('hide');

                                        // $('#alert-nama_barang').removeClass('d-block').addClass('d-none');
                                        // $('#alert-kode_barang').removeClass('d-block').addClass('d-none');
                                        // $('#alert-stok_minimum').removeClass('d-block').addClass('d-none');
                                        // $('#alert-jenis_id').removeClass('d-block').addClass('d-none');
                                        // $('#alert-satuan_id').removeClass('d-block').addClass('d-none');
                                        // $('#alert-deskripsi').removeClass('d-block').addClass('d-none');

                                        let table = $('#table_id').DataTable();
                                        table.draw();
                                    },
                                    error: function(error) {
                                        // console.log(error);
                                    }
                                });
                            },
                            error: function(error) {
                                // console.log(error);
                            }
                        });
                    }

                    $('#modal_tambah_barang_excel').modal('hide');
                    $('#excel').val('');
                    $('#alert-excel').removeClass('d-block').addClass('d-none');

                    let table = $('#table_id').DataTable();
                    table.draw();
                },

                error: function(error) {
                    if (error.responseJSON && error.responseJSON.excel && error.responseJSON.excel[0]) {
                        $('#excel').val('');
                        $('#alert-excel').removeClass('d-none').addClass('d-block');
                        $('#alert-excel').html(error.responseJSON.excel[0]);
                    } else {
                        $('#alert-excel').removeClass('d-block').addClass('d-none');

                    }
                }
            });
        });
    </script>


    <!-- Show Modal Tambah barang -->
    <script>
        $('body').on('click', '#button_tambah_barang', function() {
            $('#modal_tambah_barang').modal('show');
        });

        $('#store').click(function(e) {
            e.preventDefault();

            let nama_barang = $('#nama_barang').val();
            let kode_barang = $('#kode_barang').val();
            let gambar = null;
            let stok_minimum = $('#stok_minimum').val();
            let test_group = $('#test_group').val();
            let jenis_id = $('#jenis_id').val();
            let satuan_id = $('#satuan_id').val();
            let deskripsi = $('#deskripsi').val();
            let token = $("meta[name='csrf-token']").attr("content");

            if (kode_barang) {
                let canvas = document.createElement('canvas');
    
                JsBarcode(canvas, kode_barang, {
                    format: "CODE128",
                    displayValue: true,
                    fontSize: 20,
                    width: 2,
                    height: 60
                });
                
                let barcodeDataUrl = canvas.toDataURL('image/png');
    
                // Konversi base64 menjadi Blob
                const base64Data = barcodeDataUrl.split(',')[1];
                const binaryData = atob(base64Data);
                const arrayBuffer = new Uint8Array(binaryData.length);
    
                for (let i = 0; i < binaryData.length; i++) {
                    arrayBuffer[i] = binaryData.charCodeAt(i);
                }
    
                gambar = new Blob([arrayBuffer], { type: 'image/png' });
            }

            let formData = new FormData();
            formData.append('nama_barang', nama_barang);
            formData.append('kode_barang', kode_barang);
            if (gambar) {
                formData.append('gambar', gambar, kode_barang + '.png');
            }
            formData.append('stok_minimum', stok_minimum);
            formData.append('test_group', test_group);
            formData.append('jenis_id', jenis_id);
            formData.append('satuan_id', satuan_id);
            formData.append('deskripsi', deskripsi);
            formData.append('_token', token);

            $.ajax({
                url: '/barang',
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
                        url: '/barang/get-data',
                        type: "GET",
                        cache: false,
                        success: function(response) {
                            $('#table-barangs').html(''); // kosongkan tabel terlebih dahulu

                            let counter = 1;
                            $('#table_id').DataTable().clear();
                            $.each(response.data, function(key, value) {
                                let barang = `
                                    <tr class="barang-row" id="index_${value.id}">
                                        <td>${counter++}</td>
                                        <td><img src="/storage/${value.gambar}" alt="gambar Barang" style="width: 150px";"></td>
                                        <td>${value.kode_barang}</td>
                                        <td>${value.nama_barang}</td>
                                        <td>${value.test_group}</td>
                                        <td>${value.stok_minimum}</td>
                                        <td style="padding: 8px 6px;">
                                            <a href="javascript:void(0)" id="button_detail_barang" data-id="${value.id}" class="btn btn-icon btn-success btn-lg mb-2"><i class="far fa-eye"></i> </a>
                                            <a href="javascript:void(0)" id="button_edit_barang" data-id="${value.id}" class="btn btn-icon btn-warning btn-lg mb-2"><i class="far fa-edit"></i> </a>
                                            <a href="javascript:void(0)" id="button_hapus_barang" data-id="${value.id}" class="btn btn-icon btn-danger btn-lg mb-2"><i class="fas fa-trash" style="padding: 0 1px;"></i> </a>
                                        </td>
                                        <td style="padding: 8px 6px;">        
                                            <a href="javascript:void(0)" class="btn-barcode btn btn-icon btn-info btn-lg mb-2">Cetak</a>
                                        </td>
                                    </tr>
                                `;
                                $('#table_id').DataTable().row.add($(barang)).draw(false);
                            });

                            $('#nama_barang').val('');
                            $('#kode_barang').val('');
                            $('#stok_minimum').val('');
                            $('#deskripsi').val('');

                            $('#modal_tambah_barang').modal('hide');

                            $('#alert-nama_barang').removeClass('d-block').addClass('d-none');
                            $('#alert-kode_barang').removeClass('d-block').addClass('d-none');
                            $('#alert-stok_minimum').removeClass('d-block').addClass('d-none');
                            $('#alert-jenis_id').removeClass('d-block').addClass('d-none');
                            $('#alert-satuan_id').removeClass('d-block').addClass('d-none');
                            $('#alert-deskripsi').removeClass('d-block').addClass('d-none');
                        },
                        error: function(error) {
                            console.log(error);
                        }
                    });

                },

                error: function(error) {

                    if (error.responseJSON && error.responseJSON.nama_barang && error.responseJSON
                        .nama_barang[0]) {
                        $('#alert-nama_barang').removeClass('d-none').addClass('d-block');
                        $('#alert-nama_barang').html(error.responseJSON.nama_barang[0]);
                    } else {
                        $('#alert-nama_barang').removeClass('d-block').addClass('d-none');
                    }
                    
                    if (error.responseJSON && error.responseJSON.kode_barang && error.responseJSON
                        .kode_barang[0]) {
                        $('#alert-kode_barang').removeClass('d-none').addClass('d-block');
                        $('#alert-kode_barang').html(error.responseJSON.kode_barang[0]);
                    } else {
                        $('#alert-kode_barang').removeClass('d-block').addClass('d-none');
                    }

                    if (error.responseJSON && error.responseJSON.stok_minimum && error.responseJSON
                        .stok_minimum[0]) {
                        $('#alert-stok_minimum').removeClass('d-none').addClass('d-block');
                        $('#alert-stok_minimum').html(error.responseJSON.stok_minimum[0]);
                    } else {
                        $('#alert-stok_minimum').removeClass('d-block').addClass('d-none');
                    }

                    if (error.responseJSON && error.responseJSON.test_group && error.responseJSON
                        .test_group[0]) {
                        $('#alert-test_group').removeClass('d-none').addClass('d-block');
                        $('#alert-test_group').html(error.responseJSON.test_group[0]);
                    } else {
                        $('#alert-test_group').removeClass('d-block').addClass('d-none');
                    }

                    if (error.responseJSON && error.responseJSON.jenis_id && error.responseJSON
                        .jenis_id[0]) {
                        $('#alert-jenis_id').removeClass('d-none').addClass('d-block');
                        $('#alert-jenis_id').html(error.responseJSON.jenis_id[0]);
                    } else {
                        $('#alert-jenis_id').removeClass('d-block').addClass('d-none');
                    }

                    if (error.responseJSON && error.responseJSON.satuan_id && error.responseJSON
                        .satuan_id[0]) {
                        $('#alert-satuan_id').removeClass('d-none').addClass('d-block');
                        $('#alert-satuan_id').html(error.responseJSON.satuan_id[0]);
                    } else {
                        $('#alert-satuan_id').removeClass('d-block').addClass('d-none');
                    }

                    if (error.responseJSON && error.responseJSON.deskripsi && error.responseJSON
                        .deskripsi[0]) {
                        $('#alert-deskripsi').removeClass('d-none').addClass('d-block');
                        $('#alert-deskripsi').html(error.responseJSON.deskripsi[0]);
                    } else {
                        $('#alert-deskripsi').removeClass('d-block').addClass('d-none');
                    }
                }
            });
        });
    </script>

    <!-- Show Detail Data Barang -->
    <script>
        $('body').on('click', '#button_detail_barang', function() {
            let barang_id = $(this).data('id');

            $.ajax({
                url: `/barang/${barang_id}/`,
                type: "GET",
                cache: false,
                success: function(response) {
                    $('#barang_id').val(response.data.id);
                    $('#detail_gambar').val(null);
                    $('#detail_nama_barang').val(response.data.nama_barang);
                    $('#detail_kode_barang').val(response.data.kode_barang);
                    $('#detail_lot').val(response.data.lot);
                    $('#detail_test_group').val(response.data.test_group);
                    $('#detail_jenis_id').val(response.data.jenis_id);
                    $('#detail_satuan_id').val(response.data.satuan_id);
                    $('#detail_stok').val(response.data.stok !== null && response.data.stok !== '' ? response.data.stok : 'Stok Kosong');
                    $('#detail_stok_minimum').val(response.data.stok_minimum);
                    $('#detail_deskripsi').val(response.data.deskripsi);
                    $('#detail_gambar_preview').attr('src', '/storage/' + response.data.gambar);
                    $('#modal_detail_barang').modal('show');
                }
            });
        });
    </script>

    <!-- Edit Data Barang -->
    <script>
        // Menampilkan Form Modal Edit
        $('body').on('click', '#button_edit_barang', function() {
            let barang_id = $(this).data('id');

            $.ajax({
                url: `/barang/${barang_id}/edit`,
                type: "GET",
                cache: false,
                success: function(response) {
                    $('#barang_id').val(response.data.id);
                    $('#edit_nama_barang').val(response.data.nama_barang);
                    $('#edit_kode_barang').val(response.data.kode_barang);
                    $('#edit_stok_minimum').val(response.data.stok_minimum);
                    $('#edit_test_group').val(response.data.test_group);
                    $('#edit_jenis_id').val(response.data.jenis_id);
                    $('#edit_satuan_id').val(response.data.satuan_id);
                    $('#edit_deskripsi').val(response.data.deskripsi);

                    $('#modal_edit_barang').modal('show');
                }
            });
        });

        // Proses Update Data
        $('#update').click(function(e) {
            e.preventDefault();

            let barang_id = $('#barang_id').val();
            let nama_barang = $('#edit_nama_barang').val();
            let kode_barang = $('#edit_kode_barang').val();
            let gambar = null;
            let stok_minimum = $('#edit_stok_minimum').val();
            let test_group = $('#edit_test_group').val();
            let deskripsi = $('#edit_deskripsi').val();
            let jenis_id = $('#edit_jenis_id').val();
            let satuan_id = $('#edit_satuan_id').val();
            let token = $("meta[name='csrf-token']").attr("content");

            if (kode_barang) {
                let canvas = document.createElement('canvas');
    
                JsBarcode(canvas, kode_barang, {
                    format: "CODE128",
                    displayValue: true,
                    fontSize: 20,
                    width: 2,
                    height: 60
                });
                
                let barcodeDataUrl = canvas.toDataURL('image/png');
    
                // Konversi base64 menjadi Blob
                const base64Data = barcodeDataUrl.split(',')[1];
                const binaryData = atob(base64Data);
                const arrayBuffer = new Uint8Array(binaryData.length);
    
                for (let i = 0; i < binaryData.length; i++) {
                    arrayBuffer[i] = binaryData.charCodeAt(i);
                }
    
                gambar = new Blob([arrayBuffer], { type: 'image/png' });
            }

            // Buat objek FormData
            let formData = new FormData();
            formData.append('nama_barang', nama_barang);
            formData.append('kode_barang', kode_barang);
            if (gambar) {
                formData.append('gambar', gambar, kode_barang + '.png');
            }
            formData.append('stok_minimum', stok_minimum);
            formData.append('test_group', test_group);
            formData.append('deskripsi', deskripsi);
            formData.append('jenis_id', jenis_id);
            formData.append('satuan_id', satuan_id);
            formData.append('_token', token);
            formData.append('_method', 'PUT');

            $.ajax({
                url: `/barang/${barang_id}`,
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

                    let row = $(`#index_${response.data.id}`);
                    let rowData = row.find('td');

                    // Memperbarui data pada kolom nomor urutan (indeks 0)
                    rowData.eq(0).text(row.index() + 1);

                    // Memperbarui data pada kolom gambar (indeks 1)
                    let imageColumn = rowData.eq(1).find('img');
                    imageColumn.attr('src', `/storage/${response.data.gambar}`);

                    // Memperbarui data pada kolom kode barang (indeks 2)
                    rowData.eq(2).text(response.data.kode_barang);

                    // Memperbarui data pada kolom nama barang (indeks 3)
                    rowData.eq(3).text(response.data.nama_barang);

                    // Memperbarui data pada kolom test group (indeks 4)
                    rowData.eq(4).text(response.data.test_group);

                    // Memperbarui data pada kolom stok minimum (indeks 4)
                    rowData.eq(5).text(response.data.stok_minimum);

                    $('#modal_edit_barang').modal('hide');

                    $('#alert-edit_nama_barang').removeClass('d-block').addClass('d-none');
                    $('#alert-edit_kode_barang').removeClass('d-block').addClass('d-none');
                    $('#alert-edit_stok_minimum').removeClass('d-block').addClass('d-none');
                    $('#alert-edit-test_group').removeClass('d-block').addClass('d-none');
                    $('#alert-edit_jenis_id').removeClass('d-block').addClass('d-none');
                    $('#alert-edit_satuan_id').removeClass('d-block').addClass('d-none');
                    $('#alert-edit_deskripsi').removeClass('d-block').addClass('d-none');
                },

                error: function(error) {

                    if (error.responseJSON && error.responseJSON.nama_barang && error.responseJSON
                        .nama_barang[0]) {
                        $('#alert-edit_nama_barang').removeClass('d-none').addClass('d-block');
                        $('#alert-edit_nama_barang').html(error.responseJSON.nama_barang[0]);
                    } else {
                        $('#alert-edit_nama_barang').removeClass('d-block')('#alert-edit_nama_barang').addClass('d-none');
                    }
                    
                    if (error.responseJSON && error.responseJSON.kode_barang && error.responseJSON
                        .kode_barang[0]) {
                        $('#alert-edit_kode_barang').removeClass('d-none')('#alert-edit_kode_barang').addClass('d-block');
                        $('#alert-edit_kode_barang').html(error.responseJSON.kode_barang[0]);
                    } else {
                        $('#alert-edit_kode_barang').removeClass('d-block').addClass('d-none');
                    }

                    if (error.responseJSON && error.responseJSON.stok_minimum && error.responseJSON
                        .stok_minimum[0]) {
                        $('#alert-edit_stok_minimum').removeClass('d-none').addClass('d-block');
                        $('#alert-edit_stok_minimum').html(error.responseJSON.stok_minimum[0]);
                    } else {
                        $('#alert-edit_stok_minimum').removeClass('d-block').addClass('d-none');
                    }

                    if (error.responseJSON && error.responseJSON.test_group && error.responseJSON
                        .test_group[0]) {
                        $('#alert-edit_test_group').removeClass('d-none').addClass('d-block');
                        $('#alert-edit_test_group').html(error.responseJSON.test_group[0]);
                    } else {
                        $('#alert-edit_test_group').removeClass('d-block').addClass('d-none');
                    }

                    if (error.responseJSON && error.responseJSON.jenis_id && error.responseJSON
                        .jenis_id[0]) {
                        $('#alert-edit_jenis_id').removeClass('d-none').addClass('d-block');
                        $('#alert-edit_jenis_id').html(error.responseJSON.jenis_id[0]);
                    } else {
                        $('#alert-edit_jenis_id').removeClass('d-block').addClass('d-none');
                    }

                    if (error.responseJSON && error.responseJSON.satuan_id && error.responseJSON
                        .satuan_id[0]) {
                        $('#alert-edit_satuan_id').removeClass('d-none').addClass('d-block');
                        $('#alert-edit_satuan_id').html(error.responseJSON.satuan_id[0]);
                    } else {
                        $('#alert-edit_satuan_id').removeClass('d-block').addClass('d-none');
                    }

                    if (error.responseJSON && error.responseJSON.deskripsi && error.responseJSON
                        .deskripsi[0]) {
                        $('#alert-edit_deskripsi').removeClass('d-none').addClass('d-block');
                        $('#alert-edit_deskripsi').html(error.responseJSON.deskripsi[0]);
                    } else {
                        $('#alert-edit_deskripsi').removeClass('d-block').addClass('d-none');
                    }
                }
            })
        })
    </script>

    <!-- Hapus Data Barang -->
    <script>
        $('body').on('click', '#button_hapus_barang', function() {
            let barang_id = $(this).data('id');
            let token = $("meta[name='csrf-token']").attr("content");

            Swal.fire({
                title: 'Apakah Kamu Yakin?',
                text: "ingin menghapus data ini!",
                icon: 'warning',
                showCancelButton: true,
                cancelButtonText: 'TIDAK',
                confirmButtonText: 'YA, HAPUS!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/barang/${barang_id}`,
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

                            // Ambil ulang data dan gambar tabel
                            $.ajax({
                                url: "/barang/get-data",
                                type: "GET",
                                dataType: 'JSON',
                                success: function(response) {
                                    let counter = 1;
                                    $.each(response.data, function(key, value) {
                                        let barang = `
                                        <tr class="barang-row" id="index_${value.id}">
                                            <td>${counter++}</td>
                                            <td><img src="/storage/${value.gambar}" alt="gambar Barang" style="width: 150px";"></td>
                                            <td>${value.kode_barang}</td>
                                            <td>${value.nama_barang}</td>
                                            <td>${value.test_group}</td>
                                            <td>${value.stok_minimum}</td>
                                            <td style="padding: 8px 6px;">
                                                <a href="javascript:void(0)" id="button_detail_barang" data-id="${value.id}" class="btn btn-icon btn-success btn-lg mb-2"><i class="far fa-eye"></i> </a>
                                                <a href="javascript:void(0)" id="button_edit_barang" data-id="${value.id}" class="btn btn-icon btn-warning btn-lg mb-2"><i class="far fa-edit"></i> </a>
                                                <a href="javascript:void(0)" id="button_hapus_barang" data-id="${value.id}" class="btn btn-icon btn-danger btn-lg mb-2"><i class="fas fa-trash" style="padding: 0 1px;"></i> </a>
                                            </td>
                                            <td style="padding: 8px 6px;">        
                                                <a href="javascript:void(0)" class="btn-barcode btn btn-icon btn-info btn-lg mb-2">Cetak</a>
                                            </td>
                                        </tr>
                                    `;
                                        $('#table_id').DataTable().row.add($(barang)).draw(false);
                                    });
                                }
                            });
                        }
                    })
                }
            })
        })
    </script>


    <!-- Preview Image -->
    <script>
        function previewImage() {
            preview.src = URL.createObjectURL(event.target.files[0]);
        }
    </script>

    <script>
        function previewImageEdit() {
            edit_gambar_preview.src = URL.createObjectURL(event.target.files[0]);
        }
    </script>
@endsection
