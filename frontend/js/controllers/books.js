function renderBooks() {
  if (!Auth.requireAuth()) return;

  let booksCache = [];
  const currentUser = Auth.currentUser() || {};
  const isAdmin = (currentUser.role || "").toLowerCase() === "admin";

  const renderTable = (list) => {
    let rows = "";
    list.forEach((b) => {
      rows += `
        <tr>
          <td>${b.title}</td>
          <td>${b.author?.name || b.author_name || ""}</td>
          <td>${b.category?.name || b.category_name || ""}</td>
          <td>${b.status || ""}</td>
          <td>
            ${
              isAdmin
                ? `<button class="btn btn-sm btn-warning editBook" data-id="${b.id}">Edit</button>
            <button class="btn btn-sm btn-danger deleteBook" data-id="${b.id}">Delete</button>`
                : ``
            }
          </td>
        </tr>`;
    });
    $("#booksTableBody").html(rows);
  };

  const loadBooks = () => {
    RestClient.get("/books", function (response) {
      booksCache = response;
      renderTable(response);
    });
  };

  const filterBooks = () => {
    const query = $("#searchBook").val().toLowerCase();
    const category = $("#filterCategory").val();
    const filtered = booksCache.filter((b) => {
      const title = (b.title || "").toLowerCase();
      const author = (b.author?.name || b.author_name || "").toLowerCase();
      const cat = b.category?.name || b.category_name || "";
      const matchesText = title.includes(query) || author.includes(query);
      const matchesCat = category === "" || cat === category;
      return matchesText && matchesCat;
    });
    renderTable(filtered);
  };

  $("#searchBook, #filterCategory").on("input change", filterBooks);

  $("#addBookBtn").click(function () {
    if (!isAdmin) return;
    $("#bookId").val("");
    $("#bookForm")[0].reset();
    $("#bookModalLabel").text("Add Book");
    $("#bookModal").modal("show");
  });

  $(document).on("click", ".editBook", function () {
    const id = $(this).data("id");
    const b = booksCache.find((x) => x.id == id);
    if (!b) return;
    $("#bookId").val(b.id);
    $("#bookTitle").val(b.title);
    $("#bookAuthorId").val(b.author_id || b.author?.id || "");
    $("#bookCategoryId").val(b.category_id || b.category?.id || "");
    $("#bookStatus").val(b.status || "Available");
    $("#bookModalLabel").text("Edit Book");
    $("#bookModal").modal("show");
  });

  $(document).on("click", ".deleteBook", function () {
    if (!confirm("Are you sure you want to delete this book?")) return;
    const id = $(this).data("id");
    RestClient.delete(`/books/${id}`, null, function () {
      loadBooks();
    });
  });

  const loadSelectors = () => {
    RestClient.get("/authors", function (authors) {
      const $sel = $("#bookAuthorId");
      $sel.html('<option value="">Select author...</option>');
      authors.forEach((a) =>
        $sel.append(`<option value="${a.id}">${a.name}</option>`)
      );
    });
    RestClient.get("/categories", function (categories) {
      const $sel = $("#bookCategoryId");
      $sel.html('<option value="">Select category...</option>');
      categories.forEach((c) =>
        $sel.append(`<option value="${c.id}">${c.name}</option>`)
      );
    });
  };

  $("#bookForm").submit(function (e) {
    e.preventDefault();
    const id = $("#bookId").val();
    const title = $("#bookTitle").val();
    const author_id = parseInt($("#bookAuthorId").val());
    const category_id = parseInt($("#bookCategoryId").val());
    const payload = { title, author_id, category_id };
    if (id) {
      RestClient.put(`/books/${id}`, payload, function () {
        $("#bookModal").modal("hide");
        loadBooks();
      });
    } else {
      RestClient.post(`/books`, payload, function () {
        $("#bookModal").modal("hide");
        loadBooks();
      });
    }
  });

  loadBooks();
  loadSelectors();

  if (!isAdmin) {
    try {
      $("#addBookBtn").hide();
      $("#booksTable thead th:last").hide();
    } catch (e) {}
  }
}
