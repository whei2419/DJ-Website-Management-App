@extends('layouts.site')

@section('title', 'Home')

@section('content')

    @include('website.sections.hero-section')
    @include('website.sections.section-white')
    @include('website.sections.brand-info ')
    @include('website.sections.cards')
    @include('website.partials.footer')

@endsection
