const Auth = {
  login: function (email, password, onSuccess, onError) {
    RestClient.post(
      "/auth/login",
      { email: email, password: password },
      function (response) {
        const token = response?.data?.token || response?.token;
        if (!token) {
          alert("Login failed: missing token");
          return;
        }
        localStorage.setItem("user_token", token);
        try {
          window.dispatchEvent(new Event("auth:updated"));
        } catch (e) {}
        if (onSuccess) onSuccess(response);
      },
      function (jqXHR) {
        if (onError) onError(jqXHR);
        else alert(jqXHR.responseJSON?.message || "Login failed");
      }
    );
  },
  register: function (email, password, onSuccess, onError) {
    RestClient.post(
      "/auth/register",
      { email: email, password: password },
      function (response) {
        try {
          window.dispatchEvent(new Event("auth:updated"));
        } catch (e) {}
        if (onSuccess) onSuccess(response);
      },
      function (jqXHR) {
        if (onError) onError(jqXHR);
        else alert(jqXHR.responseJSON?.message || "Register failed");
      }
    );
  },
  requireAuth: function () {
    const token = localStorage.getItem("user_token");
    if (!token) {
      location.href = "auth/login.html";
      return false;
    }
    return true;
  },
  currentUser: function () {
    const token = localStorage.getItem("user_token");
    const payload = Utils.parseJwt(token);
    return payload ? payload.user : null;
  },
  logout: function () {
    localStorage.removeItem("user_token");
    try {
      window.dispatchEvent(new Event("auth:updated"));
    } catch (e) {}
    location.href = "auth/login.html";
  },
};
