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
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-calendar-event me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><rect x="4" y="5" width="16" height="16" rx="2" /><line x1="16" y1="3" x2="16" y2="7" /><line x1="8" y1="3" x2="8" y2="7" /><line x1="4" y1="11" x2="20" y2="11" /><rect x="8" y="15" width="2" height="2" /></svg>
                                    Upcoming Events
                                </h3>
                                <div class="card-actions">
                                    <a href="{{ route('admin.dates.index') }}" class="btn btn-sm btn-primary">
                                        View All
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                @if($dates->isEmpty())
                                    <div class="empty">
                                        <div class="empty-icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><rect x="4" y="5" width="16" height="16" rx="2" /><line x1="16" y1="3" x2="16" y2="7" /><line x1="8" y1="3" x2="8" y2="7" /><line x1="4" y1="11" x2="20" y2="11" /></svg>
                                        </div>
                                        <p class="empty-title">No dates scheduled</p>
                                        <p class="empty-subtitle text-muted">
                                            Get started by adding your first event date.
                                        </p>
                                        <div class="empty-action">
                                            <a href="{{ route('admin.dates.index') }}" class="btn btn-primary">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19" /><line x1="5" y1="12" x2="19" y2="12" /></svg>
                                                Add Date
                                            </a>
                                        </div>
                                    </div>
                                @else
                                    <div class="row g-2">
                                        @foreach($dates as $date)
                                            <div class="col-3 col-md-2">
                                                <a href="{{ route('admin.djs.index') }}?date={{ $date['date']->format('Y-m-d') }}" class="text-decoration-none">
                                                    <div class="card card-sm" style="aspect-ratio: 1; transition: all 0.2s;">
                                                        <div class="card-body d-flex flex-column align-items-center justify-content-center text-center p-2 position-relative" style="height: 100%;">
                                                            <span class="badge bg-blue position-absolute" style="top: 4px; left: 4px; font-size: 0.65rem; padding: 2px 6px;">
                                                                {{ $date['dj_count'] }}
                                                            </span>
                                                            <div class="text-uppercase text-muted small fw-bold" style="font-size: 0.6rem;">{{ $date['month'] }}</div>
                                                            <div class="h2 m-0 my-1">{{ $date['day'] }}</div>
                                                            <div class="text-muted" style="font-size: 0.65rem;">{{ $date['day_name'] }}</div>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="row row-cards">
                            <div class="col-6">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="me-3">
                                                <span class="avatar avatar-sm" style="background-color: #206bc4;">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon text-white" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="9" cy="7" r="4" /><path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" /><path d="M16 3.13a4 4 0 0 1 0 7.75" /><path d="M21 21v-2a4 4 0 0 0 -3 -3.85" /></svg>
                                                </span>
                                            </div>
                                            <div class="subheader">Total DJs</div>
                                        </div>
                                        <div class="h1 mb-0">{{ $totalDJs }}</div>
                                        <div class="text-muted mt-1">
                                            <a href="{{ route('admin.djs.index') }}" class="text-decoration-none">View all →</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="me-3">
                                                <span class="avatar avatar-sm" style="background-color: #d63939;">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon text-white" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><rect x="4" y="5" width="16" height="16" rx="2" /><line x1="16" y1="3" x2="16" y2="7" /><line x1="8" y1="3" x2="8" y2="7" /><line x1="4" y1="11" x2="20" y2="11" /></svg>
                                                </span>
                                            </div>
                                            <div class="subheader">Total Dates</div>
                                        </div>
                                        <div class="h1 mb-0">{{ $totalDates }}</div>
                                        <div class="text-muted mt-1">
                                            <a href="{{ route('admin.dates.index') }}" class="text-decoration-none">View all →</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-clock me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="12" cy="12" r="9" /><polyline points="12 7 12 12 15 15" /></svg>
                                            Recently Added DJs
                                        </h3>
                                        <div class="card-actions">
                                            <a href="{{ route('admin.djs.index') }}" class="btn btn-sm btn-primary">
                                                View All
                                            </a>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        @if($recentDJs->isEmpty())
                                            <div class="empty">
                                                <p class="empty-title">No DJs yet</p>
                                                <p class="empty-subtitle text-muted">Start adding DJs to see them here</p>
                                            </div>
                                        @else
                                            <div class="table-responsive">
                                                <table class="table table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th>Name</th>
                                                            <th>Date Slot</th>
                                                            <th>Added</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($recentDJs as $dj)
                                                            <tr>
                                                                <td>
                                                                    <div class="d-flex align-items-center">
                                                                        <span class="avatar avatar-xs me-2" style="background-image: url(https://ui-avatars.com/api/?name={{ urlencode($dj->name) }}&background=random&size=128)"></span>
                                                                        {{ $dj->name }}
                                                                    </div>
                                                                </td>
                                                                <td>{{ $dj->slot ?? '-' }}</td>
                                                                <td class="text-muted">{{ $dj->created_at->diffForHumans() }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @endif
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
