// Admin dates page JS
// Handles DataTables initialization and AJAX CRUD for /admin/dates

(function(){
  function init(){
    var csrfMeta = document.querySelector('meta[name="csrf-token"]');
    var csrf = csrfMeta ? csrfMeta.getAttribute('content') : '';

    function ajaxJson(url, options){
      options = options || {};
      options.headers = Object.assign({'X-CSRF-TOKEN': csrf, 'Accept': 'application/json'}, options.headers || {});
      return fetch(url, options).then(function(res){
        if (!res.ok) return res.json().then(function(j){ throw j; });
        return res.json();
      });
    }

    // Expose editDate globally for onclick handlers in the table
    window.editDate = function(date){
      var form = document.getElementById('editDateForm');
      var dateInput = document.getElementById('editDateInput');
      if (!form || !dateInput) return;
      form.action = '/admin/dates/' + date.id;
      dateInput.value = date.date;
      var modal = new bootstrap.Modal(document.getElementById('editDateModal'));
      modal.show();
    };

    // Initialize DataTable if available
    var datesTableApi = null;
    if (window.jQuery && $.fn.dataTable) {
      datesTableApi = $('#datesTable').DataTable({
        responsive: true,
        autoWidth: false,
        dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
             "rt" +
             "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
        columnDefs: [ { orderable: false, targets: -1, width: '120px' } ],
        order: [[0, 'asc']],
        pageLength: 10,
        language: { search: '', searchPlaceholder: 'Search dates...' }
      });
      window.datesTableApi = datesTableApi;
    }

    // Create
    var createForm = document.getElementById('createDateForm');
    if (createForm){
      createForm.addEventListener('submit', function(e){
        e.preventDefault();
        var fd = new FormData(createForm);
        ajaxJson(createForm.action, { method: 'POST', body: fd })
          .then(function(data){
            if (data.success){
              var actionsHtml = '<div class="d-flex gap-2">' +
                '<button type="button" class="btn btn-sm btn-outline-primary" title="Edit" onclick=\'editDate(' + JSON.stringify(data.date) + ')\'><i class="fas fa-edit"></i></button>' +
                '<form method="POST" action="/admin/dates/' + data.date.id + '" class="d-inline ajax-delete-form">' +
                '<input type="hidden" name="_token" value="' + csrf + '">' +
                '<input type="hidden" name="_method" value="DELETE">' +
                '<button type="submit" class="btn btn-sm btn-outline-danger" title="Delete"><i class="fas fa-trash"></i></button>' +
                '</form></div>';

              if (datesTableApi) {
                var rowNode = datesTableApi.row.add([data.date.date_formatted, actionsHtml]).draw(false).node();
                $(rowNode).attr('data-id', data.date.id);
              } else {
                var tbody = document.getElementById('datesTableBody');
                var tr = document.createElement('tr');
                tr.dataset.id = data.date.id;
                tr.innerHTML = '<td class="date-col">' + data.date.date_formatted + '</td>' + '<td>' + actionsHtml + '</td>';
                if (tbody.firstChild) tbody.insertBefore(tr, tbody.firstChild);
                else tbody.appendChild(tr);
              }

              var modalEl = document.getElementById('createDateModal');
              bootstrap.Modal.getInstance(modalEl)?.hide();
              createForm.reset();
              (window.adminToaster || { show: function(_, m){ alert(m);} }).show('success', data.message || 'Date created');
            }
          })
          .catch(function(err){
            var msg = (err && err.message) ? err.message : 'Request failed';
            (window.adminToaster || { show: function(_, m){ alert(m);} }).show('error', msg);
          });
      });
    }

    // Edit submit
    var editForm = document.getElementById('editDateForm');
    if (editForm){
      editForm.addEventListener('submit', function(e){
        e.preventDefault();
        var fd = new FormData(editForm);
        var url = editForm.action;
        ajaxJson(url, { method: 'POST', body: fd })
          .then(function(data){
            if (data.success){
              if (datesTableApi) {
                var row = $('#datesTable').find('tr[data-id="' + data.date.id + '"]');
                if (row.length) { datesTableApi.cell(row, 0).data(data.date.date_formatted).draw(false); }
              } else {
                var tr = document.querySelector('tr[data-id="' + data.date.id + '"]');
                if (tr){ var col = tr.querySelector('.date-col'); if (col) col.textContent = data.date.date_formatted; }
              }
              bootstrap.Modal.getInstance(document.getElementById('editDateModal'))?.hide();
              (window.adminToaster || { show: function(_, m){ alert(m);} }).show('success', data.message || 'Date updated');
            }
          })
          .catch(function(err){
            var msg = (err && err.message) ? err.message : 'Request failed';
            (window.adminToaster || { show: function(_, m){ alert(m);} }).show('error', msg);
          });
      });
    }

    // Delete delegated
    document.addEventListener('submit', function(e){
      var form = e.target;
      if (form.classList && form.classList.contains('ajax-delete-form')){
        e.preventDefault();
        if (!confirm('Are you sure?')) return;
        var url = form.action;
        ajaxJson(url, { method: 'DELETE' })
          .then(function(data){
            if (data.success){
              if (datesTableApi) {
                var row = $('#datesTable').find('tr[data-id="' + form.action.split('/').pop() + '"]');
                if (row.length) { datesTableApi.row(row).remove().draw(false); }
              } else {
                var tr = form.closest('tr'); if (tr && tr.parentNode) tr.parentNode.removeChild(tr);
              }
              (window.adminToaster || { show: function(_, m){ alert(m);} }).show('success', data.message || 'Date deleted');
            }
          })
          .catch(function(err){
            var msg = (err && err.message) ? err.message : 'Request failed';
            (window.adminToaster || { show: function(_, m){ alert(m);} }).show('error', msg);
          });
      }
    });
  }

  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
  else init();

})();
