@extends('layouts.admin')

@section('title','DJs')

@section('content')
    <div class="page-header d-print-none">
        <div class="container">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">DJs</h2>
                    <div class="text-muted mt-1">Manage DJs — add, edit, or remove DJs</div>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createDJModal">
                        <i class="fas fa-plus me-2"></i>Create DJ
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
                        <table class="table table-vcenter card-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Video</th>
                                    <th>Dates</th>
                                    <th class="w-1">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($djs as $dj)
                                    <tr>
                                        <td>{{ $dj->name }}</td>
                                        <td>
                                            @if($dj->video_url)
                                                <a href="{{ $dj->video_url }}" target="_blank">External Video</a>
                                            @elseif($dj->video_path)
                                                <a href="{{ Storage::url($dj->video_path) }}" target="_blank">View Video</a>
                                            @else
                                                <span class="text-muted">No video</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($dj->dates->count() > 0)
                                                @foreach($dj->dates as $date)
                                                    {{ $date->date->format('Y-m-d') }}{{ !$loop->last ? ', ' : '' }}
                                                @endforeach
                                            @else
                                                <span class="text-muted">No dates assigned</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('admin.djs.edit', $dj) }}" class="btn btn-sm btn-primary">Edit</a>
                                                <form method="POST" action="{{ route('admin.djs.destroy', $dj) }}" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">No DJs found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create DJ Modal -->
    <div class="modal modal-blur fade" id="createDJModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create DJ</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="{{ route('admin.djs.store') }}" enctype="multipart/form-data" id="createDJForm">
                    @csrf
                    <div class="modal-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Video</label>
                            <div id="videoDropZone" class="border rounded p-3 text-center" style="cursor: pointer;">
                                <input id="videoInput" type="file" name="video" accept="video/*" class="form-control d-none">
                                <div id="videoDropLabel">Drag & drop a video here, paste (Ctrl/Cmd+V), or click to select</div>
                                <div id="videoFileName" class="mt-2 text-muted"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Create DJ</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if ($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var el = document.getElementById('createDJModal');
                if (el) {
                    var modal = new bootstrap.Modal(el);
                    modal.show();
                }
            });
        </script>
    @endif
    
    <script>
        (function(){
            var zone = document.getElementById('videoDropZone');
            var input = document.getElementById('videoInput');
            var nameEl = document.getElementById('videoFileName');

            if (zone){
                zone.addEventListener('click', function(){ input.click(); });

                input.addEventListener('change', function(){
                    var f = input.files[0];
                    nameEl.textContent = f ? f.name + ' (' + Math.round(f.size/1024/1024) + 'MB)' : '';
                });

                zone.addEventListener('dragover', function(e){ e.preventDefault(); zone.classList.add('bg-light'); });
                zone.addEventListener('dragleave', function(e){ zone.classList.remove('bg-light'); });
                zone.addEventListener('drop', function(e){
                    e.preventDefault(); zone.classList.remove('bg-light');
                    var f = e.dataTransfer.files && e.dataTransfer.files[0];
                    if (f) { input.files = e.dataTransfer.files; nameEl.textContent = f.name + ' (' + Math.round(f.size/1024/1024) + 'MB)'; }
                });

                // paste support
                document.addEventListener('paste', function(e){
                    var items = e.clipboardData && e.clipboardData.items;
                    if (!items) return;
                    for (var i=0;i<items.length;i++){
                        var item = items[i];
                        if (item.kind === 'file' && item.type.indexOf('video') === 0){
                            var blob = item.getAsFile();
                            var dt = new DataTransfer();
                            dt.items.add(blob);
                            input.files = dt.files;
                            nameEl.textContent = blob.name || 'pasted-video';
                        }
                    }
                });
            }
        })();
    </script>
@endsection
