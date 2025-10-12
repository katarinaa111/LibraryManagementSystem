function renderMembers() {
  let members = [
    {
      id: 1,
      name: "Alice Johnson",
      email: "alice@example.com",
      role: "Student",
      joinDate: "2024-03-15",
    },
    {
      id: 2,
      name: "Mark Smith",
      email: "mark@example.com",
      role: "Librarian",
      joinDate: "2023-12-02",
    },
  ];

  // Render Members Table
  const renderMembers = (list) => {
    let rows = "";
    list.forEach((m) => {
      rows += `
        <tr>
          <td>${m.name}</td>
          <td>${m.email}</td>
          <td>${m.role}</td>
          <td>${m.joinDate}</td>
          <td>
            <button class="btn btn-sm btn-warning editMember" data-id="${m.id}">Edit</button>
            <button class="btn btn-sm btn-danger deleteMember" data-id="${m.id}">Delete</button>
          </td>
        </tr>`;
    });
    $("#membersTableBody").html(rows);
  };

  // Initial render
  renderMembers(members);

  // Add Member button
  $("#addMemberBtn").click(function () {
    $("#memberForm")[0].reset();
    $("#memberId").val("");
    $("#memberModalLabel").text("Add Member");
    $("#memberModal").modal("show");
  });

  // Edit Member
  $(document).on("click", ".editMember", function () {
    let id = $(this).data("id");
    let m = members.find((x) => x.id == id);
    $("#memberId").val(m.id);
    $("#memberName").val(m.name);
    $("#memberEmail").val(m.email);
    $("#memberRole").val(m.role);
    $("#memberJoinDate").val(m.joinDate);
    $("#memberModalLabel").text("Edit Member");
    $("#memberModal").modal("show");
  });

  // Delete Member
  $(document).on("click", ".deleteMember", function () {
    if (confirm("Are you sure you want to delete this member?")) {
      let id = $(this).data("id");
      members = members.filter((m) => m.id != id);
      renderMembers(members);
    }
  });

  // Save Member
  $("#memberForm").submit(function (e) {
    e.preventDefault();
    let id = $("#memberId").val();
    let newMember = {
      id: id ? parseInt(id) : Date.now(),
      name: $("#memberName").val(),
      email: $("#memberEmail").val(),
      role: $("#memberRole").val(),
      joinDate: $("#memberJoinDate").val(),
    };

    if (id) {
      members = members.map((m) => (m.id == id ? newMember : m));
    } else {
      members.push(newMember);
    }

    $("#memberModal").modal("hide");
    renderMembers(members);
  });
}
