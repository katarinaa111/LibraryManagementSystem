let Constants = {
    PROJECT_BASE_URL:
      location.hostname == "localhost"
        ? "http://localhost/KatarinaSoja/LibraryManagementSystem/backend"
        : "https://add-production-server-after-deployment/backend",
    USER_ROLE: "user",
    ADMIN_ROLE: "admin",
};
