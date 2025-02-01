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
    <h1>Perkiraan Penggunaan Barang dalam 6 Bulan ke Depan</h1>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode Barang</th>
                <th>Nama Barang</th>
                @for ($i = 1; $i <= 6; $i++)
                    <th>{{ now()->addMonths($i)->format('F Y') }}</th>
                @endfor
            </tr>
        </thead>
        <tbody>
            @foreach ($forecastResults as $result)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $result['kode_barang'] }}</td>
                <td>{{ $result['barang'] }}</td>
                @foreach ($result['forecast'] as $forecast)
                    <td>{{ $forecast }} {{ $result['satuan'] }}</td>
                @endforeach
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
