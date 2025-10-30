document.addEventListener("DOMContentLoaded", function () {
  // Warna dari config
  config.colors.borderColor;
  config.colors.bodyBg;
  config.colors.headingColor;

  let categoryTable = document.querySelector(".datatables-categories");

  // Inisialisasi DataTable
  if (categoryTable) {
    let dt = new DataTable(categoryTable, {
      ajax: { url: "/api/categories", dataSrc: "data" },
      columns: [
        { data: "id", orderable: false, render: DataTable.render.select() }, // Checkbox
        { data: "name" },                         // Name
        { data: null }                            // Actions
      ],
      columnDefs: [
        // Checkbox
        {
          orderable: false,
          searchable: false,
          responsivePriority: 4,
          checkboxes: { selectAllRender: '<input type="checkbox" class="form-check-input">' },
          render: () => '<input type="checkbox" class="dt-checkboxes form-check-input">'
        },
        // Name
        {
          targets: 1,
          responsivePriority: 3,
          render: (data, type, row) => {
            return `<span class="text-heading">${row.name || "-"}</span>`;
          }
        },
        // Actions
        {
          targets: -1,
          title: "Actions",
          searchable: false,
          orderable: false,
          render: (data, type, row) => `
            <div class="d-flex align-items-center">
              <a href="javascript:;" class="btn btn-icon btn-delete" data-id="${row.id}" data-name="${row.name}">
                <i class="icon-base bx bx-trash icon-md"></i>
              </a>
            </div>`
        }
      ],
      select: { style: "multi", selector: "td:nth-child(2)" },
      order: [[1, "desc"]],
      // Layout
      layout: {
        topStart: {
          rowClass: "row mx-3 my-0 justify-content-between",
          features: [{ pageLength: { menu: [10, 25, 50, 100], text: "_MENU_" } }]
        },
        topEnd: {
          features: [
            { search: { placeholder: "Search Category", text: "_INPUT_" } }
          ]
        },
        bottomStart: { rowClass: "row mx-3 justify-content-between", features: ["info"] },
        bottomEnd: { paging: { firstLast: false } }
      },
      language: {
        sLengthMenu: "_MENU_",
        search: "",
        searchPlaceholder: "Search Category",
        paginate: {
          next: '<i class="icon-base bx bx-chevron-right icon-18px"></i>',
          previous: '<i class="icon-base bx bx-chevron-left icon-18px"></i>'
        }
      },
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
    title.innerHTML = `Delete Category ${name} !!`;
    modalConfirmDelete.show();
  });

  $('#confirm-delete').on('click', function () {
    if (idDelete) {
      $(this).prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Loading...');
      $.ajax({
        url: '/api/categories/' + idDelete,
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

  // Inisialisasi FormValidation untuk form Add Category (mirip halaman login)
  let addCategoryForm = document.querySelector("#addNewCategoryForm");
  let addCategoryValidator;
  if (addCategoryForm && typeof FormValidation !== "undefined") {
    addCategoryValidator = FormValidation.formValidation(addCategoryForm, {
      fields: {
        name: {
          validators: {
            notEmpty: { message: "Please enter category name" },
            stringLength: {
              max: 100,
              message: "Name must be less than 100 characters",
            },
          },
        },
      },
      plugins: {
        trigger: new FormValidation.plugins.Trigger(),
        bootstrap5: new FormValidation.plugins.Bootstrap5({
          eleValidClass: "",
          rowSelector: () => ".form-control-validation",
        }),
        autoFocus: new FormValidation.plugins.AutoFocus(),
      },
      init: (validator) => {
        // Penempatan pesan error jika dalam input-group
        validator.on("plugins.message.placed", (e) => {
          if (e.element.parentElement.classList.contains("input-group")) {
            e.element.parentElement.insertAdjacentElement(
              "afterend",
              e.messageElement
            );
          }
        });
      },
    });
  }

  // Helper: tampilkan alert global error (backend)
  const showGlobalError = ($form, messages) => {
    // Pastikan tidak duplikasi
    $form.find('.global-error-alert').remove();

    const alertHtml = `
      <div class="alert alert-danger global-error-alert" role="alert">
        ${messages.map(m => `<div>${m}</div>`).join('')}
      </div>
    `;
    // Tampilkan di bagian paling atas form agar mudah terlihat
    $form.prepend(alertHtml);
  };

  // Save data
  $('#addNewCategoryForm').on('submit', function (e) {
    e.preventDefault();
    const button = $(this).find('button[type="submit"]');
    const form = this;
    const $form = $(form);

    // Normalisasi nilai: trim spasi agar validator konsisten
    if (form.elements.name) {
      form.elements.name.value = form.elements.name.value.trim();
    }

    const runAjaxSave = () => {
      button.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Saving...');
      const formData = new FormData(form);
      $.ajax({
        url: '/api/categories',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
          if (response.success) {
            // Bersihkan alert jika sebelumnya ada
            $form.find('.global-error-alert').remove();
            toastSuccess(response.message);
            setTimeout(() => {
              window.location.reload();
            }, 800);
          } else {
            toastError(response.message || 'Failed to save data');
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
              if (input.length) {
                input.addClass('is-invalid');
              }
              if (Array.isArray(fieldMessages) && fieldMessages.length) {
                messages.push(fieldMessages[0]);
              }
            });

            // Tampilkan alert global dengan semua pesan
            if (messages.length) {
              showGlobalError($form, messages);
            }
          } else {
            toastError('Failed to save data');
          }

          button.prop('disabled', false).html('<i class="icon-base bx bx-save me-1"></i> Save');
        }
      });
    };

    // Validasi frontend dulu: jika lolos, baru kirim ke backend
    if (addCategoryValidator) {
      addCategoryValidator.validate().then(function (status) {
        if (status === 'Valid') {
          runAjaxSave();
        }
        // Jika tidak valid, tampilkan pesan frontend saja (backend tidak dipanggil)
      });
    } else {
      runAjaxSave();
    }
  });
  // End save data
});
