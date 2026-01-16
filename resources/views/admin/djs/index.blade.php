@extends('layouts.admin')

@section('title', 'DJs')

@section('content')

    <div class="page-wrapper">
        <div class="modal" id="addEditDJModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 id="DjTitle" class="modal-title"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addDJForm" action="" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                <input type="text" class="form-control" id="djName" name="name" required>
                            </div>
                            <small class="error-message"></small>
                        </div>
                        <div class="mb-3">
                            <label for="slot" class="form-label">Time Slot</label>
                            <input type="hidden" id="djSlot" name="slot" required>
                            <div id="dateGrid" class="date-grid">
                                <div class="text-center text-muted py-3">
                                    <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                                    Loading available dates...
                                </div>
                            </div>
                            <small class="error-message"></small>
                        </div>
                        <div class="mb-3">
                            <label for="video" class="form-label">Video Preview</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-video"></i></span>
                                <input type="file" class="form-control" id="djVideo" name="video" accept="video/*" required>
                            </div>
                            <small class="error-message"></small>
                        </div>
                        </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn me-auto" data-bs-dismiss="modal">Close</button>
                    <button id="saveDJButton" class="btn btn-primary"><i class="fas fa-save me-2"></i>Save changes</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal modal-blur fade" id="deleteDJModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
            <div class="modal-content">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="modal-status bg-danger"></div>
                <div class="modal-body text-center py-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon mb-2 text-danger icon-lg" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 9v4" /><path d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.87l-8.106 -13.536a1.914 1.914 0 0 0 -3.274 0z" /><path d="M12 16h.01" /></svg>
                    <h3>Are you sure?</h3>
                    <div class="text-secondary">Do you really want to delete this DJ? This action cannot be undone.</div>
                </div>
                <div class="modal-footer">
                    <div class="w-100">
                        <div class="row">
                            <div class="col">
                                <button type="button" class="btn w-100" data-bs-dismiss="modal">Cancel</button>
                            </div>
                            <div class="col">
                                <button type="button" id="confirmDeleteBtn" class="btn btn-danger w-100">Delete DJ</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

        {{-- Content start here --}}
        <div class="container-xl">
            <div class="page-body">
                <div class="row row-deck row-cards w-100">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-table">
                                <div class="card-header">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <h3 class="card-title mb-0">DJ table</h3>
                                        </div>
                                        <div class="col-auto ms-auto">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="d-flex align-items-center">
                                                    <span class="me-2">Show</span>
                                                    <select id="djsTableLength" class="form-select form-select-sm" style="width: auto;">
                                                        <option value="10">10</option>
                                                        <option value="20" selected>20</option>
                                                        <option value="50">50</option>
                                                        <option value="100">100</option>
                                                    </select>
                                                    <span class="ms-2">entries</span>
                                                </div>
                                                <input type="text" id="djsTableSearch" class="form-control form-control-sm" placeholder="Search DJs..." style="width: 250px;">
                                                <a href="#" id="addOpen" class="btn btn-primary btn-sm d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#addEditDJModal"><i class="fas fa-plus me-2"></i> Add DJ</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="advanced-table"> 
                                    <div class="table-responsive">
                                        <table id="djsTable" class="table table-vcenter">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Video Preview</th>
                                                    <th>Name</th>
                                                    <th>Slot</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody class="table-tbody">
                                                <tr>
                                                    <td colspan="5" class="text-center py-4">
                                                        <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                                                        <span class="text-muted">Loading DJs...</span>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<!-- DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script>
    const djsDataRoute = "{{ route('admin.djs.list') }}";
    const saveDJRoute = "{{ route('admin.djs.store') }}";
    const availableDatesRoute = "{{ route('admin.djs.available-dates') }}";
</script>
@vite(['resources/sass/admin/_tables.scss', 'resources/js/admin-djs-datatables.js'])
@endpush
