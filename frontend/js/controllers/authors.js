// Authors management controller
// - CRUD via /authors endpoints (GET, POST, PUT, DELETE)
// - Admin-only for create/update/delete; members can view and search
// - Client-side search and filtering by name/bio
function renderAuthors() {
  if (!Auth.requireAuth()) return;

  let authorsCache = [];
  const currentUser = Auth.currentUser() || {};
  const isAdmin = (currentUser.role || "").toLowerCase() === "admin";

  // Render table rows from cached list
  const renderTable = (list) => {
    let rows = "";
    list.forEach((a) => {
      const bio = (a.bio || "").trim();
      rows += `
        <tr>
          <td>${a.name || ""}</td>
          <td>${bio}</td>
          <td>
            ${
              isAdmin
                ? `<button class="btn btn-sm btn-warning editAuthor" data-id="${a.id}">Edit</button>
            <button class="btn btn-sm btn-danger deleteAuthor" data-id="${a.id}">Delete</button>`
                : ``
            }
          </td>
        </tr>`;
    });
    $("#authorsTableBody").html(rows);
  };

  // Load all authors: GET /authors
  const loadAuthors = () => {
    RestClient.get("/authors", function (response) {
      authorsCache = response;
      renderTable(response);
    });
  };

  // Local filter by name or bio
  const filterAuthors = () => {
    const query = ($("#searchAuthor").val() || "").toLowerCase();
    const filtered = authorsCache.filter((a) => {
      const name = (a.name || "").toLowerCase();
      const bio = (a.bio || "").toLowerCase();
      return name.includes(query) || bio.includes(query);
    });
    renderTable(filtered);
  };

  $("#searchAuthor").on("input", filterAuthors);

  // Open modal to add author (admin only)
  $("#addAuthorBtn").click(function () {
    if (!isAdmin) return;
    $("#authorId").val("");
    $("#authorForm")[0].reset();
    $("#authorModalLabel").text("Add Author");
    $("#authorModal").modal("show");
  });

  // Open modal to edit author (admin only)
  $(document).on("click", ".editAuthor", function () {
    const id = $(this).data("id");
    const a = authorsCache.find((x) => x.id == id);
    if (!a) return;
    $("#authorId").val(a.id);
    $("#authorName").val(a.name || "");
    $("#authorBio").val(a.bio || "");
    $("#authorModalLabel").text("Edit Author");
    $("#authorModal").modal("show");
  });

  // Delete author (admin only): DELETE /authors/{id}
  $(document).on("click", ".deleteAuthor", function () {
    if (!isAdmin) return;
    if (!confirm("Are you sure you want to delete this author?")) return;
    const id = $(this).data("id");
    RestClient.delete(`/authors/${id}`, null, function () {
      loadAuthors();
    });
  });

  // Submit add/update form (admin only): POST/PUT /authors
  $("#authorForm").submit(function (e) {
    e.preventDefault();
    if (!isAdmin) return;
    const id = $("#authorId").val();
    const name = ($("#authorName").val() || "").trim();
    const bio = ($("#authorBio").val() || "").trim();
    if (!name) {
      alert("Name is required");
      return;
    }
    const payload = { name, bio };
    if (id) {
      RestClient.put(`/authors/${id}`, payload, function () {
        $("#authorModal").modal("hide");
        loadAuthors();
      });
    } else {
      RestClient.post(`/authors`, payload, function () {
        $("#authorModal").modal("hide");
        loadAuthors();
      });
    }
  });

  loadAuthors();

  if (!isAdmin) {
    try {
      $("#addAuthorBtn").hide();
      $("#authorsTable thead th:last").hide();
    } catch (e) {}
  }
}
