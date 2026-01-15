@extends('layouts.admin')

@section('title', 'Dates')

@section('content')
    <div class="modal" id="addDateModal" tabindex="-1">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Date</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addDateForm" action="{{ route('admin.dates.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="date" class="form-label">Event Date</label>
                            <input type="date" class="form-control" id="date" name="date" required>
                        </div>
                        </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn me-auto" data-bs-dismiss="modal">Close</button>
                    <button type="submit" form="addDateForm" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Delete Date</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="confirmDeleteMessage">Are you sure you want to delete this date? This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
                </div>
            </div>
        </div>
    </div>
    <div class="page-wrapper">
        <div class="page-header d-print-none" aria-label="Page header">
            <div class="container-xl">
                <div class="row g-2 align-items-center">
                    <div class="col">
                        <!-- Page pre-title -->
                        <div class="page-pretitle">Overview</div>
                        <h2 class="page-title">Event Dates</h2>
                    </div>
                    <div class="col col-md-auto">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addDateModal">
                            <i class="fas fa-plus me-2"></i>Add Date
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Content start here --}}
        <div class="container-xl">
            <div class="page-body">
                <div class="row row-deck row-cards">
                    <div class="col-12">
                        <div class="card px-3 py-4">
                            <div class="card-table">
                                <div id="advanced-table">
                                    <div class="table-responsive">
                                        <table id="dates-table" class="table table-vcenter table-selectable table-hover align-middle table-modern">
                                            <thead>
                                                <tr>
                                                    <th><i class="fas fa-hashtag me-2 text-muted"></i>ID</th>
                                                    <th><i class="fas fa-calendar-alt me-2 text-muted"></i>Date</th>
                                                    <th class="text-end"><i class="fas fa-ellipsis-v me-2 text-muted"></i>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($dates as $date)
                                                    <tr data-id="{{ $date->id }}">
                                                        <td>{{ $date->id }}</td>
                                                        <td><span class="badge bg-primary text-white">{{ $date->date->format('Y-m-d (l)') }}</span></td>
                                                        <td>
                                                            @include('admin.dates.partials.actions', ['date' => $date])
                                                        </td>
                                                    </tr>
                                                @endforeach
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
    <script>
        const dataTablesRoute = "{{ route('admin.dates.list') }}";
    </script>
    @vite(['resources/js/admin-dates.js'])
@endpush
