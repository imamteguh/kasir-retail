<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Laba Rugi - {{ $label ?? '' }}</title>
    <style>
        body { font-family: Arial, Helvetica, sans-serif; font-size: 12px; }
        h1 { font-size: 18px; margin: 0 0 8px 0; }
        h2 { font-size: 14px; margin: 0 0 6px 0; }
        .meta { margin-bottom: 12px; }
        .meta div { margin: 2px 0; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #333; padding: 6px; }
        th { background: #f0f0f0; }
        .text-right { text-align: right; }
        .summary { margin: 10px 0; }
        .summary div { margin: 2px 0; }
    </style>
</head>
<body>
    <h1>Laporan Laba Rugi</h1>
    <div class="meta">
        <div><strong>Toko:</strong> {{ $store->name ?? '-' }}</div>
        <div><strong>Periode:</strong> {{ $start ?? '-' }} s/d {{ $end ?? '-' }}</div>
        <div><strong>Dibuat:</strong> {{ now()->format('Y-m-d H:i') }}</div>
    </div>

    <div class="summary">
        <div><strong>Total Penjualan:</strong> Rp {{ number_format($report['total_sales'] ?? 0, 0, ',', '.') }}</div>
        <div><strong>Total Pembelian:</strong> Rp {{ number_format($report['total_purchase'] ?? 0, 0, ',', '.') }}</div>
        <div><strong>Total Biaya (Cost):</strong> Rp {{ number_format($report['total_cost'] ?? 0, 0, ',', '.') }}</div>
        <div><strong>Laba:</strong> Rp {{ number_format($report['profit'] ?? 0, 0, ',', '.') }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Komponen</th>
                <th class="text-right">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Total Penjualan</td>
                <td class="text-right">Rp {{ number_format($report['total_sales'] ?? 0, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Total Pembelian</td>
                <td class="text-right">Rp {{ number_format($report['total_purchase'] ?? 0, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Total Biaya (Cost)</td>
                <td class="text-right">Rp {{ number_format($report['total_cost'] ?? 0, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td><strong>Laba</strong></td>
                <td class="text-right"><strong>Rp {{ number_format($report['profit'] ?? 0, 0, ',', '.') }}</strong></td>
            </tr>
        </tbody>
    </table>

    @if(!empty($autoPrint))
    <script>
        window.addEventListener('load', function(){
            window.print();
        });
    </script>
    @endif
</body>
</html>