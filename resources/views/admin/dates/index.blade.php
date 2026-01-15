@extends('layouts.admin')

@section('title','Dates')

@section('content')
    <div class="page-header d-print-none">
        <div class="container">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">Dates</h2>
                    <div class="text-muted mt-1">Manage dates — add, edit, or remove dates and assign DJs</div>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createDateModal">
                        <i class="fas fa-plus me-2"></i>Add Date
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container">

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="datesTable" class="table card-table table-hover table-vcenter">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th class="w-1">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="datesTableBody">
                                @forelse($dates as $date)
                                    <tr data-id="{{ $date->id }}">
                                        <td class="date-col">{{ $date->date->format('Y-m-d (l)') }}</td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <button type="button" class="btn btn-sm btn-outline-primary" title="Edit" onclick="editDate({{ json_encode($date) }})"><i class="fas fa-edit"></i></button>
                                                <form method="POST" action="{{ route('admin.dates.destroy', $date) }}" class="d-inline ajax-delete-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete"><i class="fas fa-trash"></i></button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center text-muted">No dates found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Date Modal -->
    <div class="modal modal-blur fade" id="createDateModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Date</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="{{ route('admin.dates.store') }}" id="createDateForm">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Date</label>
                            <input type="date" name="date" class="form-control" value="{{ old('date') }}" required>
                        </div>

                        {{-- DJ assignment removed --}}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Date</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Date Modal -->
    <div class="modal modal-blur fade" id="editDateModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Date</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" id="editDateForm">
                    @csrf
                    @method('PATCH')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Date</label>
                            <input type="date" name="date" id="editDateInput" class="form-control" required>
                        </div>

                        {{-- DJ assignment removed --}}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Date</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if ($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var el = document.getElementById('createDateModal');
                if (el) {
                    var modal = new bootstrap.Modal(el);
                    modal.show();
                }
            });
        </script>
    @endif

    <script>
        function editDate(date) {
            var form = document.getElementById('editDateForm');
            var dateInput = document.getElementById('editDateInput');

            form.action = '/admin/dates/' + date.id;
            dateInput.value = date.date;

            var modal = new bootstrap.Modal(document.getElementById('editDateModal'));
            modal.show();
        }
    </script>
    <script>
        (function(){
            var csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            function ajaxJson(url, options){
                options = options || {};
                options.headers = Object.assign({'X-CSRF-TOKEN': csrf, 'Accept': 'application/json'}, options.headers || {});
                return fetch(url, options).then(function(res){
                    if (!res.ok) return res.json().then(function(j){ throw j; });
                    return res.json();
                });
            }

            // Initialize DataTable
            var datesTableApi = null;
            if (window.jQuery && $.fn.dataTable) {
                datesTableApi = $('#datesTable').DataTable({
                    columnDefs: [{ orderable: false, targets: -1 }],
                    order: [[0, 'asc']],
                    pageLength: 10,
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
                                // append row via DataTables if available
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
                                // close modal
                                var modalEl = document.getElementById('createDateModal');
                                bootstrap.Modal.getInstance(modalEl)?.hide();
                                // reset form
                                createForm.reset();
                                window.adminToaster.show('success', data.message || 'Date created');
                            }
                        })
                        .catch(function(err){
                            var msg = (err && err.message) ? err.message : 'Request failed';
                            window.adminToaster.show('error', msg);
                        });
                });
            }

            // Edit (submit)
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
                                    if (row.length) {
                                        datesTableApi.cell(row, 0).data(data.date.date_formatted).draw(false);
                                    }
                                } else {
                                    var tr = document.querySelector('tr[data-id="' + data.date.id + '"]');
                                    if (tr){
                                        var col = tr.querySelector('.date-col');
                                        if (col) col.textContent = data.date.date_formatted;
                                    }
                                }
                                bootstrap.Modal.getInstance(document.getElementById('editDateModal'))?.hide();
                                window.adminToaster.show('success', data.message || 'Date updated');
                            }
                        })
                        .catch(function(err){
                            var msg = (err && err.message) ? err.message : 'Request failed';
                            window.adminToaster.show('error', msg);
                        });
                });
            }

            // Delete (delegated)
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
                                    var tr = form.closest('tr');
                                    if (tr && tr.parentNode) tr.parentNode.removeChild(tr);
                                }
                                window.adminToaster.show('success', data.message || 'Date deleted');
                            }
                        })
                        .catch(function(err){
                            var msg = (err && err.message) ? err.message : 'Request failed';
                            window.adminToaster.show('error', msg);
                        });
                }
            });
        })();
    </script>
@endsection
