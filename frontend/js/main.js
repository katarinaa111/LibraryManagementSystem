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

app.run();
