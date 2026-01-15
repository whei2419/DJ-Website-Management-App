@extends('layouts.admin')

@section('title', 'DJs')

@section('content')

    <div class="page-wrapper">
        <div class="page-header d-print-none" aria-label="Page header">
            <div class="container-xl">
                <div class="row g-2 align-items-center">
                    <div class="col">
                        <!-- Page pre-title -->
                        <div class="page-pretitle">Overview</div>
                        <h2 class="page-title">DJs</h2>
                    </div>
                    <div class="col col-md-auto">
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createDJModal">
                            <i class="fas fa-plus me-2"></i>Add DJ
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Content start here --}}
        <div class="container-xl">
            <div class="page-body">
                <div class="row row-deck row-cards">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-table">
                                <div id="advanced-table">
                                    <div class="table-responsive">
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
