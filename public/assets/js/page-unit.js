document.addEventListener("DOMContentLoaded", function () {
  // Warna dari config
  config.colors.borderColor;
  config.colors.bodyBg;
  config.colors.headingColor;

  let unitTable = document.querySelector(".datatables-units");

  // Inisialisasi DataTable
  if (unitTable) {
    let dt = new DataTable(unitTable, {
      ajax: { url: "/api/units", dataSrc: "data" },
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
            <div class="d-flex align-items-center gap-2">
              <a href="javascript:;" class="btn btn-icon btn-edit" data-id="${row.id}" data-name="${row.name}">
                <i class="icon-base bx bx-edit icon-md"></i>
              </a>
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
            { search: { placeholder: "Search Unit", text: "_INPUT_" } }
          ]
        },
        bottomStart: { rowClass: "row mx-3 justify-content-between", features: ["info"] },
        bottomEnd: { paging: { firstLast: false } }
      },
      language: {
        sLengthMenu: "_MENU_",
        search: "",
        searchPlaceholder: "Search Unit",
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
    title.innerHTML = `Delete Unit ${name} !!`;
    modalConfirmDelete.show();
  });

  $('#confirm-delete').on('click', function () {
    if (idDelete) {
      $(this).prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Loading...');
      $.ajax({
        url: '/api/units/' + idDelete,
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
  const modalEditDiv = document.getElementById('modalEditUnit');
  const modalEdit = modalEditDiv ? new bootstrap.Modal(modalEditDiv) : null;
  const editForm = document.querySelector('#editUnitForm');
  let editUnitValidator;

  // Init validator for edit form
  if (editForm && typeof FormValidation !== "undefined") {
    editUnitValidator = FormValidation.formValidation(editForm, {
      fields: {
        name: {
          validators: {
            notEmpty: { message: "Please enter unit name" },
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

  // Open edit modal and populate form
  $(document).on('click', '.btn-edit', function (e) {
    e.preventDefault();
    const id = $(this).data('id');
    const name = $(this).data('name');

    if (!editForm || !modalEdit) return;

    const $form = $(editForm);
    // reset previous errors
    $form.find('.is-invalid').removeClass('is-invalid');
    $form.find('.global-error-alert').remove();

    // fill values
    editForm.elements.id.value = id;
    editForm.elements.name.value = name || '';

    // show modal
    modalEdit.show();
  });

  // Submit edit form
  $('#editUnitForm').on('submit', function (e) {
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
        url: '/api/units/' + id,
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
          if (response.success) {
            $form.find('.global-error-alert').remove();
            toastSuccess(response.message);
            setTimeout(() => {
              window.location.reload();
            }, 800);
          } else {
            toastError(response.message || 'Failed to update unit');
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
              if (input.length) {
                input.addClass('is-invalid');
              }
              if (Array.isArray(fieldMessages) && fieldMessages.length) {
                messages.push(fieldMessages[0]);
              }
            });
            if (messages.length) {
              showGlobalError($form, messages);
            }
          } else {
            toastError('Failed to update unit');
          }
          button.prop('disabled', false).html('<i class="icon-base bx bx-save me-1"></i> Save Changes');
        }
      });
    };

    if (editUnitValidator) {
      editUnitValidator.validate().then(function (status) {
        if (status === 'Valid') {
          runAjaxUpdate();
        }
      });
    } else {
      runAjaxUpdate();
    }
  });
  // End edit data

  // Inisialisasi FormValidation untuk form Add Unit
  let addUnitForm = document.querySelector("#addNewUnitForm");
  let addUnitValidator;
  if (addUnitForm && typeof FormValidation !== "undefined") {
    addUnitValidator = FormValidation.formValidation(addUnitForm, {
      fields: {
        name: {
          validators: {
            notEmpty: { message: "Please enter unit name" },
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
    $form.find('.global-error-alert').remove();

    const alertHtml = `
      <div class="alert alert-danger global-error-alert" role="alert">
        ${messages.map(m => `<div>${m}</div>`).join('')}
      </div>
    `;

    $form.prepend(alertHtml);
  };

  // Save data
  $('#addNewUnitForm').on('submit', function (e) {
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
        url: '/api/units',
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
            toastError(response.message || 'Failed to save unit');
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
            toastError('Failed to save unit');
          }

          button.prop('disabled', false).html('<i class="icon-base bx bx-save me-1"></i> Save');
        }
      });
    };

    // Validasi frontend dulu: jika lolos, baru kirim ke backend
    if (addUnitValidator) {
      addUnitValidator.validate().then(function (status) {
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
