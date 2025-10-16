var app = $.spapp({
  defaultView: "dashboard",
  templateDir: "./views/",
});

app.route({
  view: "books",
  load: "books.html",
  onCreate: renderBooks,
  onReady: function () {},
});

app.route({
  view: "members",
  load: "members.html",
  onCreate: renderMembers,
  onReady: function () {},
});

app.route({
  view: "borrowing",
  load: "borrowing-books.html",
  onCreate: renderBorrowedBooks,
  onReady: function () {},
});

app.run();
