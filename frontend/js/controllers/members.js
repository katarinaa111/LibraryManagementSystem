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

  const validator = $("#memberForm").validate({
    rules: {
      name: {
        required: true,
        minlength: 2,
      },
      email: {
        required: true,
        email: true,
      },
      role: "required",
      join_date: "required",
    },
    messages: {
      name: {
        required: "Please enter member name",
        minlength: "Name must be at least 2 characters",
      },
      email: "Please enter a valid email address",
      role: "Please select a role",
      join_date: "Please select a join date",
    },
    submitHandler: function (form) {
      const id = $("#memberId").val();
      const payload = {
        username: $("#memberName").val(),
        email: $("#memberEmail").val(),
        role: normalizeRole($("#memberRole").val()),
        joinDate: $("#memberJoinDate").val(),
      };

      $.blockUI({ message: '<h3>Processing...</h3>' });

      const success = function () {
        $.unblockUI();
        $("#memberModal").modal("hide");
        loadUsers();
      };

      const error = function (err) {
        $.unblockUI();
        alert(err?.data?.message || "Error saving member");
      };

      if (id) {
        RestClient.put(`/users/${id}`, payload, success, error);
      } else {
        RestClient.post(`/users`, payload, success, error);
      }
    },
  });

  // Reset validation when modal is hidden
  $("#memberModal").on("hidden.bs.modal", function () {
    validator.resetForm();
    $(".error").removeClass("error");
  });

  function normalizeRole(viewRole) {
    const r = (viewRole || "").toLowerCase();
    if (r === "admin") return "admin";
    return "member";
  }

  loadUsers();
}
