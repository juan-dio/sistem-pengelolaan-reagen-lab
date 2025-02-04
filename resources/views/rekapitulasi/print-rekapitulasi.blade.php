<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        h1, p{
            text-align: center;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            text-align: center;
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .footer {
            position: fixed;
            bottom: 20px;
            right: 20px;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <h1>Rekapitulasi</h1>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode Barang</th>
                <th>Nama Barang</th>
                <th>Jenis</th>
                <th>Stok</th>
                <th>Outstanding</th>
                <th>Masuk</th>
                <th>Keluar</th>
                {{-- <th>Satuan</th> --}}
            </tr>
        </thead>
        <tbody>
            @foreach ($barangRekap as $key => $barang)
            <tr>
                <td>{{ $key + 1 }}</td>
                <td>{{ $barang->kode_barang }}</td>
                <td>{{ $barang->nama_barang }}</td>
                <td>{{ $barang->jenis->jenis_barang ?? '-' }}</td>
                <td>{{ $barang->stok }} {{ $barang->satuan->satuan }}</td>
                <td>{{ $barangOutstanding[$barang->id]->total_outstanding ?? 0 }} {{ $barang->satuan->satuan }}</td>
                <td>{{ $barangMasuk[$barang->id]->total_masuk ?? 0 }} {{ $barang->satuan->satuan }}</td>
                <td>{{ $barangKeluar[$barang->id]->total_keluar ?? 0 }} {{ $barang->satuan->satuan }}</td>
                {{-- <td>{{ $barang->satuan->satuan ?? '-' }}</td> --}}
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Dicetak oleh: {{ auth()->user()->name }}<br>
        Tanggal: {{ date('d-m-Y') }}
    </div>
    
    <script>
        const previousUrl = document.referrer;

        window.print();

        setTimeout(() => {
            window.location.href = previousUrl;
        }, 100);
    </script>
</body>
</html>
