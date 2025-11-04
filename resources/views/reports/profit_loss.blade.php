<x-layouts.app title="Laporan Laba Rugi">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">Laporan Laba Rugi</h5>
                <a class="btn btn-outline-danger" id="pdfBtn" href="#" target="_blank">
                    <i class="icon-base bx bx-printer me-2"></i> Print PDF
                </a>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Tipe Periode</label>
                        <select class="form-select" id="typeSelect">
                            <option value="daily">Harian</option>
                            <option value="weekly">Mingguan</option>
                            <option value="monthly">Bulanan</option>
                            <option value="range">Rentang Tanggal</option>
                        </select>
                    </div>
                    <div class="col-md-3" id="dateField">
                        <label class="form-label">Tanggal</label>
                        <input type="text" id="dateInput" class="form-control" placeholder="DD-MM-YYYY" />
                    </div>
                    <div class="col-md-3 d-none" id="monthField">
                        <label class="form-label">Bulan</label>
                        <input type="text" id="monthInput" class="form-control" placeholder="Pilih bulan" />
                    </div>
                    <div class="col-md-4 d-none" id="rangeField">
                        <label class="form-label">Rentang Tanggal</label>
                        <input type="text" id="rangeInput" class="form-control" placeholder="DD-MM-YYYY ke DD-MM-YYYY" />
                    </div>
                    <div class="col-md-2 align-self-end">
                        <button class="btn btn-primary w-100" id="applyBtn"><i class="bx bx-filter me-1"></i> Tampilkan</button>
                    </div>
                </div>

                <hr/>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <div class="card shadow-none border border-primary text-primary">
                            <div class="card-body">
                                <div class="text-muted">Periode</div>
                                <div class="fw-semibold fs-5" id="periodLabel">-</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card shadow-none border border-success text-success">
                            <div class="card-body">
                                <div class="text-muted">Total Penjualan</div>
                                <div class="fw-bold fs-5" id="totalSales">Rp 0</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card shadow-none border border-secondary text-secondary">
                            <div class="card-body">
                                <div class="text-muted">Laba</div>
                                <div class="fw-bold fs-5" id="profit">Rp 0</div>
                            </div>
                        </div>
                    </div>
                </div>

                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tabChart" type="button" role="tab">Grafik</button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tabTable" type="button" role="tab">Tabel</button>
                    </li>
                </ul>
                <div class="tab-content p-3 border">
                    <div class="tab-pane fade show active" id="tabChart" role="tabpanel">
                        <div id="profitChart" style="min-height:320px;"></div>
                    </div>
                    <div class="tab-pane fade" id="tabTable" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th>Komponen</th>
                                        <th class="text-end">Jumlah</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Total Penjualan</td>
                                        <td class="text-end" id="tblSales">Rp 0</td>
                                    </tr>
                                    <tr>
                                        <td>Total Pembelian</td>
                                        <td class="text-end" id="tblPurchase">Rp 0</td>
                                    </tr>
                                    <tr>
                                        <td>Total Biaya (Cost)</td>
                                        <td class="text-end" id="tblCost">Rp 0</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Laba</strong></td>
                                        <td class="text-end" id="tblProfit"><strong>Rp 0</strong></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <link rel="stylesheet" href="{{ asset('assets/vendor/libs/flatpickr/flatpickr.css') }}" />
        <link rel="stylesheet" href="{{ asset('assets/vendor/libs/apex-charts/apex-charts.css') }}" />
    @endpush

    @push('scripts')
        <script src="{{ asset('assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
        <script src="{{ asset('assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
        <script>
        (function(){
            const typeEl = document.getElementById('typeSelect');
            const dateEl = document.getElementById('dateField');
            const monthEl = document.getElementById('monthField');
            const rangeEl = document.getElementById('rangeField');
            const applyBtn = document.getElementById('applyBtn');
            const pdfBtn = document.getElementById('pdfBtn');

            const dateInput = flatpickr('#dateInput', {dateFormat: 'd-m-Y', defaultDate: new Date()});
            const rangeInput = flatpickr('#rangeInput', {mode: 'range', dateFormat: 'd-m-Y'});
            const monthInput = document.getElementById('monthInput');
            const monthPicker = flatpickr('#monthInput', {
                dateFormat: 'Y-m',        // nilai yang dipakai di computePeriod
                altInput: true,           // tampilkan input alternatif yang user-friendly
                altFormat: 'F Y',         // tampilan untuk user
                defaultDate: new Date(),  // set default ke bulan sekarang
                onChange: function(){     // update link PDF saat bulan berubah
                    updatePdfLink();
                }
            });

            function toggleFields(){
                const type = typeEl.value;
                dateEl.classList.add('d-none');
                monthEl.classList.add('d-none');
                rangeEl.classList.add('d-none');
                if (type === 'daily' || type === 'weekly') {
                    dateEl.classList.remove('d-none');
                } else if (type === 'monthly') {
                    monthEl.classList.remove('d-none');
                } else if (type === 'range') {
                    rangeEl.classList.remove('d-none');
                }
            }

            function startOfWeek(date){
                const d = new Date(date);
                const day = d.getDay(); // 0-6 Sun-Sat
                const diff = (day === 0 ? -6 : 1) - day; // Monday as start
                d.setDate(d.getDate() + diff);
                return d;
            }
            function endOfWeek(date){
                const s = startOfWeek(date);
                const e = new Date(s);
                e.setDate(s.getDate() + 6);
                return e;
            }
            function fmt(d){
                const yyyy = d.getFullYear();
                const mm = String(d.getMonth()+1).padStart(2,'0');
                const dd = String(d.getDate()).padStart(2,'0');
                return `${yyyy}-${mm}-${dd}`;
            }
            function rupiah(n){
                const v = Math.round(Number(n||0));
                return 'Rp ' + v.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            }

            let chart = null;
            function renderChart(values){
                const data = [
                    Number(values.sales ?? 0),
                    Number(values.purchase ?? 0),
                    Number(values.cost ?? 0),
                    Number(values.profit ?? 0)
                ];
                const categories = ['Penjualan', 'Pembelian', 'Biaya', 'Laba'];

                const baseOpts = {
                    chart: { type: 'bar', height: 320 },
                    xaxis: { categories },
                    dataLabels: {
                        enabled: true,
                        formatter: function(val){ return 'Rp ' + Math.round(val).toLocaleString('id-ID'); }
                    },
                    tooltip: { y: { formatter: function(val){ return 'Rp ' + Math.round(val).toLocaleString('id-ID'); } } }
                };

                if (!chart) {
                    const options = Object.assign({}, baseOpts, {
                        series: [{ name: 'Jumlah', data }]
                    });
                    chart = new ApexCharts(document.querySelector('#profitChart'), options);
                    chart.render();
                } else {
                    chart.destroy();
                    const options = Object.assign({}, baseOpts, {
                        series: [{ name: 'Jumlah', data }]
                    });
                    chart = new ApexCharts(document.querySelector('#profitChart'), options);
                    chart.render();
                }
            }

            function computePeriod(){
                const type = typeEl.value;
                let start, end, label;
                if (type === 'daily') {
                    const d = dateInput.selectedDates[0] || new Date();
                    start = fmt(d); end = fmt(d); label = `Harian ${start}`;
                } else if (type === 'weekly') {
                    const d = dateInput.selectedDates[0] || new Date();
                    const s = startOfWeek(d); const e = endOfWeek(d);
                    start = fmt(s); end = fmt(e); label = `Mingguan ${start} s/d ${end}`;
                } else if (type === 'monthly') {
                    const val = monthInput.value || new Date().toISOString().slice(0,7);
                    const parts = val.split('-');
                    const d = new Date(Number(parts[0]), Number(parts[1])-1, 1);
                    const s = new Date(d.getFullYear(), d.getMonth(), 1);
                    const e = new Date(d.getFullYear(), d.getMonth()+1, 0);
                    start = fmt(s); end = fmt(e); label = `Bulanan ${val}`;
                } else { // range
                    const sel = rangeInput.selectedDates;
                    const s = sel[0] || new Date();
                    const e = sel[1] || sel[0] || new Date();
                    start = fmt(s); end = fmt(e); label = `Rentang ${start} s/d ${end}`;
                }
                return { type, start, end, label };
            }

            function updatePdfLink(){
                const p = computePeriod();
                const base = "{{ route('reports.profit-loss.pdf') }}";
                let url = new URL(base, window.location.origin);
                url.searchParams.set('type', p.type);
                if (p.type === 'monthly') {
                    url.searchParams.set('month', p.start.slice(0,7));
                } else if (p.type === 'daily' || p.type === 'weekly') {
                    url.searchParams.set('date', p.start);
                } else {
                    url.searchParams.set('start_date', p.start);
                    url.searchParams.set('end_date', p.end);
                }
                pdfBtn.setAttribute('href', url.toString());
            }

            function loadReport(){
                const p = computePeriod();
                document.getElementById('periodLabel').textContent = p.label;
                updatePdfLink();
                $.ajax({
                    url: "{{ route('pos.reports.profit-loss') }}",
                    method: 'GET',
                    dataType: 'json',
                    data: { start_date: p.start, end_date: p.end },
                    success: function(resp){
                        const data = (resp && resp.data) ? resp.data : {};
                        const totals = {
                            sales: Number(data.total_sales || 0),
                            purchase: Number(data.total_purchase || 0),
                            cost: Number(data.total_cost || 0),
                            profit: Number(data.profit || 0)
                        };
                        document.getElementById('totalSales').textContent = rupiah(totals.sales);
                        document.getElementById('profit').textContent = rupiah(totals.profit);

                        document.getElementById('tblSales').textContent = rupiah(totals.sales);
                        document.getElementById('tblPurchase').textContent = rupiah(totals.purchase);
                        document.getElementById('tblCost').textContent = rupiah(totals.cost);
                        document.getElementById('tblProfit').textContent = rupiah(totals.profit);

                        renderChart(totals);
                    },
                    error: function(xhr){
                        console.error(xhr);
                        const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Gagal memuat laporan';
                        document.getElementById('periodLabel').textContent = msg;
                    }
                });
            }

            typeEl.addEventListener('change', function(){ toggleFields(); updatePdfLink(); });
            document.getElementById('dateInput').addEventListener('change', updatePdfLink);
            document.getElementById('monthInput').addEventListener('change', updatePdfLink);
            document.getElementById('rangeInput').addEventListener('change', updatePdfLink);
            applyBtn.addEventListener('click', function(){ loadReport(); });

            toggleFields();
            updatePdfLink();
            loadReport();
        })();
        </script>
    @endpush
</x-layouts.app>