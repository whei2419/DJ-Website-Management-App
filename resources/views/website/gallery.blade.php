@extends('layouts.site')

@section('title', 'Gallery')

@push('scripts')
    @vite(['resources/js/gallery.js'])
@endpush

@section('content')
    @include('website.sections.galery')
@endsection
