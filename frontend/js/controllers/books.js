function renderBooks() {
  let books = [
    {
      id: 1,
      title: "1984",
      author: "George Orwell",
      category: "Fiction",
      status: "Available",
    },
    {
      id: 2,
      title: "A Brief History of Time",
      author: "Stephen Hawking",
      category: "Science",
      status: "Borrowed",
    },
  ];

  const renderBooks = (list) => {
    let rows = "";
    list.forEach((b) => {
      rows += `
        <tr>
          <td>${b.title}</td>
          <td>${b.author}</td>
          <td>${b.category}</td>
          <td>${b.status}</td>
          <td>
            <button class="btn btn-sm btn-warning editBook" data-id="${b.id}">Edit</button>
            <button class="btn btn-sm btn-danger deleteBook" data-id="${b.id}">Delete</button>
          </td>
        </tr>`;
    });
    $("#booksTableBody").html(rows);
  };

  const filterBooks = () => {
    let query = $("#searchBook").val().toLowerCase();
    let category = $("#filterCategory").val();
    let filtered = books.filter(
      (b) =>
        (b.title.toLowerCase().includes(query) ||
          b.author.toLowerCase().includes(query)) &&
        (category === "" || b.category === category)
    );
    renderBooks(filtered);
  };

  // Initial render
  renderBooks(books);

  // Event: Search & Filter
  $("#searchBook, #filterCategory").on("input change", filterBooks);

  // Event: Add book button
  $("#addBookBtn").click(function () {
    $("#bookId").val("");
    $("#bookForm")[0].reset();
    $("#bookModalLabel").text("Add Book");
    $("#bookModal").modal("show");
  });

  // Event: Edit book
  $(document).on("click", ".editBook", function () {
    let id = $(this).data("id");
    let b = books.find((x) => x.id == id);
    $("#bookId").val(b.id);
    $("#bookTitle").val(b.title);
    $("#bookAuthor").val(b.author);
    $("#bookCategory").val(b.category);
    $("#bookStatus").val(b.status);
    $("#bookModalLabel").text("Edit Book");
    $("#bookModal").modal("show");
  });

  // Event: Delete book
  $(document).on("click", ".deleteBook", function () {
    if (confirm("Are you sure you want to delete this book?")) {
      let id = $(this).data("id");
      books = books.filter((b) => b.id != id);
      filterBooks();
    }
  });

  // Event: Save book
  $("#bookForm").submit(function (e) {
    e.preventDefault();
    let id = $("#bookId").val();
    let newBook = {
      id: id ? parseInt(id) : Date.now(),
      title: $("#bookTitle").val(),
      author: $("#bookAuthor").val(),
      category: $("#bookCategory").val(),
      status: $("#bookStatus").val(),
    };

    if (id) {
      // Update existing
      books = books.map((b) => (b.id == id ? newBook : b));
    } else {
      // Add new
      books.push(newBook);
    }

    $("#bookModal").modal("hide");
    filterBooks();
  });
}
