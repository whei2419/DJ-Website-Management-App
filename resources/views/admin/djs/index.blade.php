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
                            <input type="hidden" id="djDateId" name="date_id">
                            <div class="mb-3">
                                <label class="form-label">Assigned Date</label>
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
                                    <input type="file" class="form-control" id="djVideo" name="video"
                                        accept="video/*" required>
                                </div>
                                <small class="error-message"></small>

                                <!-- Inline progress UI (hidden until upload starts) -->
                                <div id="uploadInlineProgress" class="mb-3 d-none">
                                    <div class="progress">
                                        <div id="uploadInlineProgressBar" class="progress-bar" role="progressbar"
                                            style="width: 0%;">0%</div>
                                    </div>
                                    <div id="uploadInlineProgressText" class="text-muted small mt-1">Preparing upload...
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="djVisible" name="visible" value="1"
                                    checked>
                                <label class="form-check-label" for="djVisible">Show in gallery</label>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn me-auto" data-bs-dismiss="modal">Close</button>
                        <button id="saveDJButton" class="btn btn-primary"><i class="fas fa-save me-2"></i>Save
                            changes</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Inline upload progress (shown inside add/edit modal) -->

        <!-- Delete Confirmation Modal -->
        <div class="modal modal-blur fade" id="deleteDJModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
                <div class="modal-content">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <div class="modal-status bg-danger"></div>
                    <div class="modal-body text-center py-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon mb-2 text-danger icon-lg" width="24"
                            height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M12 9v4" />
                            <path
                                d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.87l-8.106 -13.536a1.914 1.914 0 0 0 -3.274 0z" />
                            <path d="M12 16h.01" />
                        </svg>
                        <h3>Are you sure?</h3>
                        <div class="text-secondary">Do you really want to delete this DJ? This action cannot be undone.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="mb-3">
                            <label for="video" class="form-label">Video Preview</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-video"></i></span>
                                <!-- Uppy uploader placeholder -->
                                <div id="uploader" style="width:100%;"></div>
                            </div>
                            <small class="error-message"></small>

                            <!-- Inline progress UI (hidden until upload starts) -->
                            <div id="uploadInlineProgress" class="mb-3 d-none">
                                <div class="progress">
                                    <div id="uploadInlineProgressBar" class="progress-bar" role="progressbar"
                                        style="width: 0%;">0%</div>
                                </div>
                                <div id="uploadInlineProgressText" class="text-muted small mt-1">Preparing upload...</div>
                            </div>
                        </div>
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
                                                                    <select id="djsTableLength"
                                                                        class="form-select form-select-sm"
                                                                        style="width: auto;">
                                                                        <option value="10">10</option>
                                                                        <option value="20" selected>20</option>
                                                                        <option value="50">50</option>
                                                                        <option value="100">100</option>
                                                                    </select>
                                                                    <span class="ms-2">entries</span>
                                                                </div>
                                                                <input type="text" id="djsTableSearch"
                                                                    class="form-control form-control-sm"
                                                                    placeholder="Search DJs..." style="width: 250px;">
                                                                <a href="#" id="addOpen"
                                                                    class="btn btn-primary btn-sm d-flex align-items-center"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#addEditDJModal"><i
                                                                        class="fas fa-plus me-2"></i> Add DJ</a>
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
                                                                    <th>Date</th>
                                                                    <th>Visible</th>
                                                                    <th>Action</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody class="table-tbody">
                                                                <tr>
                                                                    <td colspan="6" class="text-center py-4">
                                                                        <div class="spinner-border spinner-border-sm me-2"
                                                                            role="status">
                                                                        </div>
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
                    <!-- Uppy (client-side uploader) -->
                    <link href="https://releases.transloadit.com/uppy/v3.8.0/uppy.min.css" rel="stylesheet" />
                    <script src="https://releases.transloadit.com/uppy/v3.8.0/uppy.min.js"></script>
                    <script src="https://cdn.jsdelivr.net/npm/hls.js@1"></script>
                    <script>
                        // Initialize Uppy dashboard and store instance in window.uppy
                        const initUppy = () => {
                            if (window.uppy) return window.uppy;
                            const uppy = new Uppy.Core({
                                autoProceed: false,
                                restrictions: {
                                    maxFileSize: 1000 * 1024 * 1024,
                                    allowedFileTypes: ['video/*']
                                }
                            });

                            uppy.use(Uppy.Dashboard, {
                                inline: true,
                                target: '#uploader',
                                showProgressDetails: true
                            });

                            // Do not attach an uploader plugin; we'll use our own chunked function
                            window.uppy = uppy;
                            return uppy;
                        };
                        document.addEventListener('DOMContentLoaded', initUppy);

                        // Chunked upload helper
                        async function uploadFileInChunks(file, onProgress = null) {
                            const chunkSize = 5 * 1024 * 1024; // 5MB
                            const totalChunks = Math.ceil(file.size / chunkSize);
                            const uploadId = crypto.randomUUID();

                            for (let i = 0; i < totalChunks; i++) {
                                const start = i * chunkSize;
                                const end = Math.min(file.size, start + chunkSize);
                                const chunk = file.slice(start, end);
                                const form = new FormData();
                                form.append('upload_id', uploadId);
                                form.append('chunk_index', i + 1);
                                form.append('chunk', chunk);

                                let ok = false;
                                let retries = 0;
                                while (!ok && retries < 3) {
                                    const resp = await fetch('{{ route('upload.chunk') }}', {
                                        method: 'POST',
                                        body: form,
                                        headers: {
                                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute(
                                                'content')
                                        }
                                    });
                                    if (resp.ok) ok = true;
                                    else retries++;
                                }

                                if (!ok) throw new Error('Failed to upload chunk ' + (i + 1));

                                if (onProgress) onProgress(Math.round(((i + 1) / totalChunks) * 100));
                            }

                            // Ask server to assemble
                            const completeResp = await fetch('{{ route('upload.complete') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content')
                                },
                                body: JSON.stringify({
                                    upload_id: uploadId,
                                    total_chunks: totalChunks,
                                    filename: file.name
                                })
                            });
                            if (!completeResp.ok) throw new Error('Failed to complete upload');
                            return await completeResp.json();
                        }

                        // Form submission and upload are handled centrally in `resources/js/admin-djs-datatables.js`.
                    </script>
                    @vite(['resources/js/admin-djs-datatables.js'])
                @endpush
