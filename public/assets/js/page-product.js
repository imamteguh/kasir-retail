document.addEventListener("DOMContentLoaded", async function () {
  // Warna dari config
  config.colors.borderColor;
  config.colors.bodyBg;
  config.colors.headingColor;

  const productTable = document.querySelector(".datatables-products");

  const formatCurrency = (value) => {
    try {
      return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(value || 0);
    } catch (e) {
      return value;
    }
  };

  // Load options for categories and units
  const populateSelect = async (url, selectEls, key = 'data') => {
    try {
      const res = await fetch(url);
      const json = await res.json();
      const list = json[key] || [];
      selectEls.forEach((el) => {
        if (!el) return;
        el.innerHTML = '<option value="">-- Select --</option>' + list.map(item => `<option value="${item.id}">${item.name}</option>`).join('');
      });
    } catch (err) {
      console.error('Failed populate select', url, err);
    }
  };

  const addForm = document.getElementById('addNewProductForm');
  const editForm = document.getElementById('editProductForm');
  
  // Tambah: populate select kategori & unit + inisialisasi Select2
  const categorySelects = [addForm?.querySelector('select[name="category_id"]'), editForm?.querySelector('select[name="category_id"]')].filter(Boolean);
  const unitSelects = [addForm?.querySelector('select[name="unit_id"]'), editForm?.querySelector('select[name="unit_id"]')].filter(Boolean);

  await populateSelect('/api/categories', categorySelects);
  await populateSelect('/api/units', unitSelects);

  const initSelect2 = (elements, placeholder) => {
    elements.forEach((el) => {
      if (!el) return;
      const $el = $(el);
      if (typeof $el.select2 === 'function') {
        // Hapus select2 sebelumnya agar tidak double init
        if ($el.hasClass("select2-hidden-accessible")) {
          $el.select2('destroy');
        }

        $el.select2({
          dropdownParent: $el.closest('.modal'),
          placeholder: placeholder,
          width: '100%',
          allowClear: true
        });
      }
    });
  };
  
  $('#modalAddProduct').on('shown.bs.modal', function () {
    initSelect2([$(this).find('select[name="category_id"]')[0]], '-- Select Category --');
    initSelect2([$(this).find('select[name="unit_id"]')[0]], '-- Select Unit --');
  });

  $('#modalEditProduct').on('shown.bs.modal', function () {
    initSelect2([$(this).find('select[name="category_id"]')[0]], '-- Select Category --');
    initSelect2([$(this).find('select[name="unit_id"]')[0]], '-- Select Unit --');
  });

  // Inisialisasi DataTable
  if (productTable) {
    const dt = new DataTable(productTable, {
      ajax: { url: "/api/products", dataSrc: "data" },
      columns: [
        { data: "id" },                           // Kolom control
        { data: "id", orderable: false, render: DataTable.render.select() }, // Checkbox
        { data: "code" },                         // Code
        { data: "name" },                         // Name
        { data: "category.name" },                // Category
        { data: "unit.name" },                    // Unit
        { data: "stock" },                        // Stock
        { data: "selling_price" },                // Selling Price
        { data: "is_active" },                    // Status
        { data: null }                            // Actions
      ],
      columnDefs: [
        // Kolom control
        {
          className: "control",
          searchable: false,
          orderable: false,
          responsivePriority: 2,
          targets: 0,
          render: () => ""
        },
        // Checkbox
        {
          targets: 1,
          orderable: false,
          searchable: false,
          responsivePriority: 3,
          checkboxes: { selectAllRender: '<input type="checkbox" class="form-check-input">' },
          render: () => '<input type="checkbox" class="dt-checkboxes form-check-input">'
        },
        // Code
        {
          targets: 2,
          responsivePriority: 1,
          render: (data, type, row) => `<span class="text-heading">${row.code || '-'}</span>`
        },
        // Name
        {
          targets: 3,
          render: (data, type, row) => `<span class="text-heading">${row.name || '-'}</span>`
        },
        // Category
        {
          targets: 4,
          render: (data, type, row) => `${row.category?.name || '-'}`
        },
        // Unit
        {
          targets: 5,
          render: (data, type, row) => `${row.unit?.name || '-'}`
        },
        // Stock
        {
          targets: 6,
          className: 'text-end',
          render: (data, type, row) => `${row.stock ?? 0}`
        },
        // Selling Price
        {
          targets: 7,
          className: 'text-end',
          render: (data, type, row) => `${formatCurrency(row.selling_price)}`
        },
        // Status
        {
          targets: 8,
          className: 'text-center',
          render: (data, type, row) => {
            const status = row.is_active ? 'Publish' : 'Inactive';
            const statusClass = row.is_active ? 'bg-label-success' : 'bg-label-danger';
            return `<span class="badge ${statusClass}" text-capitalized>${status}</span>`;
          }
        },
        // Actions
        {
          targets: -1,
          title: "Actions",
          searchable: false,
          orderable: false,
          className: 'text-center',
          render: (data, type, row) => `
            <div class="d-inline-block text-nowrap">
              <button class="btn btn-icon btn-edit"
                 data-id="${row.id}"
                 data-name="${row.name}"
                 data-category_id="${row.category_id || ''}"
                 data-unit_id="${row.unit_id || ''}"
                 data-cost_price="${row.cost_price || 0}"
                 data-selling_price="${row.selling_price || 0}"
                 data-stock="${row.stock || 0}"
                 data-min_stock="${row.min_stock || 0}"
                 data-is_active="${row.is_active || 0}">
                <i class="icon-base bx bx-edit icon-md"></i>
              </button>
              <button class="btn btn-icon btn-delete" data-id="${row.id}" data-name="${row.name}">
                <i class="icon-base bx bx-trash icon-md"></i>
              </button>
            </div>`
        }
      ],
      select: { style: "multi", selector: "td:nth-child(2)" },
      order: [[2, "asc"]],
      // Layout
      layout: {
        topStart: {
          rowClass: "row mx-3 my-0 justify-content-between",
          features: [
            {
              search: {
                className: "me-5 ms-n4 pe-5 mb-n6 mb-md-0",
                placeholder: "Search Product",
                text: "_INPUT_"
              }
            }
          ]
        },
        topEnd: {
          features: [
            {
              pageLength: { menu: [10, 25, 50, 100], text: "_MENU_" },
              buttons: [
                {
                  extend: "collection",
                  className: "btn btn-label-secondary dropdown-toggle me-4",
                  text: `
                    <span class="d-flex align-items-center gap-2">
                      <i class="icon-base bx bx-export icon-xs"></i>
                      <span class="d-none d-sm-inline-block">Export</span>
                    </span>`,
                  buttons: [
                    {
                      extend: "print",
                      text: `
                        <span class="d-flex align-items-center">
                          <i class="icon-base bx bx-printer me-1"></i>Print
                        </span>`,
                      className: "dropdown-item",
                      exportOptions: { columns: [1, 2, 3, 4, 5, 6] },
                      customize: function (doc) {
                        doc.document.body.style.color = config.colors.headingColor;
                        doc.document.body.style.borderColor = config.colors.borderColor;
                        doc.document.body.style.backgroundColor = config.colors.bodyBg;

                        let table = doc.document.body.querySelector("table");
                        table.classList.add("compact");
                        table.style.color = "inherit";
                        table.style.borderColor = "inherit";
                        table.style.backgroundColor = "inherit";
                      }
                    },
                    {
                      extend: "csv",
                      text: `
                        <span class="d-flex align-items-center">
                          <i class="icon-base bx bx-file me-1"></i>Csv
                        </span>`,
                      className: "dropdown-item",
                      exportOptions: { columns: [1, 2, 3, 4, 5, 6] }
                    },
                    {
                      extend: "excel",
                      text: `
                        <span class="d-flex align-items-center">
                          <i class="icon-base bx bxs-file-export me-1"></i>Excel
                        </span>`,
                      className: "dropdown-item",
                      exportOptions: { columns: [1, 2, 3, 4, 5, 6] }
                    },
                    {
                      extend: "pdf",
                      text: `
                        <span class="d-flex align-items-center">
                          <i class="icon-base bx bxs-file-pdf me-1"></i>Pdf
                        </span>`,
                      className: "dropdown-item",
                      exportOptions: { columns: [1, 2, 3, 4, 5, 6] }
                    },
                    {
                      extend: "copy",
                      text: `<i class="icon-base bx bx-copy me-1"></i>Copy`,
                      className: "dropdown-item",
                      exportOptions: { columns: [1, 2, 3, 4, 5, 6] }
                    }
                  ]
                }
              ]
            }
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
      },
      // Mode responsive
      responsive: {
        details: {
          display: DataTable.Responsive.display.modal({
            header: (row) => "Details of " + row.data().name
          }),
          type: "column",
          renderer: (api, rowIdx, columns) => {
            let data = columns.map(col => {
              return col.title !== ""
                ? `<tr data-dt-row="${col.rowIndex}" data-dt-column="${col.columnIndex}">
                    <td>${col.title}:</td>
                    <td>${col.data}</td>
                   </tr>`
                : "";
            }).join("");

            if (data) {
              let wrapper = document.createElement("div");
              wrapper.classList.add("table-responsive");

              let table = document.createElement("table");
              wrapper.appendChild(table);

              table.classList.add("table");

              let tbody = document.createElement("tbody");
              tbody.innerHTML = data;
              table.appendChild(tbody);

              return wrapper;
            }
            return false;
          }
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

  // Delete data
  let idDelete;
  $(document).on('click', '.btn-delete', function (e) {
    e.preventDefault();
    idDelete = $(this).data('id');
    const name = $(this).data('name');
    let title = modalConfirmDeleteDiv.querySelector('.modal-title');
    title.innerHTML = `Delete Product ${name} !!`;
    modalConfirmDelete.show();
  });

  $('#confirm-delete').on('click', function () {
    if (idDelete) {
      $(this).prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Loading...');
      $.ajax({
        url: '/api/products/' + idDelete,
        method: 'DELETE',
        success: function(response) {
          if (response.success) {
              toastSuccess(response.message);
              setTimeout(() => window.location.reload(), 800);
            } else {
              toastError(response.message || 'Failed to delete data');
            }
        },
        error: function(xhr) {
          toastError('Failed to delete data');
        }
      });
    }
  });
  // End delete data

  // Edit data
  const modalEditDiv = document.getElementById('modalEditProduct');
  const modalEdit = modalEditDiv ? new bootstrap.Modal(modalEditDiv) : null;
  let editProductValidator;

  // Init validator for edit form
  if (editForm && typeof FormValidation !== "undefined") {
    editProductValidator = FormValidation.formValidation(editForm, {
      fields: {
        name: {
          validators: {
            notEmpty: { message: "Please enter product name" },
            stringLength: { max: 150, message: "Name must be less than 150 characters" },
          },
        },
        cost_price: {
          validators: {
            notEmpty: { message: "Please enter cost price" },
            numeric: { message: "Cost price must be numeric" }
          }
        },
        selling_price: {
          validators: {
            notEmpty: { message: "Please enter selling price" },
            numeric: { message: "Selling price must be numeric" }
          }
        },
      },
      plugins: {
        trigger: new FormValidation.plugins.Trigger(),
        bootstrap5: new FormValidation.plugins.Bootstrap5({ eleValidClass: "", rowSelector: () => ".form-control-validation" }),
        autoFocus: new FormValidation.plugins.AutoFocus(),
      },
    });
  }

  // Open edit modal and populate form
  // Bagian handler klik edit modal
  $(document).on('click', '.btn-edit', function (e) {
    e.preventDefault();
    const id = $(this).data('id');
    const name = $(this).data('name');
    const category_id = $(this).data('category_id');
    const unit_id = $(this).data('unit_id');
    const cost_price = $(this).data('cost_price');
    const selling_price = $(this).data('selling_price');
    const stock = $(this).data('stock');
    const min_stock = $(this).data('min_stock');
    const is_active = $(this).data('is_active');

    if (!editForm || !modalEdit) return;

    const $form = $(editForm);
    // reset previous errors
    $form.find('.is-invalid').removeClass('is-invalid');
    $form.find('.global-error-alert').remove();

    // fill values
    editForm.elements.id.value = id;
    editForm.elements.name.value = name || '';
    editForm.elements.cost_price.value = cost_price || 0;
    editForm.elements.selling_price.value = selling_price || 0;
    editForm.elements.stock.value = stock || 0;
    editForm.elements.min_stock.value = min_stock || 0;
    editForm.elements.is_active.checked = is_active || false;

    // Tambah: set selected kategori & unit di Select2 saat edit
    if (editForm.elements.category_id) {
      $(editForm.elements.category_id).val(category_id || '').trigger('change');
    }
    if (editForm.elements.unit_id) {
      $(editForm.elements.unit_id).val(unit_id || '').trigger('change');
    }

    // show modal
    modalEdit.show();
  });

  // Submit edit form
  $('#editProductForm').on('submit', function (e) {
    e.preventDefault();
    if (!editForm) return;
    const button = $(this).find('button[type="submit"]');
    const $form = $(this);
    const id = editForm.elements.id ? editForm.elements.id.value : null;

    const runAjaxUpdate = () => {
      if (!id) return;
      button.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Saving...');
      const formData = new FormData(editForm);
      $.ajax({
        url: '/api/products/' + id,
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
          if (response.success) {
            $form.find('.global-error-alert').remove();
            toastSuccess(response.message);
            setTimeout(() => { window.location.reload(); }, 800);
          } else {
            toastError(response.message || 'Failed to update product');
            button.prop('disabled', false).html('<i class="icon-base bx bx-save me-1"></i> Save Changes');
          }
        },
        error: function(xhr) {
          if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
            const errors = xhr.responseJSON.errors;
            const messages = [];
            Object.keys(errors).forEach(function(field) {
              const fieldMessages = errors[field];
              const input = $form.find(`[name="${field}"]`);
              if (input.length) { input.addClass('is-invalid'); }
              if (Array.isArray(fieldMessages) && fieldMessages.length) { messages.push(fieldMessages[0]); }
            });
            if (messages.length) { showGlobalError($form, messages); }
          } else {
            toastError('Failed to update product');
          }
          button.prop('disabled', false).html('<i class="icon-base bx bx-save me-1"></i> Save Changes');
        }
      });
    };

    if (typeof editProductValidator !== 'undefined') {
      editProductValidator.validate().then(function(status) {
        if (status === 'Valid') { runAjaxUpdate(); }
      });
    } else {
      runAjaxUpdate();
    }
  });

  // Create data
  const modalAddDiv = document.getElementById('modalAddProduct');
  const modalAdd = modalAddDiv ? new bootstrap.Modal(modalAddDiv) : null;
  let addProductValidator;

  if (addForm && typeof FormValidation !== "undefined") {
    addProductValidator = FormValidation.formValidation(addForm, {
      fields: {
        name: {
          validators: {
            notEmpty: { message: "Please enter product name" },
            stringLength: { max: 150, message: "Name must be less than 150 characters" },
          },
        },
        cost_price: { 
          validators: { 
            notEmpty: { message: "Please enter cost price" }, 
            numeric: { message: "Cost price must be numeric" } 
          } 
        },
        selling_price: { 
          validators: { 
            notEmpty: { message: "Please enter selling price" }, 
            numeric: { message: "Selling price must be numeric" } 
          } 
        },
      },
      plugins: {
        trigger: new FormValidation.plugins.Trigger(),
        bootstrap5: new FormValidation.plugins.Bootstrap5({ eleValidClass: "", rowSelector: () => ".form-control-validation" }),
        autoFocus: new FormValidation.plugins.AutoFocus(),
      },
    });
  }

  $('#addNewProductForm').on('submit', function (e) {
    e.preventDefault();
    if (!addForm) return;
    const button = $(this).find('button[type="submit"]');
    const $form = $(this);

    const runAjaxCreate = () => {
      button.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Saving...');
      const formData = new FormData(addForm);
      $.ajax({
        url: '/api/products',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
          if (response.success) {
            $form.find('.global-error-alert').remove();
            toastSuccess(response.message);
            setTimeout(() => { window.location.reload(); }, 800);
          } else {
            toastError(response.message || 'Failed to create product');
            button.prop('disabled', false).html('<i class="icon-base bx bx-save me-1"></i> Save');
          }
        },
        error: function(xhr) {
          if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
            const errors = xhr.responseJSON.errors;
            const messages = [];
            Object.keys(errors).forEach(function(field) {
              const fieldMessages = errors[field];
              const input = $form.find(`[name="${field}"]`);
              if (input.length) { input.addClass('is-invalid'); }
              if (Array.isArray(fieldMessages) && fieldMessages.length) { messages.push(fieldMessages[0]); }
            });
            if (messages.length) { showGlobalError($form, messages); }
          } else {
            toastError('Failed to create product');
          }
          button.prop('disabled', false).html('<i class="icon-base bx bx-save me-1"></i> Save');
        }
      });
    };

    if (typeof addProductValidator !== 'undefined') {
      addProductValidator.validate().then(function(status) {
        if (status === 'Valid') { runAjaxCreate(); }
      });
    } else {
      runAjaxCreate();
    }
  });

  // Helper: tampilkan alert global error (backend)
  const showGlobalError = ($form, messages) => {
    $form.find('.global-error-alert').remove();
    const alertHtml = `
      <div class="alert alert-danger global-error-alert" role="alert">
        ${messages.map(m => `<div>${m}</div>`).join('')}
      </div>
    `;
    $form.prepend(alertHtml);
  };
});