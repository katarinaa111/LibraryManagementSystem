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
