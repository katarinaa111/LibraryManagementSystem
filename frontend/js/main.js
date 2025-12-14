var app = $.spapp({
  defaultView: "dashboard",
  templateDir: "./views/",
});

function guard() {
  return Auth.requireAuth();
}

app.route({
  view: "books",
  load: "books.html",
  onCreate: function () {
    if (!guard()) return;
    renderBooks();
  },
  onReady: function () {},
});

app.route({
  view: "authors",
  load: "authors.html",
  onCreate: function () {
    if (!guard()) return;
    renderAuthors();
  },
  onReady: function () {},
});

app.route({
  view: "categories",
  load: "categories.html",
  onCreate: function () {
    if (!guard()) return;
    renderCategories();
  },
  onReady: function () {},
});

app.route({
  view: "members",
  load: "members.html",
  onCreate: function () {
    if (!guard()) return;
    renderMembers();
  },
  onReady: function () {},
});

app.route({
  view: "borrowing",
  load: "borrowing-books.html",
  onCreate: function () {
    if (!guard()) return;
    renderBorrowedBooks();
  },
  onReady: function () {},
});

app.run();
