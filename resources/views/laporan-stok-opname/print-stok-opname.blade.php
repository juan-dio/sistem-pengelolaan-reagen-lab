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
    <h1>Laporan Stok Opname</h1>
    @if ($tanggalMulai && $tanggalSelesai)
        <p>Rentang Tanggal : {{ $tanggalMulai }} - {{ $tanggalSelesai }}<p>
    @else
        <p>Rentang Tanggal : Semua</p>
    @endif
    

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Barang</th>
                <th>Stok Sistem</th>
                <th>Stok Aktual</th>
                <th>Selisih</th>
                <th>Keterangan</th>
                <th>Tanggal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->barang->nama_barang}} </td>
                <td>{{ $item->barang->stok }} {{ $item->barang->satuan->satuan }}</td>
                <td>{{ $item->stok_aktual }} {{ $item->barang->satuan->satuan }}</td>
                <td>{{ $item->barang->stok - $item->stok_aktual }} {{ $item->barang->satuan->satuan }}</td>
                <td>{{ $item->keterangan }} </td>
                <td>{{ $item->created_at->format('Y-m-d') }}</td>
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
