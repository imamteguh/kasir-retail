<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Penjualan - {{ ($type==='monthly') ? ('Bulanan ' . ($month ?? '')) : ('Harian ' . ($date ?? '')) }}</title>
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
    <h1>Rekap Penjualan</h1>
    <div class="meta">
        <div><strong>Toko:</strong> {{ $store->name ?? '-' }}</div>
        @if($type==='monthly')
            <div><strong>Periode:</strong> {{ $start }} s/d {{ $end }}</div>
        @else
            <div><strong>Tanggal:</strong> {{ $date }}</div>
        @endif
        <div><strong>Dibuat:</strong> {{ now()->format('Y-m-d H:i') }}</div>
    </div>

    <div class="summary">
        <div><strong>Total Penjualan:</strong> Rp {{ number_format($total_sales ?? 0, 0, ',', '.') }}</div>
        <div><strong>Jumlah Transaksi:</strong> {{ $transactions ?? 0 }}</div>
    </div>

    @if($type==='monthly')
        <table>
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th class="text-right">Jumlah Transaksi</th>
                    <th class="text-right">Total Penjualan</th>
                </tr>
            </thead>
            <tbody>
                @forelse(($grouped ?? []) as $dateKey => $recap)
                    <tr>
                        <td>{{ $dateKey }}</td>
                        <td class="text-right">{{ $recap['transactions'] }}</td>
                        <td class="text-right">Rp {{ number_format($recap['total'], 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3">Tidak ada data.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @else
        <table>
            <thead>
                <tr>
                    <th>Invoice</th>
                    <th>Tanggal</th>
                    <th class="text-right">Item</th>
                    <th>Metode</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse(($sales ?? []) as $sale)
                    <tr>
                        <td>{{ $sale->invoice_number }}</td>
                        <td>{{ $sale->date->format('Y-m-d H:i') }}</td>
                        <td class="text-right">{{ $sale->items->sum('qty') }}</td>
                        <td>{{ strtoupper($sale->payment_method) }}</td>
                        <td class="text-right">Rp {{ number_format($sale->total, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">Tidak ada data.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @endif
    @if(!empty($autoPrint))
    <script>
        window.addEventListener('load', function(){
            window.print();
        });
    </script>
    @endif
</body>
</html>