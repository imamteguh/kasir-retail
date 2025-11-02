<x-layouts.app title="Rekap Penjualan">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">Rekap Penjualan</h5>
                <a class="btn btn-outline-danger" href="{{ route('reports.sales.pdf', request()->query()) }}">
                    <i class="icon-base bx bx-printer me-2"></i> Print PDF
                </a>
            </div>
            <div class="card-body">
                <form class="row g-3" method="GET" action="{{ route('reports.sales') }}">
                    <div class="col-md-3">
                        <label class="form-label">Tipe Rekap</label>
                        <select class="form-select" name="type" id="typeSelect">
                            <option value="daily" {{ (request('type','daily')==='daily')?'selected':'' }}>Harian</option>
                            <option value="monthly" {{ (request('type')==='monthly')?'selected':'' }}>Bulanan</option>
                        </select>
                    </div>
                    <div class="col-md-3" id="dateField">
                        <label class="form-label">Tanggal</label>
                        <input type="date" name="date" class="form-control" value="{{ request('date', $date ?? now()->toDateString()) }}" />
                    </div>
                    <div class="col-md-3 d-none" id="monthField">
                        <label class="form-label">Bulan</label>
                        <input type="month" name="month" class="form-control" value="{{ request('month', $month ?? now()->format('Y-m')) }}" />
                    </div>
                    <div class="col-md-3 align-self-end">
                        <button class="btn btn-primary" type="submit"><i class="bx bx-filter me-1"></i> Tampilkan</button>
                    </div>
                </form>

                <hr/>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <div class="card shadow-none border border-primary text-primary">
                            <div class="card-body">
                                <div class="text-muted">Periode</div>
                                <div class="fw-semibold fs-5">
                                    @if(($type ?? 'daily')==='monthly')
                                        {{ $start }} s/d {{ $end }}
                                    @else
                                        {{ $date ?? now()->toDateString() }}
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card shadow-none border border-success text-success">
                            <div class="card-body">
                                <div class="text-muted">Total Penjualan</div>
                                <div class="fw-bold fs-5">Rp {{ number_format($total_sales ?? 0, 0, ',', '.') }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card shadow-none border border-info text-info">
                            <div class="card-body">
                                <div class="text-muted">Jumlah Transaksi</div>
                                <div class="fw-bold fs-5">{{ $transactions ?? 0 }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                @if(($type ?? 'daily')==='monthly')
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th class="text-end">Jumlah Transaksi</th>
                                    <th class="text-end">Total Penjualan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse(($grouped ?? []) as $dateKey => $recap)
                                    <tr>
                                        <td>{{ $dateKey }}</td>
                                        <td class="text-end">{{ $recap['transactions'] }}</td>
                                        <td class="text-end">Rp {{ number_format($recap['total'], 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3"><div class="alert alert-warning mb-0">Tidak ada data.</div></td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Invoice</th>
                                    <th>Tanggal</th>
                                    <th class="text-end">Item</th>
                                    <th>Metode</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse(($sales ?? []) as $sale)
                                    <tr>
                                        <td>{{ $sale->invoice_number }}</td>
                                        <td>{{ $sale->date->format('Y-m-d H:i') }}</td>
                                        <td class="text-end">{{ $sale->items->sum('qty') }}</td>
                                        <td>{{ strtoupper($sale->payment_method) }}</td>
                                        <td class="text-end">Rp {{ number_format($sale->total, 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5"><div class="alert alert-warning mb-0">Tidak ada data.</div></td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        (function(){
            function toggleFields(){
                const type = document.getElementById('typeSelect').value;
                const dateField = document.getElementById('dateField');
                const monthField = document.getElementById('monthField');
                if (type === 'monthly') {
                    dateField.classList.add('d-none');
                    monthField.classList.remove('d-none');
                } else {
                    monthField.classList.add('d-none');
                    dateField.classList.remove('d-none');
                }
            }
            document.getElementById('typeSelect').addEventListener('change', toggleFields);
            toggleFields();
        })();
    </script>
    @endpush
</x-layouts.app>