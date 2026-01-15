// Admin toaster module (bundled)
// Admin toaster module (Tabler/Bootstrap toast markup)
(function(){
  function show(type, message, options = {}){
    if(!message) return;
    var container = document.getElementById('tablerToasts');
    if(!container) return;

    var colors = {
      success: '#16a34a',
      error: '#dc2626',
      warning: '#f59e0b',
      info: '#2563eb'
    };
    var titleText = (type === 'success') ? 'Success' : (type === 'error' ? 'Error' : (type === 'warning' ? 'Warning' : 'Info'));
    var color = colors[type] || colors.info;
    var avatarUrl = options.avatar || null;
    var title = options.title || titleText;
    var timestamp = options.time || 'Just now';
    var autohide = typeof options.autohide === 'undefined' ? (options.timeout ? true : false) : options.autohide;
    var timeout = options.timeout || 4000;

    var toast = document.createElement('div');
    toast.className = 'toast';
    toast.setAttribute('role','alert');
    toast.setAttribute('aria-live','assertive');
    toast.setAttribute('aria-atomic','true');
    toast.setAttribute('data-bs-autohide', autohide ? 'true' : 'false');
    toast.setAttribute('data-bs-toggle','toast');
    toast.classList.add('mb-2');

    var header = document.createElement('div');
    header.className = 'toast-header d-flex align-items-center';

    if(avatarUrl){
      var span = document.createElement('span');
      span.className = 'avatar avatar-xs me-2';
      span.style.backgroundImage = 'url(' + avatarUrl + ')';
      header.appendChild(span);
    } else {
      var span = document.createElement('span');
      span.className = 'avatar avatar-xs me-2';
      span.style.backgroundColor = color;
      span.innerHTML = '<i class="fas fa-bell" style="color:#fff;font-size:10px"></i>';
      header.appendChild(span);
    }

    var strong = document.createElement('strong');
    strong.className = 'me-auto';
    strong.innerText = title;

    var small = document.createElement('small');
    small.innerText = timestamp;

    var btn = document.createElement('button');
    btn.type = 'button';
    btn.className = 'ms-2 btn-close';
    btn.setAttribute('data-bs-dismiss','toast');
    btn.setAttribute('aria-label','Close');

    header.appendChild(strong);
    header.appendChild(small);
    header.appendChild(btn);

    var body = document.createElement('div');
    body.className = 'toast-body';
    body.innerText = message;

    toast.appendChild(header);
    toast.appendChild(body);

    container.appendChild(toast);

    // Use Bootstrap Toast if available, otherwise fallback to simple show/remove
    if(window.bootstrap && window.bootstrap.Toast){
      var bsToast = new bootstrap.Toast(toast, { autohide: autohide, delay: timeout });
      bsToast.show();
      // ensure element is removed when hidden
      toast.addEventListener('hidden.bs.toast', function(){ if(container.contains(toast)) container.removeChild(toast); });
    } else {
      toast.classList.add('show');
      if(autohide){
        setTimeout(function(){ if(container.contains(toast)) container.removeChild(toast); }, timeout);
      }
      btn.addEventListener('click', function(){ if(container.contains(toast)) container.removeChild(toast); });
    }
  }

  window.adminToaster = { show };
})();
