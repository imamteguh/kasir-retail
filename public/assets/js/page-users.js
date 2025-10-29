document.addEventListener("DOMContentLoaded", function () {
  // Warna dari config
  config.colors.borderColor;
  config.colors.bodyBg;
  config.colors.headingColor;

  let userTable = document.querySelector(".datatables-users");

  // Status mapping sesuai API: active, expired, pending
  const statusMap = {
    pending: { title: "Pending", class: "bg-label-warning" },
    active: { title: "Active", class: "bg-label-success" },
    expired: { title: "Expired", class: "bg-label-secondary" }
  };

  // Inisialisasi DataTable
  if (userTable) {
    let dt = new DataTable(userTable, {
      ajax: { url: "/api/users", dataSrc: "data" },
      columns: [
        { data: "id" },                           // Control
        { data: "id", orderable: false, render: DataTable.render.select() }, // Checkbox
        { data: "name" },                         // User (name+email+avatar render)
        { data: "role" },                         // Role
        { data: "plan" },                         // Plan
        { data: "subscription_status" },          // Status
        { data: "created_at" },                   // Created
        { data: null }                            // Actions
      ],
      columnDefs: [
        // Control
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
          responsivePriority: 4,
          checkboxes: { selectAllRender: '<input type="checkbox" class="form-check-input">' },
          render: () => '<input type="checkbox" class="dt-checkboxes form-check-input">'
        },
        // User (name + email + avatar)
        {
          targets: 2,
          responsivePriority: 3,
          render: (data, type, row) => {
            const fullName = row.name;
            const email = row.email;
            const avatar = row.avatar;

            let avatarHtml;
            if (avatar) {
              const src = avatar.startsWith("http")
                ? avatar
                : (avatar.startsWith("/") ? avatar : `/storage/${avatar}`);
              avatarHtml = `<img src="${src}" alt="Avatar" class="rounded-circle">`;
            } else {
              const initialsArr = (fullName?.match(/\b\w/g) || []).map(e => e.toUpperCase());
              const initials = ((initialsArr.shift() || "") + (initialsArr.pop() || "")).toUpperCase();
              const colors = ["success", "danger", "warning", "info", "dark", "primary", "secondary"];
              const color = colors[Math.floor(Math.random() * colors.length)];
              avatarHtml = `<span class="avatar-initial rounded-circle bg-label-${color}">${initials}</span>`;
            }

            return `
              <div class="d-flex justify-content-start align-items-center user-name">
                <div class="avatar-wrapper">
                  <div class="avatar avatar-sm me-4">${avatarHtml}</div>
                </div>
                <div class="d-flex flex-column">
                  <span class="text-heading text-truncate fw-medium">${fullName}</span>
                  <small>${email || ""}</small>
                </div>
              </div>`;
          }
        },
        // Role (map ikon untuk role aplikasi)
        {
          targets: 3,
          render: (data, type, row) => {
            const role = row.role;
            const label = role?.replace("_", " ") || "";
            if (type === "filter" || type === "sort") {
              return role; // gunakan nilai mentah untuk pencarian & sorting
            }
            const icons = {
              super_admin: '<i class="icon-base bx bx-shield-quarter text-danger me-2"></i>',
              owner: '<i class="icon-base bx bx-store text-primary me-2"></i>',
              cashier: '<i class="icon-base bx bx-credit-card-front text-success me-2"></i>'
            };
            return `<span class='text-truncate d-flex align-items-center text-heading'>${icons[role] || ""}${label}</span>`;
          }
        },
        // Plan: tampilkan "Free" jika plan null, dan gunakan nilai mentah untuk filter/sort
        {
          targets: 4,
          render: (data, type, row) => {
            const planLabel = row.plan || "Free";
            if (type === "filter" || type === "sort") {
              return planLabel; // pastikan DataTables punya nilai "Free" untuk opsi filter
            }
            return `<span class="text-heading">${planLabel}</span>`;
          }
        },
        // Status subscription
        {
          targets: 5,
          render: (data, type, row) => {
            const key = (row.subscription_status || "").toLowerCase();
            const status = statusMap[key] || { title: row.subscription_status || "-", class: "bg-label-secondary" };
            return `<span class="badge ${status.class}" text-capitalize>${status.title}</span>`;
          }
        },
        // Created at
        {
          targets: 6,
          render: (data, type, row) => `<span class="text-heading">${row.created_at || "-"}</span>`
        },
        // Actions
        {
          targets: -1,
          title: "Actions",
          searchable: false,
          orderable: false,
          render: (data, type, row) => `
            <div class="d-flex align-items-center">
              <a href="javascript:;" class="btn btn-icon">
                <i class="icon-base bx bx-show icon-md"></i>
              </a>
              <a href="javascript:;" class="btn btn-icon btn-delete" data-id="${row.id}" data-name="${row.name}">
                <i class="icon-base bx bx-trash icon-md"></i>
              </a>
              <a href="javascript:;" class="btn btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                <i class="icon-base bx bx-dots-vertical-rounded icon-md"></i>
              </a>
              <div class="dropdown-menu dropdown-menu-end m-0">
                <a href="javascript:;" class="dropdown-item">Edit</a>
                <a href="javascript:;" class="dropdown-item">Suspend</a>
              </div>
            </div>`
        }
      ],
      select: { style: "multi", selector: "td:nth-child(2)" },
      order: [[2, "desc"]],

      // Layout
      layout: {
        topStart: {
          rowClass: "row mx-3 my-0 justify-content-between",
          features: [{ pageLength: { menu: [10, 25, 50, 100], text: "_MENU_" } }]
        },
        topEnd: {
          features: [
            { search: { placeholder: "Search User", text: "_INPUT_" } }
          ]
        },
        bottomStart: { rowClass: "row mx-3 justify-content-between", features: ["info"] },
        bottomEnd: { paging: { firstLast: false } }
      },

      language: {
        sLengthMenu: "_MENU_",
        search: "",
        searchPlaceholder: "Search User",
        paginate: {
          next: '<i class="icon-base bx bx-chevron-right icon-18px"></i>',
          previous: '<i class="icon-base bx bx-chevron-left icon-18px"></i>'
        }
      },

      responsive: {
        details: {
          display: DataTable.Responsive.display.modal({
            header: (row) => "Details of " + row.data().name
          }),
          type: "column",
          renderer: (api, rowIdx, columns) => {
            let data = columns.map(col =>
              col.title !== ""
                ? `<tr data-dt-row="${col.rowIndex}" data-dt-column="${col.columnIndex}">
                    <td>${col.title}:</td>
                    <td>${col.data}</td>
                  </tr>`
                : ""
            ).join("");

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
      },

      initComplete: function () {
        let api = this.api();

        // Filter dropdown helper
        const addFilter = (colIndex, targetSelector, selectId, placeholder) => {
          let column = api.column(colIndex);
          let select = document.createElement("select");
          select.id = selectId;
          select.className = "form-select text-capitalize";
          select.innerHTML = `<option value="">${placeholder}</option>`;
          document.querySelector(targetSelector).appendChild(select);

          select.addEventListener("change", () => {
            let val = select.value ? `^${select.value}$` : "";
            column.search(val, true, false).draw();
          });

          Array.from(new Set(column.data().toArray()))
            .sort()
            .forEach(val => {
              let option = document.createElement("option");
              option.value = val || "Free";
              option.textContent = val || "Free";
              select.appendChild(option);
            });
        };

        // Bangun filter Role dari data mentah agar cocok dengan pencarian
        {
          const targetSelector = ".user_role";
          const selectId = "UserRole";
          const placeholder = "Select Role";

          let select = document.createElement("select");
          select.id = selectId;
          select.className = "form-select text-capitalize";
          select.innerHTML = `<option value="">${placeholder}</option>`;
          document.querySelector(targetSelector).appendChild(select);

          const rawData = api.rows().data().toArray();
          const roles = Array.from(new Set(rawData.map(d => d.role))).sort();

          roles.forEach(role => {
            let option = document.createElement("option");
            option.value = role;                   // nilai mentah untuk search
            option.textContent = role.replace("_", " "); // label yang rapi
            select.appendChild(option);
          });

          select.addEventListener("change", () => {
            let val = select.value ? `^${select.value}$` : "";
            api.column(3).search(val, true, false).draw();
          });
        }

        // Filter Plan otomatis akan menyertakan "Free" dari data kolom karena render filter/sort
        addFilter(4, ".user_plan", "UserPlan", "Select Plan");

        // Filter status
        let statusSelect = document.createElement("select");
        statusSelect.id = "FilterUserStatus";
        statusSelect.className = "form-select text-capitalize";
        statusSelect.innerHTML = '<option value="">Select Status</option>';
        document.querySelector(".user_status").appendChild(statusSelect);

        statusSelect.addEventListener("change", () => {
          let val = statusSelect.value ? `^${statusSelect.value}$` : "";
          api.column(5).search(val, true, false).draw();
        });

        let statusColumn = api.column(5);
        Array.from(new Set(statusColumn.data().toArray()))
          .sort()
          .forEach(val => {
            const key = (val || "").toLowerCase();
            let option = document.createElement("option");
            option.value = statusMap[key]?.title || val;
            option.textContent = statusMap[key]?.title || val;
            option.className = "text-capitalize";
            statusSelect.appendChild(option);
          });
      }
    });

    // Event listener modal
    document.addEventListener("show.bs.modal", (e) => {
      if (e.target.classList.contains("dtr-bs-modal")) handleDelete();
    });
    document.addEventListener("hide.bs.modal", (e) => {
      if (e.target.classList.contains("dtr-bs-modal")) handleDelete();
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
    title.innerHTML = `Hapus User ${name} !!`;
    modalConfirmDelete.show();
  });

  $('#confirm-delete').on('click', function () {
    if (idDelete) {
      $(this).prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Menghapus...');
      $.ajax({
        url: '/api/users/' + idDelete,
        method: 'DELETE',
        success: function(response) {
          if (response.success) {
              toastSuccess(response.message);
              setTimeout(() => window.location.reload(), 800);
            } else {
              toastError(response.message || 'Gagal menyimpan data');
            }
        },
        error: function(xhr) {
          toastError('Terjadi kesalahan saat menghapus data');
        }
      });
    }
  });
  // End delete data
});
