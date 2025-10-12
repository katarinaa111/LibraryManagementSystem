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

app.run();
