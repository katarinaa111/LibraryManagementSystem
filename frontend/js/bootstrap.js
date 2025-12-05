$(function(){
  var token = localStorage.getItem('user_token');
  if (!token) {
    location.href = 'auth/login.html';
    return;
  }
  var payload = Utils.parseJwt(token);
  if (!payload || (payload.exp && (Date.now()/1000) >= payload.exp)) {
    localStorage.removeItem('user_token');
    location.href = 'auth/login.html';
    return;
  }
  if (typeof Menu !== 'undefined') Menu.render();
  setInterval(function(){
    var t = localStorage.getItem('user_token');
    if (!t) { location.href = 'auth/login.html'; return; }
    var p = Utils.parseJwt(t);
    if (p && p.exp && (Date.now()/1000) >= p.exp) {
      localStorage.removeItem('user_token');
      location.href = 'auth/login.html';
    }
  }, 60000);
});
