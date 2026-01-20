@extends('layouts.admin')

@section('content')
    <div class="container">
        <h1 class="my-4">Add New DJ</h1>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.djs.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-3">
                <label for="name" class="form-label">DJ Name</label>
                <input type="text" name="name" id="name" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="video" class="form-label">Upload Video (required)</label>
                <input type="file" name="video" id="video" accept="video/*" class="form-control" required>
                <div class="form-text">Uploading a video is required. A preview and poster will be generated automatically
                    (FFmpeg required).</div>
            </div>

            <input type="hidden" name="date_id" id="djDateId">
            <div class="mb-3">
                <label class="form-label">Assigned Date</label>
                <div id="dateGrid" class="date-grid">
                    <div class="text-center text-muted py-3">
                        <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                        Loading available dates...
                    </div>
                </div>
                <div class="form-text">Select a date from the picker to assign this DJ.</div>
            </div>

            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="visible" name="visible" value="1" checked>
                <label class="form-check-label" for="visible">Show in gallery</label>
            </div>

            <button type="submit" class="btn btn-success">Create DJ</button>
        </form>
    </div>
@endsection
