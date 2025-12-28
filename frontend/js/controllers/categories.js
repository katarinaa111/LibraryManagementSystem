// Categories management controller
// - CRUD via /categories endpoints (GET, POST, PUT, DELETE)
// - Admin-only for create/update/delete; members can view and search
// - Client-side search and filtering by name/description
function renderCategories() {
  if (!Auth.requireAuth()) return;

  let categoriesCache = [];
  const currentUser = Auth.currentUser() || {};
  const isAdmin = (currentUser.role || "").toLowerCase() === "admin";

  // Render table rows from cached list
  const renderTable = (list) => {
    let rows = "";
    list.forEach((c) => {
      const desc = (c.description || "").trim();
      rows += `
        <tr>
          <td>${c.name || ""}</td>
          <td>${desc}</td>
          <td>
            ${isAdmin
          ? `<button class="btn btn-sm btn-warning editCategory" data-id="${c.id}">Edit</button>
            <button class="btn btn-sm btn-danger deleteCategory" data-id="${c.id}">Delete</button>`
          : ``
        }
          </td>
        </tr>`;
    });
    $("#categoriesTableBody").html(rows);
  };

  // Load all categories: GET /categories
  const loadCategories = () => {
    RestClient.get("/categories", function (response) {
      categoriesCache = response;
      renderTable(response);
    });
  };

  // Local filter by name or description
  const filterCategories = () => {
    const query = ($("#searchCategory").val() || "").toLowerCase();
    const filtered = categoriesCache.filter((c) => {
      const name = (c.name || "").toLowerCase();
      const desc = (c.description || "").toLowerCase();
      return name.includes(query) || desc.includes(query);
    });
    renderTable(filtered);
  };

  $("#searchCategory").on("input", filterCategories);

  // Open modal to add category (admin only)
  $("#addCategoryBtn").click(function () {
    if (!isAdmin) return;
    $("#categoryId").val("");
    $("#categoryForm")[0].reset();
    $("#categoryModalLabel").text("Add Category");
    $("#categoryModal").modal("show");
  });

  // Open modal to edit category (admin only)
  $(document).on("click", ".editCategory", function () {
    const id = $(this).data("id");
    const c = categoriesCache.find((x) => x.id == id);
    if (!c) return;
    $("#categoryId").val(c.id);
    $("#categoryName").val(c.name || "");
    $("#categoryDescription").val(c.description || "");
    $("#categoryModalLabel").text("Edit Category");
    $("#categoryModal").modal("show");
  });

  // Delete category (admin only): DELETE /categories/{id}
  $(document).on("click", ".deleteCategory", function () {
    if (!isAdmin) return;
    if (!confirm("Are you sure you want to delete this category?")) return;
    const id = $(this).data("id");
    RestClient.delete(`/categories/${id}`, null, function () {
      loadCategories();
    });
  });

  // Submit add/update form (admin only): POST/PUT /categories
  const validator = $("#categoryForm").validate({
    rules: {
      name: {
        required: true,
        minlength: 2,
      },
      description: {
        maxlength: 500,
      },
    },
    messages: {
      name: {
        required: "Please enter the category name",
        minlength: "Name must be at least 2 characters long",
      },
      description: {
        maxlength: "Description cannot exceed 500 characters",
      },
    },
    submitHandler: function (form) {
      if (!isAdmin) return;
      const id = $("#categoryId").val();
      const name = ($("#categoryName").val() || "").trim();
      const description = ($("#categoryDescription").val() || "").trim();
      
      const payload = { name, description };

      $.blockUI({ message: '<h3>Processing...</h3>' });

      const success = function () {
        $.unblockUI();
        $("#categoryModal").modal("hide");
        loadCategories();
      };

      const error = function (err) {
        $.unblockUI();
        alert(err?.data?.message || "Error saving category");
      };

      if (id) {
        RestClient.put(`/categories/${id}`, payload, success, error);
      } else {
        RestClient.post(`/categories`, payload, success, error);
      }
    },
  });

  // Reset validation when modal is hidden
  $("#categoryModal").on("hidden.bs.modal", function () {
    validator.resetForm();
    $(".error").removeClass("error");
  });

  loadCategories();

  if (!isAdmin) {
    try {
      $("#addCategoryBtn").hide();
      $("#categoriesTable thead th:last").hide();
    } catch (e) { }
  }
}
