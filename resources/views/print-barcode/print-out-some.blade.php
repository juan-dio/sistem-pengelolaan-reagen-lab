<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Barcode</title>
    <style>
        .print-area {
            display: none;
        }
        @media print {
            @page {
                margin: 0;
            }

            body {
                margin: 0;
                padding: 0;
                font-family: Arial, sans-serif;
            }

            .print-area {
                display: block;
                width: 100%;
                /* height: 100%; */
                text-align: center;
                page-break-after: always;
            }

            .barcode-container {
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                padding: 0 10px;
                /* border: 1px solid black; */
                width: 40mm;
                height: 30mm;
            }

            .barcode-title {
                width: 100%;
                box-sizing: border-box;
                padding: 0 5px;
                display: flex;
                justify-content: space-between;
            }

            .barcode-title h3 {
                font-size: 10px;
                margin: 5px 0;
            }

            img {
                max-width: 100%;
                height: auto;
                /* max-height: 100%; */
            }
        }
    </style>
</head>
<body>
    @foreach ($print_barangs as $print_barang)
      @for ($i = 0; $i < $print_barang['jumlah']; $i++)
        <div class="print-area">
            <div class="barcode-container">
                <div class="barcode-title">
                    <h3>{{ $print_barang['barang']->nama_barang }}</h3>
                    <h3>{{ $print_barang['barang']->test_group }}</h3>
                </div>
                <img src="/storage/gambar-barang/{{ $print_barang['barang']->kode_barang }}.png" alt="barcode">
            </div>
        </div>
      @endfor
    @endforeach

    <script>
        const previousUrl = document.referrer;

        window.print();

        setTimeout(() => {
            window.location.href = previousUrl;
        }, 100);
    </script>

</body>
</html>
