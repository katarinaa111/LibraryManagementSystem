function renderBorrowedBooks() {
  if (!Auth.requireAuth()) return;

  let booksCache = [];
  let usersCache = [];
  let recordsCache = [];
  const currentUser = Auth.currentUser() || {};
  const isAdmin = (currentUser.role || "").toLowerCase() === "admin";

  const loadSelectOptions = () => {
    $("#borrowBook").html('<option value="">Choose book...</option>');
    booksCache.forEach((b) => {
      $("#borrowBook").append(`<option value="${b.id}">${b.title}</option>`);
    });

    $("#borrowMember").html('<option value="">Choose member...</option>');
    usersCache.forEach((u) => {
      $("#borrowMember").append(
        `<option value="${u.id}">${u.username || u.name}</option>`
      );
    });
  };

  const renderTable = () => {
    let rows = "";
    recordsCache.forEach((r) => {
      const book =
        booksCache.find((b) => b.id == r.book_id)?.title || "Unknown";
      const member =
        usersCache.find((u) => u.id == r.user_id)?.username || "Unknown";
      const status = r.returned_date ? "Returned" : "Borrowed";
      rows += `
        <tr>
          <td>${book}</td>
          <td>${member}</td>
          <td>${r.borrowed_date || ""}</td>
          <td>${r.supposed_return_date || ""}</td>
          <td>
            <span class="badge ${
              status === "Returned" ? "bg-success" : "bg-warning text-dark"
            }">${status}</span>
          </td>
          <td>
            ${
              !r.returned_date
                ? isAdmin
                  ? `<button class="btn btn-sm btn-success markReturned" data-id="${r.id}">Return</button>`
                  : ``
                : `<span class="text-muted">Done</span>`
            }
          </td>
        </tr>`;
    });
    $("#borrowTableBody").html(rows);
  };

  const loadAll = () => {
    RestClient.get("/books", function (books) {
      booksCache = books;
      RestClient.get("/users", function (users) {
        usersCache = users;
        RestClient.get("/borrowedbooks", function (records) {
          recordsCache = records;
          loadSelectOptions();
          renderTable();
        });
      });
    });
  };

  $("#borrowForm").submit(function (e) {
    e.preventDefault();
    if (!isAdmin) {
      alert("Not authorized");
      return;
    }
    const bookId = parseInt($("#borrowBook").val());
    const memberId = parseInt($("#borrowMember").val());
    const dueDate = $("#borrowDueDate").val();
    const today = new Date().toISOString().split("T")[0];

    if (!bookId || !memberId || !dueDate) {
      alert("Please fill all fields!");
      return;
    }

    const payload = {
      user_id: memberId,
      book_id: bookId,
      borrowed_date: today,
      supposed_return_date: dueDate,
    };
    RestClient.post("/borrowedbooks", payload, function () {
      $("#borrowForm")[0].reset();
      loadAll();
    });
  });

  $(document).on("click", ".markReturned", function () {
    if (!isAdmin) {
      alert("Not authorized");
      return;
    }
    const id = $(this).data("id");
    const payload = { returned_date: new Date().toISOString().split("T")[0] };
    RestClient.put(`/borrowedbooks/${id}`, payload, function () {
      loadAll();
    });
  });

  loadAll();
  if (!isAdmin) {
    try {
      $("#borrowForm").closest(".card").hide();
      $("#borrowTable thead th:last").hide();
    } catch (e) {}
  }
}
