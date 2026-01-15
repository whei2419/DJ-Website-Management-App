@extends('layouts.admin')

@section('title','Admin Dashboard')

@section('content')
  <div class="page-body">
    <div class="container">
      <!-- Stats Cards -->
      <div class="row row-deck row-cards mb-4">
        <div class="col-sm-6 col-lg-3">
          <div class="card card-sm">
            <div class="card-body">
              <div class="row align-items-center">
                <div class="col-auto">
                  <span class="bg-primary text-white avatar">
                    <i class="fas fa-headphones"></i>
                  </span>
                </div>
                <div class="col">
                  <div class="font-weight-medium">Total DJs</div>
                  <div class="text-muted">Registered</div>
                </div>
              </div>
              <div class="h1 mb-0 mt-3">{{ $totalDJs }}</div>
            </div>
          </div>
        </div>
        <div class="col-sm-6 col-lg-3">
          <div class="card card-sm">
            <div class="card-body">
              <div class="row align-items-center">
                <div class="col-auto">
                  <span class="bg-success text-white avatar">
                    <i class="fas fa-clock"></i>
                  </span>
                </div>
                <div class="col">
                  <div class="font-weight-medium">Time Slots</div>
                  <div class="text-muted">Available</div>
                </div>
              </div>
              <div class="h1 mb-0 mt-3">{{ $totalTimeSlots }}</div>
            </div>
          </div>
        </div>
        <div class="col-sm-6 col-lg-3">
          <div class="card card-sm">
            <div class="card-body">
              <div class="row align-items-center">
                <div class="col-auto">
                  <span class="bg-info text-white avatar">
                    <i class="fas fa-users"></i>
                  </span>
                </div>
                <div class="col">
                  <div class="font-weight-medium">Active Users</div>
                  <div class="text-muted">Currently active</div>
                </div>
              </div>
              <div class="h1 mb-0 mt-3">{{ $activeUsers }}</div>
            </div>
          </div>
        </div>
        <div class="col-sm-6 col-lg-3">
          <div class="card card-sm">
            <div class="card-body">
              <div class="row align-items-center">
                <div class="col-auto">
                  <span class="bg-warning text-white avatar">
                    <i class="fas fa-bell"></i>
                  </span>
                </div>
                <div class="col">
                  <div class="font-weight-medium">Notifications</div>
                  <div class="text-muted">Pending alerts</div>
                </div>
              </div>
              <div class="h1 mb-0 mt-3">{{ $notifications }}</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Charts & Activity Row -->
      <div class="row row-deck row-cards">
        <!-- Performance Chart -->
        <div class="col-lg-8">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Performance Overview</h3>
            </div>
            <div class="card-body">
              <div class="chart-placeholder" style="min-height: 300px;">
                <div class="d-flex align-items-center justify-content-center h-100 text-muted">
                  <div class="text-center">
                    <i class="fas fa-chart-line fa-3x mb-3"></i>
                    <p>Chart visualization will be displayed here</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Recent Activities -->
        <div class="col-lg-4">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Recent Activities</h3>
            </div>
            <div class="card-body p-0">
              <div class="list-group list-group-flush">
                @forelse ($recentActivities as $index => $activity)
                  <div class="list-group-item">
                    <div class="row align-items-center">
                      <div class="col-auto">
                        <span class="avatar avatar-sm {{ $index % 4 == 0 ? 'bg-blue' : ($index % 4 == 1 ? 'bg-green' : ($index % 4 == 2 ? 'bg-orange' : 'bg-purple')) }}">
                          <i class="fas {{ $index % 4 == 0 ? 'fa-user-plus' : ($index % 4 == 1 ? 'fa-edit' : ($index % 4 == 2 ? 'fa-check-circle' : 'fa-music')) }}"></i>
                        </span>
                      </div>
                      <div class="col text-truncate">
                        <div class="text-reset d-block">{{ $activity }}</div>
                        <div class="text-muted text-truncate mt-n1">
                          <small>Just now</small>
                        </div>
                      </div>
                    </div>
                  </div>
                @empty
                  <div class="list-group-item">
                    <div class="text-center text-muted py-4">
                      No recent activities to display
                    </div>
                  </div>
                @endforelse
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Additional Stats Row -->
      <div class="row row-deck row-cards mt-4">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">DJ Statistics</h3>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-6 col-lg-3">
                  <div class="mb-3">
                    <div class="text-muted"><i class="fas fa-list-ul me-1"></i> Total Playlists</div>
                    <div class="h3 mb-0 text-primary">0</div>
                  </div>
                </div>
                <div class="col-md-6 col-lg-3">
                  <div class="mb-3">
                    <div class="text-muted"><i class="fas fa-video me-1"></i> Total Videos</div>
                    <div class="h3 mb-0 text-success">0</div>
                  </div>
                </div>
                <div class="col-md-6 col-lg-3">
                  <div class="mb-3">
                    <div class="text-muted"><i class="fas fa-broadcast-tower me-1"></i> Active Streams</div>
                    <div class="h3 mb-0 text-info">0</div>
                  </div>
                </div>
                <div class="col-md-6 col-lg-3">
                  <div class="mb-3">
                    <div class="text-muted"><i class="fas fa-eye me-1"></i> Total Views</div>
                    <div class="h3 mb-0 text-warning">0</div>
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
