@extends('layouts.admin')

@section('content')
<div class="container">
    <h1 class="my-4">Edit DJ Details</h1>

    <form action="{{ route('admin.djs.update', $dj->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="name" class="form-label">DJ Name</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ $dj->name }}" required>
        </div>

        <div class="mb-3">
            <label for="slot" class="form-label">Time Slot</label>
            <input type="text" name="slot" id="slot" class="form-control" value="{{ $dj->slot }}" required>
        </div>

        <div class="mb-3">
            <label for="video_url" class="form-label">Video URL</label>
            <input type="url" name="video_url" id="video_url" class="form-control" value="{{ $dj->video_url }}" required>
        </div>

        <button type="submit" class="btn btn-success">Update DJ Details</button>
    </form>
</div>
@endsection