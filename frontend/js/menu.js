var Menu = {
  currentRole: null,
  rolesConfig: {
    admin: {
      dashboard: true,
      borrowing: true,
      books: true,
      authors: true,
      categories: true,
      members: true,
      settings: true,
    },
    member: {
      dashboard: true,
      borrowing: true,
      books: true,
      authors: true,
      categories: true,
      members: false,
      settings: true,
    },
  },
  fetchRole: function (cb) {
    var user = Auth.currentUser();
    if (!user) {
      cb(null);
      return;
    }
    var email = user.email || user.username || null;
    if (!email) {
      cb(user.role || null);
      return;
    }
    RestClient.get(
      "/users/by-email?email=" + encodeURIComponent(email),
      function (resp) {
        var role = resp?.role || user.role || null;
        cb(role);
      },
      function () {
        cb(user.role || null);
      }
    );
  },
  render: function () {
    Menu.fetchRole(function (role) {
      Menu.currentRole = role || "member";
      var cfg =
        Menu.rolesConfig[Menu.currentRole] || Menu.rolesConfig["member"];
      function show(id, visible) {
        var el = document.getElementById(id);
        if (el) el.style.display = visible ? "" : "none";
      }
      show("menuBorrowing", !!cfg.borrowing);
      show("menuBooks", !!cfg.books);
      show("menuAuthors", !!cfg.authors);
      show("menuCategories", !!cfg.categories);
      show("menuMembers", !!cfg.members);
      show("menuSettings", !!cfg.settings);
      show("menuDashboard", !!cfg.dashboard);
    });
  },
};
window.addEventListener("auth:updated", function () {
  Menu.render();
});
