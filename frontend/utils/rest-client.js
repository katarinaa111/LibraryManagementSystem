let RestClient = {
  _axios: null,
  _initAxios: function () {
    if (typeof axios === 'undefined' || RestClient._axios) return;
    RestClient._axios = axios.create({ baseURL: Constants.PROJECT_BASE_URL });
    RestClient._axios.interceptors.request.use(function (config) {
      const token = localStorage.getItem('user_token');
      if (token) config.headers['Authentication'] = token;
      const payload = Utils.parseJwt(token);
      if (payload && payload.exp && (Date.now() / 1000) >= payload.exp) {
        localStorage.removeItem('user_token');
        if (typeof Auth !== 'undefined') Auth.logout();
        return Promise.reject({ response: { status: 401 } });
      }
      return config;
    });
    RestClient._axios.interceptors.response.use(function (resp) { return resp; }, function (error) {
      const status = error?.response?.status;
      if (status === 401 || status === 403) {
        try { if (typeof Auth !== 'undefined') Auth.logout(); } catch (e) {}
      }
      return Promise.reject(error);
    });
  },
  get: function (url, callback, error_callback) {
    RestClient._initAxios();
    if (RestClient._axios) {
      RestClient._axios.get(url)
        .then(function (resp) { if (callback) callback(resp.data); })
        .catch(function (err) {
          if (error_callback) error_callback(err.response);
          else alert(err?.response?.data?.message || 'Request failed');
        });
      return;
    }
    $.ajax({
      url: Constants.PROJECT_BASE_URL + url,
      type: "GET",
      beforeSend: function (xhr) {
        xhr.setRequestHeader(
          "Authentication",
          localStorage.getItem("user_token")
        );
      },
      success: function (response) {
        if (callback) callback(response);
      },
      error: function (jqXHR) {
        if (jqXHR && (jqXHR.status === 401 || jqXHR.status === 403)) {
          try { if (typeof Auth !== 'undefined') Auth.logout(); } catch (e) {}
        }
        if (error_callback) {
          error_callback(jqXHR);
        } else {
          try {
            alert(jqXHR.responseJSON?.message || "Request failed");
          } catch (e) {
            alert("Request failed");
          }
        }
      },
    });
  },
  request: function (url, method, data, callback, error_callback) {
    RestClient._initAxios();
    if (RestClient._axios) {
      RestClient._axios({ url: url, method: method, data: data || null })
        .then(function (resp) { if (callback) callback(resp.data); })
        .catch(function (err) {
          const jq = err.response;
          if (jq && (jq.status === 401 || jq.status === 403)) {
            try { if (typeof Auth !== 'undefined') Auth.logout(); } catch (e) {}
          }
          if (error_callback) error_callback(jq);
          else alert(jq?.data?.message || 'Request failed');
        });
      return;
    }
    $.ajax({
      url: Constants.PROJECT_BASE_URL + url,
      type: method,
      beforeSend: function (xhr) {
        xhr.setRequestHeader(
          "Authentication",
          localStorage.getItem("user_token")
        );
      },
      data: data ? JSON.stringify(data) : null,
      contentType: "application/json",
      processData: false,
    })
      .done(function (response) {
        if (callback) callback(response);
      })
      .fail(function (jqXHR) {
        if (jqXHR && (jqXHR.status === 401 || jqXHR.status === 403)) {
          try { if (typeof Auth !== 'undefined') Auth.logout(); } catch (e) {}
        }
        if (error_callback) {
          error_callback(jqXHR);
        } else {
          try {
            alert(jqXHR.responseJSON?.message || "Request failed");
          } catch (e) {
            alert("Request failed");
          }
        }
      });
  },
  post: function (url, data, callback, error_callback) {
    RestClient.request(url, "POST", data, callback, error_callback);
  },
  delete: function (url, data, callback, error_callback) {
    RestClient.request(url, "DELETE", data, callback, error_callback);
  },
  patch: function (url, data, callback, error_callback) {
    RestClient.request(url, "PATCH", data, callback, error_callback);
  },
  put: function (url, data, callback, error_callback) {
    RestClient.request(url, "PUT", data, callback, error_callback);
  },
};
