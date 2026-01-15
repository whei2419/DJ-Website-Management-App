// Admin toaster module (bundled)
(function(){
  function show(type, message, timeout = 4000){
    if(!message) return;
    var container = document.getElementById('tablerToasts');
    if(!container) return;

    var colors = {
      success: '#16a34a',
      error: '#dc2626',
      warning: '#f59e0b',
      info: '#2563eb'
    };
    var color = colors[type] || colors.info;

    var el = document.createElement('div');
    el.className = 'admin-toast mb-2 p-2 d-flex align-items-center shadow-sm rounded bg-white border';
    el.style.minWidth = '260px';
    el.setAttribute('role','alert');
    el.style.borderLeftColor = color;

    var icon = document.createElement('div');
    icon.className = 'admin-toast-icon';
    icon.innerHTML = (type === 'success') ? '<i class="fas fa-check-circle" style="color:' + color + '"></i>' :
                     (type === 'error') ? '<i class="fas fa-times-circle" style="color:' + color + '"></i>' :
                     (type === 'warning') ? '<i class="fas fa-exclamation-triangle" style="color:' + color + '"></i>' :
                     '<i class="fas fa-info-circle" style="color:' + color + '"></i>';

    var body = document.createElement('div');
    body.className = 'admin-toast-body';
    var title = document.createElement('div');
    title.className = 'title';
    title.innerText = (type === 'success') ? 'Success' : (type === 'error' ? 'Error' : (type === 'warning' ? 'Warning' : 'Info'));
    var msg = document.createElement('div');
    msg.className = 'message';
    msg.innerText = message;
    body.appendChild(title);
    body.appendChild(msg);

    var close = document.createElement('button');
    close.className = 'btn btn-link text-muted p-0 ms-3';
    close.style.border = 'none';
    close.innerHTML = '<i class="fas fa-times"></i>';
    close.addEventListener('click', function(){ if(container.contains(el)) container.removeChild(el); });

    el.appendChild(icon);
    el.appendChild(body);
    el.appendChild(close);
    container.appendChild(el);

    setTimeout(function(){ if(container.contains(el)) container.removeChild(el); }, timeout);
  }

  window.adminToaster = { show };
})();
