@extends('layouts.admin')

@section('title', 'Dates')

@section('content')

    <div class="page-wrapper">
        <div class="modal modal-blur fade" id="addDateModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 id="dateTitle" class="modal-title">Add Date</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="addDateForm" action="{{ route('admin.dates.store') }}" method="POST">
                            @csrf
                            <input type="hidden" id="dateId" name="id">
                            <div class="mb-3">
                                <label for="date" class="form-label">Event Date</label>
                                <input type="date" class="form-control" id="date" name="date" required>
                                <small class="error-message"></small>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn me-auto" data-bs-dismiss="modal">Close</button>
                        <button id="saveDateButton" class="btn btn-primary"><i class="fas fa-save me-2"></i>Save changes</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div class="modal modal-blur fade" id="deleteDateModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
                <div class="modal-content">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <div class="modal-status bg-danger"></div>
                    <div class="modal-body text-center py-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon mb-2 text-danger icon-lg" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 9v4" /><path d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.87l-8.106 -13.536a1.914 1.914 0 0 0 -3.274 0z" /><path d="M12 16h.01" /></svg>
                        <h3>Are you sure?</h3>
                        <div class="text-secondary">Do you really want to delete this date? This action cannot be undone.</div>
                    </div>
                    <div class="modal-footer">
                        <div class="w-100">
                            <div class="row">
                                <div class="col">
                                    <button type="button" class="btn w-100" data-bs-dismiss="modal">Cancel</button>
                                </div>
                                <div class="col">
                                    <button type="button" id="confirmDeleteBtn" class="btn btn-danger w-100">Delete Date</button>
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
                <div class="row row-deck row-cards">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-table">
                                <div class="card-header">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h3 class="card-title mb-0">Event Dates</h3>
                                            <p class="text-secondary m-0">Manage your event dates.</p>
                                        </div>
                                        <div>
                                            <a href="#" id="addOpen" class="btn btn-primary d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#addDateModal"><i class="fas fa-plus me-2"></i> Add Date</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table id="datesTable" class="table table-vcenter">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Date</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody class="table-tbody">
                                            <tr>
                                                <td colspan="3" class="text-center py-4">
                                                    <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                                                    <span class="text-muted">Loading dates...</span>
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
@endsection

@push('scripts')
<!-- DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script>
    const datesDataRoute = "{{ route('admin.dates.list') }}";
    const saveDateRoute = "{{ route('admin.dates.store') }}";
</script>
@vite(['resources/js/admin-dates-datatables.js'])
@endpush
