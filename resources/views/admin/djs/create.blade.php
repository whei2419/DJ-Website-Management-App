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

            <div class="mb-3">
                <label for="slot" class="form-label">Slot</label>
                <input type="text" name="slot" id="slot" class="form-control">
                <input type="hidden" name="date_id" id="date_id">
                <div class="form-text">Select a date from the date picker; the system prefers `date_id` for association.
                </div>
            </div>

            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="visible" name="visible" value="1" checked>
                <label class="form-check-label" for="visible">Show in gallery</label>
            </div>

            <button type="submit" class="btn btn-success">Create DJ</button>
        </form>
    </div>
@endsection
