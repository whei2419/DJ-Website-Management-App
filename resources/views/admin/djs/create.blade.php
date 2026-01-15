@extends('layouts.admin')

@section('content')
<div class="container">
    <h1 class="my-4">Add New DJ</h1>

    <form action="{{ route('admin.djs.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="name" class="form-label">DJ Name</label>
            <input type="text" name="name" id="name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="video_url" class="form-label">Video URL</label>
            <input type="url" name="video_url" id="video_url" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="slot" class="form-label">Slot</label>
            <input type="text" name="slot" id="slot" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-success">Create DJ</button>
    </form>
</div>
@endsection