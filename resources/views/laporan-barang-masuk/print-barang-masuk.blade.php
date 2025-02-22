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
    <h1>Laporan Barang Masuk</h1>
    @if ($tanggalMulai && $tanggalSelesai)
        <p>Rentang Tanggal : {{ $tanggalMulai }} - {{ $tanggalSelesai }}<p>
    @else
        <p>Rentang Tanggal : Semua</p>
    @endif
    

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode Transaksi</th>
                <th>Supplier</th>
                <th>Lot</th>
                <th>Tanggal Masuk</th>
                <th>Expired</th>
                <th>Nama Barang</th>
                <th>Jumlah Masuk</th>
                <th>Outstanding</th>
                <th>Harga</th>
                <th>Lokasi</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->kode_transaksi }}</td>
                <td>{{ $item->supplier->supplier}}</td>
                <td>{{ $item->lot }}</td>
                <td>{{ $item->tanggal_masuk }}</td>
                <td>{{ $item->tanggal_kadaluarsa }}</td>
                <td>{{ $item->barang->nama_barang}} </td>
                <td>{{ $item->jumlah_masuk}} {{ $item->barang->satuan->satuan }}</td>
                <td>{{ $item->outstanding }} {{ $item->barang->satuan->satuan }}</td>
                <td>{{ $item->harga }}</td>
                <td>{{ $item->lokasi }}</td>
                <td>{{ $item->keterangan }}</td>
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
