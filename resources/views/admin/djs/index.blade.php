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
                    <a href="{{ route('admin.djs.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Create DJ
                    </a>
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
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Slot</th>
                                    <th>Created</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($djs as $dj)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $dj->name }}</td>
                                        <td>{{ $dj->slot }}</td>
                                        <td>{{ $dj->created_at->format('Y-m-d') }}</td>
                                        <td class="text-end">
                                            <a href="/admin/djs/{{ $dj->id }}/edit" class="btn btn-sm btn-light">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="/admin/djs/{{ $dj->id }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-danger" onclick="return confirm('Delete this DJ?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">No DJs found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection