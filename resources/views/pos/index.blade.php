<x-layouts.app title="POS">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="app-ecommerce-category">
            <div class="card mb-4">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">Daftar Produk</h5>
                    <div class="d-flex gap-2" style="min-width: 380px">
                        <input type="text" id="searchInput" class="form-control" placeholder="Cari produk..." />
                        <select id="categoryFilter" class="form-select" style="min-width: 160px">
                            <option value="">Semua Kategori</option>
                        </select>
                        <button class="btn btn-primary" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasCart">
                            <i class="icon-base bx bx-cart me-2 icon-md"></i> Keranjang
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div id="productsGrid" class="row g-3">
                        @foreach(($products ?? []) as $p)
                            <div class="col-6 col-md-3">
                                <div class="card shadow-none bg-transparent border h-100 product-card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h6 class="text-truncate mb-2" title="{{ $p->name }}">{{ $p->name }}</h6>
                                            <span class="badge bg-label-primary">Stok: {{ $p->stock }}</span>
                                        </div>
                                        <div class="fw-semibold mb-2">Rp {{ number_format($p->selling_price, 0, ',', '.') }}</div>
                                        <button class="btn btn-sm btn-primary w-100 btn-add-to-cart" data-id="{{ $p->id }}" data-name="{{ $p->name }}" data-price="{{ $p->selling_price }}" data-stock="{{ $p->stock }}">
                                            <i class="icon-base bx bx-cart-add me-1 icon-md"></i> Tambah ke Keranjang
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Offcanvas Keranjang -->
            <div class="offcanvas offcanvas-end" style="width: 650px" tabindex="-1" id="offcanvasCart" aria-labelledby="offcanvasCartLabel">
                <!-- Offcanvas Header -->
                <div class="offcanvas-header py-6">
                    <h5 class="offcanvas-title" id="offcanvasCartLabel">Keranjang</h5>
                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <!-- Offcanvas Body -->
                <div class="offcanvas-body border-top">
                    <div class="d-flex justify-content-end mb-2">
                        <button class="btn btn-sm btn-outline-danger" id="btnClearCart"><i class="icon-base bx bx-trash me-1 icon-sm"></i> Kosongkan</button>
                    </div>
                    <div class="table-responsive mb-3" style="max-height: 320px;">
                        <table class="table table-bordered table-sm align-middle">
                            <thead>
                                <tr>
                                    <th>Produk</th>
                                    <th class="text-end">Harga</th>
                                    <th style="width: 150px" class="text-end">Qty</th>
                                    <th class="text-end">Subtotal</th>
                                    <th style="width: 50px"></th>
                                </tr>
                            </thead>
                            <tbody id="cartBody"></tbody>
                        </table>
                    </div>
                    <div class="d-flex flex-column gap-2">
                        <div class="d-flex justify-content-between">
                            <span>Subtotal</span>
                            <span id="subtotalText" class="fw-semibold">Rp 0</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <label for="discountInput" class="mb-0">Diskon</label>
                            <input type="number" id="discountInput" class="form-control" min="0" value="0" style="max-width: 160px" />
                        </div>
                        <hr/>
                        <div class="d-flex justify-content-between">
                            <span>Total</span>
                            <span id="totalText" class="fw-bold fs-5">Rp 0</span>
                        </div>
                        <!-- Pembayaran (dipindah ke offcanvas) -->
                        <div class="row g-3 mt-3">
                            <div class="col-6">
                                <label class="form-label">Metode</label>
                                <select id="paymentMethod" class="form-select">
                                    <option value="cash">Tunai</option>
                                    <option value="transfer">Transfer</option>
                                    <option value="qris">QRIS</option>
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label">Bayar</label>
                                <input type="number" id="paidInput" class="form-control" min="0" placeholder="0" />
                            </div>
                            <div class="col-12 d-flex justify-content-between align-items-center">
                                <span>Kembalian</span>
                                <span id="changeText" class="fw-semibold">Rp 0</span>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Offcanvas Footer: ubah tombol jadi langsung checkout -->
                <div class="offcanvas-footer p-3 border-top">
                    <button class="btn btn-primary w-100 btn-lg" id="btnCheckout">
                        <i class="bx bx-check-circle me-1"></i> Proses Pembayaran
                    </button>
                </div>
            </div>

        </div>
    </div>
    @push('scripts')
    <script>
    (function(){
        const baseUrl = document.documentElement.getAttribute('data-base-url') || '';
        const fmt = new Intl.NumberFormat('id-ID');
        const toRp = (n) => 'Rp ' + fmt.format(Math.max(0, Number(n||0)));

        let cart = [];
        let categoriesLoaded = false;

        function renderCart() {
            const tbody = $('#cartBody');
            tbody.empty();
            cart.forEach((item, idx) => {
                const subtotal = item.qty * item.price;
                const tr = $(
                    `<tr>
                        <td>
                            <div class="text-truncate" title="${item.name}">${item.name}</div>
                            <small class="text-muted">Stok: ${item.stock}</small>
                        </td>
                        <td class="text-end">${toRp(item.price)}</td>
                        <td>
                            <div class="input-group input-group-sm">
                                <button class="btn btn-outline-secondary btn-dec" data-idx="${idx}">-</button>
                                <input type="text" readonly class="form-control text-center qty-input" data-idx="${idx}" min="1" max="${item.stock}" value="${item.qty}">
                                <button class="btn btn-outline-secondary btn-inc" data-idx="${idx}">+</button>
                            </div>
                        </td>
                        <td class="text-end">${toRp(subtotal)}</td>
                        <td>
                            <button class="btn btn-icon btn-remove" data-idx="${idx}"><i class="icon-base text-danger bx bx-trash"></i></button>
                        </td>
                    </tr>`
                );
                tbody.append(tr);
            });
            updateTotals();
        }

        function computeTotal(){
            const subtotal = cart.reduce((acc, i) => acc + (i.qty * i.price), 0);
            const discount = Number($('#discountInput').val() || 0);
            return Math.max(0, subtotal - discount);
        }

        function updateTotals(){
            const subtotal = cart.reduce((acc, i) => acc + (i.qty * i.price), 0);
            const discount = Number($('#discountInput').val() || 0);
            const total = Math.max(0, subtotal - discount);

            // Auto set paid to total for non-cash methods
            const method = $('#paymentMethod').val();
            if (method === 'transfer' || method === 'qris') {
                $('#paidInput').val(total);
            }

            const paid = Number($('#paidInput').val() || 0);
            const change = Math.max(0, paid - total);

            $('#subtotalText').text(toRp(subtotal));
            $('#totalText').text(toRp(total));
            $('#changeText').text(toRp(change));
        }

        function addToCart(p){
            const idx = cart.findIndex(i => i.id === p.id);
            if (idx >= 0) {
                const nextQty = cart[idx].qty + 1;
                cart[idx].qty = Math.min(nextQty, p.stock);
            } else {
                cart.push({ id: p.id, name: p.name, price: Number(p.price), stock: Number(p.stock||0), qty: 1 });
            }
            toastSuccess(`"${p.name}" ditambahkan ke keranjang.`);
            renderCart();
        }

        function loadCategories(){
            if (categoriesLoaded) return;
            $.get(`${baseUrl}/api/categories`, function(resp){
                const $sel = $('#categoryFilter');
                if (resp && resp.data) {
                    resp.data.forEach(c => {
                        $sel.append(`<option value="${c.id}">${c.name}</option>`);
                    });
                }
                categoriesLoaded = true;
            });
        }

        function renderProducts(products){
            const $grid = $('#productsGrid');
            $grid.empty();
            if (!products || !products.length) {
                $grid.html('<div class="col-12"><div class="alert alert-warning">Produk tidak ditemukan.</div></div>');
                return;
            }
            products.forEach(p => {
                $grid.append(
                    `<div class="col-6 col-md-3">
                        <div class="card shadow-none bg-transparent border h-100 product-card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="text-truncate mb-2" title="${p.name}">${p.name}</h6>
                                    <span class="badge bg-label-primary">Stok: ${p.stock ?? 0}</span>
                                </div>
                                <div class="fw-semibold mb-2">${toRp(p.selling_price)}</div>
                                <button class="btn btn-sm btn-primary w-100 btn-add-to-cart" data-id="${p.id}" data-name="${p.name}" data-price="${p.selling_price}" data-stock="${p.stock ?? 0}">
                                    <i class="icon-base bx bx-cart-add icon-md me-1"></i> Tambah ke Keranjang
                                </button>
                            </div>
                        </div>
                    </div>`
                );
            });
        }

        function fetchProducts(){
            const search = $('#searchInput').val();
            const category_id = $('#categoryFilter').val();
            $.get(`${baseUrl}/api/products`, { search, category_id }, function(resp){
                renderProducts(resp.data || []);
            });
        }

        // Listeners
        $(document).on('click', '.btn-add-to-cart', function(){
            const p = {
                id: Number($(this).data('id')),
                name: $(this).data('name'),
                price: Number($(this).data('price')),
                stock: Number($(this).data('stock')),
            };
            addToCart(p);
        });

        $(document).on('click', '.btn-remove', function(){
            const idx = Number($(this).data('idx'));
            cart.splice(idx,1);
            renderCart();
        });

        $(document).on('input', '.qty-input', function(){
            const idx = Number($(this).data('idx'));
            const val = Math.max(1, Math.min(Number($(this).val()||1), cart[idx].stock));
            cart[idx].qty = val;
            renderCart();
        });

        $(document).on('click', '.btn-inc', function(){
            const idx = Number($(this).data('idx'));
            cart[idx].qty = Math.min(cart[idx].qty + 1, cart[idx].stock);
            renderCart();
        });
        $(document).on('click', '.btn-dec', function(){
            const idx = Number($(this).data('idx'));
            cart[idx].qty = Math.max(cart[idx].qty - 1, 1);
            renderCart();
        });

        $('#discountInput, #paidInput').on('input', updateTotals);

        // Auto amount and readonly toggle based on payment method
        $('#paymentMethod').on('change', function(){
            const method = $(this).val();
            const total = computeTotal();
            if (method === 'transfer' || method === 'qris') {
                $('#paidInput').val(total).prop('readonly', true);
            } else {
                $('#paidInput').prop('readonly', false);
            }
            updateTotals();
        });

        $('#btnClearCart').on('click', function(){
            cart = [];
            renderCart();
        });

        $('#searchInput').on('input', function(){
            // debounce ringan
            clearTimeout(window._searchTimer);
            window._searchTimer = setTimeout(fetchProducts, 250);
        });
        $('#categoryFilter').on('change', fetchProducts);

        $('#btnCheckout').on('click', function(){
            if (!cart.length) {
                return toastError('Keranjang masih kosong.');
            }
            const items = cart.map(i => ({ product_id: i.id, qty: i.qty, price: i.price }));
            const discount = Number($('#discountInput').val() || 0);
            const payment_method = $('#paymentMethod').val();
            const $btn = $(this);
            const originalHtml = $btn.html();
            $btn.prop('disabled', true).addClass('disabled').html('<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Memproses...');
            $.post(`${baseUrl}/api/pos`, { items, discount, payment_method })
                .done(function(resp){
                    toastSuccess('Transaksi berhasil disimpan');
                    // Reset cart
                    cart = [];
                    renderCart();
                    // Reset payment form
                    $('#discountInput').val(0);
                    $('#paymentMethod').val('cash').trigger('change');
                    $('#paidInput').val(0).prop('readonly', false);
                    updateTotals();
                    // Close offcanvas
                    try { bootstrap.Offcanvas.getOrCreateInstance('#offcanvasCart').hide(); } catch(e) {}
                    // Open receipt and auto print
                    const saleId = resp?.data?.id;
                    if (saleId) {
                        window.open(`${baseUrl}/pos/receipt/${saleId}`, '_blank');
                    }
                })
                .fail(function(xhr){
                    const msg = xhr.responseJSON?.message || 'Gagal menyimpan transaksi';
                    toastError(msg);
                })
                .always(() => {
                    $btn.prop('disabled', false).removeClass('disabled').html(originalHtml);
                });
        });

        // Init
        loadCategories();
        updateTotals();
        // Produk awal sudah dirender lewat server-side; tetap sinkron dengan filter jika digunakan
        fetchProducts(); // opsional untuk selalu ambil dari API
    })();
    </script>
    @endpush
</x-layouts.app>