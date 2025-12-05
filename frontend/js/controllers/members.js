function renderMembers() {
  if (!Auth.requireAuth()) return;

  let usersCache = [];

  const renderTable = (list) => {
    let rows = "";
    list.forEach((u) => {
      rows += `
        <tr>
          <td>${u.username || u.name || ""}</td>
          <td>${u.email || ""}</td>
          <td>${u.role || ""}</td>
          <td>${u.joinDate || ""}</td>
          <td>
            <button class="btn btn-sm btn-warning editMember" data-id="${u.id}">Edit</button>
            <button class="btn btn-sm btn-danger deleteMember" data-id="${u.id}">Delete</button>
          </td>
        </tr>`;
    });
    $("#membersTableBody").html(rows);
  };

  const loadUsers = () => {
    RestClient.get("/users", function (response) {
      usersCache = response;
      renderTable(response);
    });
  };

  $("#addMemberBtn").click(function () {
    $("#memberForm")[0].reset();
    $("#memberId").val("");
    $("#memberModalLabel").text("Add Member");
    $("#memberModal").modal("show");
  });

  $(document).on("click", ".editMember", function () {
    const id = $(this).data("id");
    const u = usersCache.find((x) => x.id == id);
    if (!u) return;
    $("#memberId").val(u.id);
    $("#memberName").val(u.username || u.name || "");
    $("#memberEmail").val(u.email || "");
    $("#memberRole").val(u.role || "Member");
    $("#memberJoinDate").val(u.joinDate || "");
    $("#memberModalLabel").text("Edit Member");
    $("#memberModal").modal("show");
  });

  $(document).on("click", ".deleteMember", function () {
    if (!confirm("Are you sure you want to delete this member?")) return;
    const id = $(this).data("id");
    RestClient.delete(`/users/${id}`, null, function () {
      loadUsers();
    });
  });

  $("#memberForm").submit(function (e) {
    e.preventDefault();
    const id = $("#memberId").val();
    const payload = {
      username: $("#memberName").val(),
      email: $("#memberEmail").val(),
      role: normalizeRole($("#memberRole").val()),
    };
    if (id) {
      RestClient.put(`/users/${id}`, payload, function () {
        $("#memberModal").modal("hide");
        loadUsers();
      });
    } else {
      RestClient.post(`/users`, payload, function () {
        $("#memberModal").modal("hide");
        loadUsers();
      });
    }
  });

  function normalizeRole(viewRole) {
    const r = (viewRole || "").toLowerCase();
    if (r === "admin") return "admin";
    return "member";
  }

  loadUsers();
}
