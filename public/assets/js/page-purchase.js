document.addEventListener("DOMContentLoaded", function () {
  // Init DataTable purchases
  const purchasesTableEl = document.querySelector(".datatables-purchases");
  if (purchasesTableEl) {
    const dt = new DataTable(purchasesTableEl, {
      ajax: { url: "/api/purchases", dataSrc: "" },
      columns: [
        { data: "id", orderable: false, render: DataTable.render.select() },
        { data: "invoice_number" },
        { data: "supplier" },
        { data: "date" },
        { data: "total" },
        { data: "items" },
      ],
      columnDefs: [
        {
          orderable: false,
          searchable: false,
          responsivePriority: 5,
          checkboxes: { selectAllRender: '<input type="checkbox" class="form-check-input">' },
          render: () => '<input type="checkbox" class="dt-checkboxes form-check-input">'
        },
        {
          targets: 1,
          responsivePriority: 3,
          render: (data, type, row) => `<span class="text-heading">${row.invoice_number || '-'}</span>`
        },
        {
          targets: 2,
          responsivePriority: 2,
          render: (data, type, row) => `<span class="text-heading">${row.supplier ? (row.supplier.name || '-') : '-'}</span>`
        },
        {
          targets: 3,
          responsivePriority: 2,
          render: (data, type, row) => {
            const d = row.date ? moment(row.date).format('DD MMM YYYY') : '-';
            return `<span class="text-heading">${d}</span>`;
          }
        },
        {
          targets: 4,
          className: 'text-end',
          render: (data, type, row) => {
            const val = row.total || 0;
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(val);
          }
        },
        {
          targets: 5,
          className: 'text-center',
          render: (data, type, row) => `${Array.isArray(row.items) ? row.items.length : 0}`
        },
      ],
      select: { style: "multi", selector: "td:nth-child(2)" },
      order: [[3, "desc"]],
      layout: {
        topStart: {
          rowClass: "row mx-3 my-0 justify-content-between",
          features: [{ pageLength: { menu: [10, 25, 50, 100], text: "_MENU_" } }]
        },
        topEnd: {
          features: [
            { search: { placeholder: "Search Purchase", text: "_INPUT_" } }
          ]
        },
        bottomStart: { rowClass: "row mx-3 justify-content-between", features: ["info"] },
        bottomEnd: { paging: { firstLast: false } }
      },
      language: {
        paginate: {
          next: '<i class="icon-base bx bx-chevron-right icon-18px"></i>',
          previous: '<i class="icon-base bx bx-chevron-left icon-18px"></i>'
        }
      }
    });

    // Fix button style
    $(".dt-buttons > .btn-group > button").removeClass("btn-secondary");
  }

  // Styling tambahan
  setTimeout(() => {
    const styleFixes = [
      { selector: ".dt-buttons .btn", classToRemove: "btn-secondary" },
      { selector: ".dt-search .form-control", classToRemove: "form-control-sm" },
      { selector: ".dt-length .form-select", classToRemove: "form-select-sm", classToAdd: "ms-0" },
      { selector: ".dt-length", classToAdd: "mb-md-6 mb-0" },
      { selector: ".dt-search", classToAdd: "mb-md-6 mb-2" },
      {
        selector: ".dt-layout-end",
        classToRemove: "justify-content-between",
        classToAdd: "d-flex gap-md-4 justify-content-md-between justify-content-center gap-4 flex-wrap mt-0"
      },
      { selector: ".dt-layout-start", classToAdd: "mt-0" },
      { selector: ".dt-buttons", classToAdd: "d-flex gap-4 mb-md-0 mb-6" },
      { selector: ".dt-layout-table", classToRemove: "row mt-2" },
      { selector: ".dt-layout-full", classToRemove: "col-md col-12", classToAdd: "table-responsive" }
    ];

    styleFixes.forEach(({ selector, classToRemove, classToAdd }) => {
      document.querySelectorAll(selector).forEach(el => {
        if (classToRemove) classToRemove.split(" ").forEach(cls => el.classList.remove(cls));
        if (classToAdd) classToAdd.split(" ").forEach(cls => el.classList.add(cls));
      });
    });
  }, 100);

  // Cached lists
  let suppliers = [];
  let products = [];

  function initSupplierSelect2() {
    const $sel = $('#supplierSelect');
    if ($sel.length) {
      if ($sel.data('select2')) {
        $sel.select2('destroy');
      }
      $sel.select2({
        dropdownParent: $('#modalAddPurchase'),
        placeholder: 'Select Supplier',
        allowClear: true,
        width: '100%'
      });
    }
  }

  async function loadSuppliers() {
    return fetch('/api/suppliers')
      .then(r => r.json())
      .then(json => {
        suppliers = Array.isArray(json.data) ? json.data : [];
        const sel = document.getElementById('supplierSelect');
        if (sel) {
          // reset options, keep first default
          sel.querySelectorAll('option:not(:first-child)').forEach(o => o.remove());
          suppliers.forEach(s => {
            const opt = document.createElement('option');
            opt.value = s.id;
            opt.textContent = s.name;
            sel.appendChild(opt);
          });
          initSupplierSelect2();
        }
      })
      .catch(() => suppliers = []);
  }

  async function loadProducts() {
    return fetch('/api/products')
      .then(r => r.json())
      .then(json => {
        products = Array.isArray(json.data) ? json.data : [];
      })
      .catch(() => products = []);
  }

  function formatCurrency(val) {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(val || 0);
  }

  // Items table helpers
  const itemsTbody = document.querySelector('#itemsTable tbody');
  const addItemBtn = document.getElementById('addItemRow');
  const grandTotalEl = document.getElementById('grandTotal');

  function productOptionsHtml(selectedId) {
    const opts = products.map(p => `<option value="${p.id}" data-cost="${p.cost_price || 0}" ${selectedId && selectedId == p.id ? 'selected' : ''}>${p.name}</option>`).join('');
    return `<option value="">-- Select Product --</option>${opts}`;
  }

  function recalcRow(row) {
    const qty = parseInt(row.querySelector('.item-qty').value || '0', 10);
    const cost = parseFloat(row.querySelector('.item-cost').value || '0');
    const subtotal = qty * cost;
    row.querySelector('.item-subtotal').textContent = formatCurrency(subtotal);
    return subtotal;
  }

  function recalcGrandTotal() {
    let total = 0;
    if (itemsTbody) {
      itemsTbody.querySelectorAll('tr').forEach(tr => total += recalcRow(tr));
    }
    if (grandTotalEl) grandTotalEl.textContent = formatCurrency(total);
    return total;
  }

  function addItemRow(defaultProductId) {
    if (!itemsTbody) return;
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td>
        <select class="form-select item-product">
          ${productOptionsHtml(defaultProductId)}
        </select>
      </td>
      <td class="text-center">
        <input type="number" min="1" value="1" class="form-control item-qty" />
      </td>
      <td class="text-end">
        <input type="number" min="0" step="1" value="0" class="form-control text-end item-cost" />
      </td>
      <td class="text-end"><span class="item-subtotal">Rp 0</span></td>
      <td class="text-center">
        <button type="button" class="btn btn-danger btn-remove-item"><i class="bx bx-trash"></i></button>
      </td>
    `;
    itemsTbody.appendChild(tr);
  
    const productSel = tr.querySelector('.item-product');
    const costInput = tr.querySelector('.item-cost');
  
    // Helper: update cost from current selection and recalc total
    function updateCostFromSelection() {
      if (!productSel) return;
      const idx = productSel.selectedIndex;
      const opt = idx >= 0 ? productSel.options[idx] : null;
      const cost = opt ? parseFloat(opt.getAttribute('data-cost') || '0') : 0;
      if (costInput) costInput.value = isNaN(cost) ? 0 : cost;
      recalcGrandTotal();
    }
  
    // Init Select2 untuk dropdown produk di dalam modal
    if (productSel) {
      const $prod = $(productSel);
      if ($prod.data('select2')) {
        $prod.select2('destroy');
      }
      $prod.select2({
        dropdownParent: $('#modalAddPurchase'),
        placeholder: 'Select Product',
        width: '100%'
      });
  
      // Pastikan handler berjalan untuk kedua event
      $prod.on('select2:select', updateCostFromSelection);
      $prod.on('change', updateCostFromSelection);
    }
  
    tr.querySelector('.item-qty').addEventListener('input', recalcGrandTotal);
    costInput.addEventListener('input', recalcGrandTotal);
    tr.querySelector('.btn-remove-item').addEventListener('click', () => {
      tr.remove();
      recalcGrandTotal();
    });
  
    // Prefill biaya dari pilihan awal (jika ada)
    updateCostFromSelection();
  }

  // Load select data when modal shown
  const modalEl = document.getElementById('modalAddPurchase');
  if (modalEl) {
    modalEl.addEventListener('show.bs.modal', () => {
      Promise.all([loadSuppliers(), loadProducts()]).then(() => {
        // Reset items list and add one default row
        if (itemsTbody) itemsTbody.innerHTML = '';
        addItemRow();
      });
    });
  }

  if (addItemBtn) {
    addItemBtn.addEventListener('click', () => addItemRow());
  }

  // Submit create purchase
  const formEl = document.getElementById('createPurchaseForm');
  if (formEl) {
    formEl.addEventListener('submit', function (e) {
      e.preventDefault();

      const supplierIdRaw = formEl.querySelector('[name="supplier_id"]').value;
      const supplier_id = supplierIdRaw ? parseInt(supplierIdRaw, 10) : null;

      const items = [];
      itemsTbody.querySelectorAll('tr').forEach(tr => {
        const productId = tr.querySelector('.item-product').value;
        const qty = parseInt(tr.querySelector('.item-qty').value || '0', 10);
        const costPrice = parseFloat(tr.querySelector('.item-cost').value || '0');
        if (productId && qty > 0 && costPrice >= 0) {
          items.push({ product_id: parseInt(productId, 10), qty, cost_price: costPrice });
        }
      });

      if (items.length === 0) {
        toastError('Add at least one item to purchase');
        return;
      }

      const submitBtn = formEl.querySelector('button[type="submit"]');
      submitBtn.disabled = true;
      submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Saving...';

      fetch('/api/purchases', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
        body: JSON.stringify({ supplier_id, items })
      })
      .then(async (r) => {
        const json = await r.json().catch(() => ({}));
        if (!r.ok) throw json;
        return json;
      })
      .then(res => {
        toastSuccess(res.message || 'Purchase record saved successfully');
        // close modal and reload page
        const modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) modal.hide();
        setTimeout(() => window.location.reload(), 800);
      })
      .catch(err => {
        const msg = (err && err.message) ? err.message : 'Failed to save purchase record';
        toastError(msg);
      })
      .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = 'Save Purchase';
      });
    });
  }
});