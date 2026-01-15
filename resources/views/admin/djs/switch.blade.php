@extends('layouts.admin')

@section('content')
<div class="container">
    <h1 class="my-4">Switch Time Slots</h1>

    <form action="{{ route('admin.djs.switch-slots') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="dj1_id" class="form-label">DJ 1</label>
            <select name="dj1_id" id="dj1_id" class="form-control" required>
                @foreach ($djs as $dj)
                    <option value="{{ $dj->id }}">{{ $dj->name }} ({{ $dj->slot }})</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="dj2_id" class="form-label">DJ 2</label>
            <select name="dj2_id" id="dj2_id" class="form-control" required>
                @foreach ($djs as $dj)
                    <option value="{{ $dj->id }}">{{ $dj->name }} ({{ $dj->slot }})</option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Switch Time Slots</button>
    </form>
</div>
@endsection