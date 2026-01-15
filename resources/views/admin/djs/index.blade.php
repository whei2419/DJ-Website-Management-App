@extends('layouts.admin')

@section('title', 'DJs')

@section('content')

    <div class="page-wrapper">
        <div class="modal" id="addEditDJModal" tabindex="-1">
        <div class="modal-dialog" role="document">
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
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-clock"></i></span>
                                <input type="date" class="form-control" id="djSlot" name="slot" value="{{ old('slot', \Carbon\Carbon::today()->format('Y-m-d')) }}" required>
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
        {{-- Content start here --}}
        <div class="container-xl">
            <div class="page-body">
                <div class="row row-deck row-cards">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-table">
                                <div class="card-header">
                                    <div class="row w-full">
                                        <div class="col">
                                            <h3 class="card-title mb-0">DJ table</h3>
                                            <p class="text-secondary m-0">This table displays all DJs.</p>
                                        </div>
                                        <div class="col-md-auto col-sm-12">
                                            <div class="ms-auto d-flex flex-wrap btn-list">
                                                <div class="input-group input-group-flat w-auto">
                                                    <span class="input-group-text">
                                                        <!-- Download SVG icon from http://tabler.io/icons/icon/search -->
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                            height="24" viewBox="0 0 24 24" fill="none"
                                                            stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                            stroke-linejoin="round" class="icon icon-1">
                                                            <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0"></path>
                                                            <path d="M21 21l-6 -6"></path>
                                                        </svg>
                                                    </span>
                                                    <input id="advanced-table-search" type="text" class="form-control"
                                                        autocomplete="off">
                                                    <span class="input-group-text">
                                                        <kbd>ctrl + K</kbd>
                                                    </span>
                                                </div>
                                                <a href="#" id="addOpen" class="btn btn-primary btn-0 d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#addEditDJModal"><i class="fas fa-plus me-2"></i> Add Dj</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="advanced-table"> 
                                    <div class="table-responsive">
                                        <table class="table table-vcenter table-selectable">
                                            <thead>
                                                <tr>
                                                    <th class="w-1">ID</th>
                                                    <th>Video Preview</th>
                                                    <th>
                                                        <button class="table-sort d-flex justify-content-between"
                                                            data-sort="sort-name">Name</button>
                                                    </th>
                                                    <th>
                                                        <button class="table-sort d-flex justify-content-between"
                                                            data-sort="sort-slot">Slot</button>
                                                    </th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody class="table-tbody">
                                                   <tr>
                                                    <td>
                                                        <input
                                                            class="form-check-input m-0 align-middle table-selectable-check"
                                                            type="checkbox" aria-label="Select invoice" value="true">
                                                    </td>
                                                    <td class="sort-name">
                                                        <span class="avatar avatar-xs me-2"
                                                            style="background-image: url(./static/avatars/008f.jpg)">
                                                        </span>
                                                        Tessie Curzon
                                                    </td>
                                                    <td class="sort-city">Hetang, China</td>
                                                    <td class="sort-status">
                                                        <span class="badge bg-danger-lt">Inactive</span>
                                                    </td>
                                                    <td class="sort-date">January 01, 2024</td>
                                                    <td class="sort-tags">
                                                        <div class="badges-list">
                                                            <span class="badge">QTA</span>
                                                            <span class="badge">Event</span>
                                                        </div>
                                                    </td>
                                                    <td class="sort-category py-0">
                                                        <span class="on-unchecked"> Agencies </span>
                                                        <div class="on-checked">
                                                            <div class="d-flex justify-content-end">
                                                                <a href="#" class="btn btn-2 btn-icon"
                                                                    aria-label="Button">
                                                                    <!-- Download SVG icon from http://tabler.io/icons/icon/dots -->
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                                        height="24" viewBox="0 0 24 24" fill="none"
                                                                        stroke="currentColor" stroke-width="2"
                                                                        stroke-linecap="round" stroke-linejoin="round"
                                                                        class="icon icon-2">
                                                                        <path d="M5 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0">
                                                                        </path>
                                                                        <path d="M12 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0">
                                                                        </path>
                                                                        <path d="M19 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0">
                                                                        </path>
                                                                    </svg>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="card-footer d-flex align-items-center">
                                        <div class="dropdown">
                                            <a class="btn dropdown-toggle" data-bs-toggle="dropdown"
                                                aria-expanded="false">
                                                <span id="page-count" class="me-1">20</span>
                                                <span>records</span>
                                            </a>
                                            <div class="dropdown-menu" style="">
                                                <a class="dropdown-item" onclick="setPageListItems(event)"
                                                    data-value="10">10 records</a>
                                                <a class="dropdown-item" onclick="setPageListItems(event)"
                                                    data-value="20">20 records</a>
                                                <a class="dropdown-item" onclick="setPageListItems(event)"
                                                    data-value="50">50 records</a>
                                                <a class="dropdown-item" onclick="setPageListItems(event)"
                                                    data-value="100">100 records</a>
                                            </div>
                                        </div>
                                        <ul class="pagination m-0 ms-auto">
                                            <li class="page-item active"><a class="page-link cursor-pointer"
                                                    data-i="1" data-page="20">1</a></li>
                                            <li class="page-item"><a class="page-link cursor-pointer" data-i="2"
                                                    data-page="20">2</a></li>
                                            <li class="page-item disabled"><a class="page-link cursor-pointer">...</a>
                                            </li>
                                            <li class="page-item"><a class="page-link cursor-pointer" data-i="7"
                                                    data-page="20">7</a></li>
                                        </ul>
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
        const djsDataRoute = "{{ route('admin.djs.list') }}";
        const saveDJRoute = "{{ route('admin.djs.store') }}";
    </script>
    @vite(['resources/js/admin-djs-custom.js'])
@endpush
