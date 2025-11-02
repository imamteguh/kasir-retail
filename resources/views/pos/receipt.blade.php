<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Struk Penjualan</title>
    <style>
        body { font-family: monospace; font-size: 13px; }
        .center { text-align: center; }
    </style>
</head>
<body>
    <div class="center">
        <h3>{{ app('store')->name }}</h3>
        <p>{{ app('store')->address }}</p>
        <hr>
        <p><b>Invoice:</b> {{ $sale->invoice_number }} <br>
        <b>Tanggal:</b> {{ $sale->date->format('d/m/Y H:i') }}</p>
        <hr>
    </div>

    <table width="100%">
        @foreach($sale->items as $item)
            <tr>
                <td>{{ $item->product->name }}</td>
                <td align="right">{{ $item->qty }} x {{ number_format($item->price, 0, ',', '.') }}</td>
            </tr>
        @endforeach
    </table>
    <hr>
    <p><b>Total:</b> Rp {{ number_format($sale->total, 0, ',', '.') }}</p>
    <p><b>Metode:</b> {{ strtoupper($sale->payment_method) }}</p>
    <p><b>Kasir:</b> {{ $sale->cashier->name }}</p>
    <hr>
    <p class="center">Terima kasih telah berbelanja!</p>
</body>
</html>
