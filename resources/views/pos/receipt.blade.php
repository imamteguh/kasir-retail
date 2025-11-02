<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Struk Penjualan</title>
    <style>
        /* Thermal receipt styling (80mm default) */
        :root { --paper-width: 80mm; }
        * { box-sizing: border-box; }
        body { font-family: monospace; font-size: 12px; margin: 0; color: #000; }
        .receipt { width: var(--paper-width); margin: 0 auto; padding: 8px; }
        .center { text-align: center; }
        .hr { border-top: 1px dashed #000; margin: 6px 0; }
        table { width: 100%; border-collapse: collapse; }
        td { padding: 2px 0; }
        .right { text-align: right; }
        .mt-4 { margin-top: 8px; }
        .small { font-size: 11px; }
        @media print {
            @page { size: var(--paper-width) auto; margin: 0; }
            body { margin: 0; }
        }
    </style>
</head>
<body>
    <?php $paid = (int) request('paid', 0); $change = max(0, $paid - $sale->total); ?>
    <div class="receipt">
        <div class="center">
            <h3 style="margin:0">{{ app('store')->name }}</h3>
            <p style="margin:2px 0">{{ app('store')->address }}</p>
            <div class="hr"></div>
            <p style="margin:2px 0"><b>Invoice:</b> {{ $sale->invoice_number }}<br>
            <b>Tanggal:</b> {{ $sale->date->format('d/m/Y H:i') }}</p>
            <div class="hr"></div>
        </div>

        <table>
            @foreach($sale->items as $item)
                <tr>
                    <td>{{ $item->product->name }}</td>
                    <td class="right">{{ $item->qty }} x {{ number_format($item->price, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </table>
        <div class="hr"></div>
        <table>
            <tr>
                <td><b>Total</b></td>
                <td class="right">Rp {{ number_format($sale->total, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td><b>Dibayar</b></td>
                <td class="right">Rp {{ number_format($paid, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td><b>Kembalian</b></td>
                <td class="right">Rp {{ number_format($change, 0, ',', '.') }}</td>
            </tr>
        </table>
        <p class="small">Metode: {{ strtoupper($sale->payment_method) }}</p>
        <p class="small">Kasir: {{ $sale->cashier->name }}</p>
        <div class="hr"></div>
        <p class="center" style="margin:4px 0">Terima kasih telah berbelanja!</p>
    </div>

    <script>
        // Auto print when the receipt page loads
        window.addEventListener('load', function(){
            window.print();
        });
    </script>
</body>
</html>
