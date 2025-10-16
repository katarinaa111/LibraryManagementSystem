function renderBorrowedBooks() {
  // Mock data (replace with API later)
  let books = [
    { id: 1, title: "1984" },
    { id: 2, title: "The Great Gatsby" },
    { id: 3, title: "A Brief History of Time" },
  ];

  let members = [
    { id: 1, name: "Alice Johnson" },
    { id: 2, name: "Mark Smith" },
    { id: 3, name: "John Doe" },
  ];

  let borrowRecords = [
    {
      id: 1,
      bookId: 1,
      memberId: 2,
      issueDate: "2025-10-10",
      dueDate: "2025-10-20",
      status: "Borrowed",
    },
  ];

  // Populate dropdowns
  const loadSelectOptions = () => {
    $("#borrowBook").html('<option value="">Choose book...</option>');
    books.forEach((b) => {
      $("#borrowBook").append(`<option value="${b.id}">${b.title}</option>`);
    });

    $("#borrowMember").html('<option value="">Choose member...</option>');
    members.forEach((m) => {
      $("#borrowMember").append(`<option value="${m.id}">${m.name}</option>`);
    });
  };

  // Render table
  const renderBorrowedBooks = () => {
    let rows = "";
    borrowRecords.forEach((r) => {
      let book = books.find((b) => b.id == r.bookId)?.title || "Unknown";
      let member = members.find((m) => m.id == r.memberId)?.name || "Unknown";
      rows += `
        <tr>
          <td>${book}</td>
          <td>${member}</td>
          <td>${r.issueDate}</td>
          <td>${r.dueDate}</td>
          <td>
            <span class="badge ${
              r.status === "Returned" ? "bg-success" : "bg-warning text-dark"
            }">
              ${r.status}
            </span>
          </td>
          <td>
            ${
              r.status === "Borrowed"
                ? `<button class="btn btn-sm btn-success markReturned" data-id="${r.id}">Return</button>`
                : `<span class="text-muted">Done</span>`
            }
          </td>
        </tr>`;
    });
    $("#borrowTableBody").html(rows);
  };

  // Initialize
  loadSelectOptions();
  renderBorrowedBooks();

  // Issue new book
  $("#borrowForm").submit(function (e) {
    e.preventDefault();
    const bookId = parseInt($("#borrowBook").val());
    const memberId = parseInt($("#borrowMember").val());
    const dueDate = $("#borrowDueDate").val();

    if (!bookId || !memberId || !dueDate) {
      alert("Please fill all fields!");
      return;
    }

    const newRecord = {
      id: Date.now(),
      bookId,
      memberId,
      issueDate: new Date().toISOString().split("T")[0],
      dueDate,
      status: "Borrowed",
    };

    borrowRecords.push(newRecord);
    $("#borrowForm")[0].reset();
    renderBorrowedBooks();
  });

  // Mark as returned
  $(document).on("click", ".markReturned", function () {
    const id = $(this).data("id");
    const record = borrowRecords.find((r) => r.id == id);
    if (record) {
      record.status = "Returned";
      renderBorrowedBooks();
    }
  });
}
