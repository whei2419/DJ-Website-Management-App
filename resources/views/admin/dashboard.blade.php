@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')

    <div class="page-wrapper">
        <div class="page-header d-print-none" aria-label="Page header">
            <div class="container-xl">
                <div class="row g-2 align-items-center">
                    <div class="col">
                        <!-- Page pre-title -->
                        <div class="page-pretitle">Overview</div>
                        <h2 class="page-title">Dashboard</h2>
                    </div>
                </div>
            </div>
        </div>

        {{-- Content start here --}}
        <div class="container-xl">
            <div class="page-body">
                <div class="row row-deck row-cards">
                    <div class="col-sm-12 col-lg-6">
                        <div class="card">
                            <div class="card-body">
                                
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-lg-6"></div>

                </div>
            </div>
        </div>
    </div>

@endsection
