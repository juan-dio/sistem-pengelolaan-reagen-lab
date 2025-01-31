@extends('layouts.app')

@include('print-barcode.print-one')
@include('print-barcode.print-some')

@section('content')
    <div class="section-header">
        <h1>Print Barcode</h1>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col">
                    <a href="javascript:void(0)" class="btn btn-primary" id="button_print_one"><i class="fa-solid fa-cube"></i> Cetak 1 Item</a>
                    <a href="javascript:void(0)" class="btn btn-primary" id="button_print_some"><i class="fa-solid fa-cubes"></i> Cetak Beberapa Item</a>
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
            setTimeout(function() {
                $('.js-example-basic-single').select2();
            }, 500);
        });
    </script>

    {{-- Cetak 1 Item --}}
    <script>
        $('body').on('click', '#button_print_one', function() {
            $('#modal_print_one').modal('show');
        });

        $('#print-one').click(function(e) {
            e.preventDefault();

            let barang_id = $('#barang_id').val();
            let jumlah = $('#jumlah').val();

            let error = [];

            if (barang_id == 'Pilih Item') {
                $('#alert-one-barang_id').removeClass('d-none');
                $('#alert-one-barang_id').text('Pilih Item terlebih dahulu');
                error.push('barang_id');
            } else {
                $('#alert-one-barang_id').addClass('d-none');
                error = error.filter(item => item !== 'barang_id');
            }

            if (jumlah < 1) {
                $('#alert-one-jumlah').removeClass('d-none');
                $('#alert-one-jumlah').text('Jumlah tidak boleh kurang dari 1');
                error.push('jumlah');
            } else {
                $('#alert-one-jumlah').addClass('d-none');
                error = error.filter(item => item !== 'jumlah');
            }

            if (error.length == 0) {
                $('#form-print-one').submit();
                $('#modal_print_one').modal('hide');
            }

        })
    </script>

    {{-- Cetak Beberapa Item --}}
    <script>
        const barangs = jQuery.parseJSON('{!! json_encode($barangs) !!}');
        console.log(barangs);
        let print_barangs = [];

        $('body').on('click', '#button_print_some', function() {
            $('#modal_print_some').modal('show');
        });

        $('#add-item').click(function(e) {
            e.preventDefault();

            let some_barang_id = $('#some_barang_id').val();
            let some_jumlah = $('#some_jumlah').val();

            let error = [];

            if (barang_id == 'Pilih Item') {
                $('#alert-some_barang_id').removeClass('d-none');
                $('#alert-some_barang_id').text('Pilih Item terlebih dahulu');
                error.push('barang_id');
            } else {
                $('#alert-some_barang_id').addClass('d-none');
                error = error.filter(item => item !== 'barang_id');
            }

            if (jumlah < 1) {
                $('#alert-some_jumlah').removeClass('d-none');
                $('#alert-some_jumlah').text('Jumlah tidak boleh kurang dari 1');
                error.push('jumlah');
            } else {
                $('#alert-some_jumlah').addClass('d-none');
                error = error.filter(item => item !== 'jumlah');
            }

            if (error.length == 0) {
                print_barangs.push({
                    barang_id: some_barang_id,
                    jumlah: some_jumlah
                });

                $('#table-item').empty();

                if (print_barangs.length > 0) {
                    print_barangs.forEach((item, index) => {
                        let barang = barangs.find(barang => barang.id == item.barang_id);
                        $('#table-item').append(`
                            <tr>
                                <td>${index + 1}</td>
                                <td>${barang.nama_barang}</td>
                                <td>${item.jumlah}</td>
                                <td>
                                    <button type="button" class="btn btn-danger btn-sm" id="delete-item" data-index="${index}">Hapus</button>
                                </td>
                            </tr>
                        `);
                    });
                } else {
                    $('#table-item').append(`
                        <tr>
                            <td colspan="4" class="text-center">Belum ada item yang dipilih</td>
                        </tr>
                    `);
                }
            }
        });

        $('body').on('click', '#delete-item', function() {
            let index = $(this).data('index');
            print_barangs.splice(index, 1);

            $('#table-item').empty();

            if (print_barangs.length > 0) {
                print_barangs.forEach((item, index) => {
                    let barang = barangs.find(barang => barang.id == item.barang_id);
                    $('#table-item').append(`
                        <tr>
                            <td>${index + 1}</td>
                            <td>${barang.nama_barang}</td>
                            <td>${item.jumlah}</td>
                            <td>
                                <button type="button" class="btn btn-danger btn-sm" id="delete-item" data-index="${index}">Hapus</button>
                            </td>
                        </tr>
                    `);
                });
            } else {
                $('#table-item').append(`
                    <tr>
                        <td colspan="4" class="text-center">Belum ada item yang dipilih</td>
                    </tr>
                `);
            }
        });

        $('#print-some').click(function(e) {
            e.preventDefault();

            if (print_barangs.length > 0) {
                let form = document.createElement('form');
                form.action = '/print-barcode/print-some';
                form.method = 'POST';
                form.enctype = 'multipart/form-data';
                form.id = 'form-print-some';

                let csrf = document.createElement('input');
                csrf.type = 'hidden';
                csrf.name = '_token';
                csrf.value = $('meta[name="csrf-token"]').attr('content');

                form.appendChild(csrf);

                print_barangs.forEach((item, index) => {
                    // if (item.barang_id == 'Pilih Item') {
                    //     $('#alert-some_barang_id').removeClass('d-none');
                    //     $('#alert-some_barang_id').text('Pilih Item terlebih dahulu');
                    //     return;
                    // }
                    let barang_id = document.createElement('input');
                    barang_id.type = 'hidden';
                    barang_id.name = `some_barang_id[${index}]`;
                    barang_id.value = item.barang_id;

                    let jumlah = document.createElement('input');
                    jumlah.type = 'hidden';
                    jumlah.name = `some_jumlah[${index}]`;
                    jumlah.value = item.jumlah;

                    form.appendChild(barang_id);
                    form.appendChild(jumlah);
                });

                document.body.appendChild(form);
                form.submit();

                $('#modal_print_some').modal('hide');
            }
        });


    </script>

@endsection
