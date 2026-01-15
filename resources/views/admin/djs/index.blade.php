@extends('layouts.admin')

@section('content')
<div class="container">
    <h1 class="my-4">Manage DJs</h1>

    <a href="{{ route('admin.djs.create') }}" class="btn btn-primary mb-3">Add New DJ</a>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Video URL</th>
                <th>Slot</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($djs as $dj)
                <tr>
                    <td>{{ $dj->id }}</td>
                    <td>{{ $dj->name }}</td>
                    <td>{{ $dj->video_url }}</td>
                    <td>{{ $dj->slot }}</td>
                    <td>
                        <a href="#" class="btn btn-sm btn-warning">Edit</a>
                        <a href="#" class="btn btn-sm btn-danger">Delete</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection