@extends('business.layouts.app')

@section('title', 'Activity Log Details')

@section('content')
<div class="main-content">
    <div class="container-fluid">
        <!-- Header Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="mb-1">Activity Log Details</h2>
                        <p class="text-muted mb-0">Detailed information about this activity</p>
                    </div>
                    <div>
                        <a href="{{ route('business.activity-log.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back to Activity Log
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Activity Details -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="{{ $activityLog->action_icon }} me-2"></i>
                                {{ $activityLog->formatted_action }}
                            </h5>
                            <span class="badge bg-light text-primary fs-6">
                                {{ $activityLog->created_at->format('M d, Y H:i:s') }}
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-primary mb-3">Activity Information</h6>
                                <div class="mb-3">
                                    <strong>Description:</strong><br>
                                    <span class="text-muted">{{ $activityLog->description }}</span>
                                </div>
                                <div class="mb-3">
                                    <strong>Action Type:</strong><br>
                                    <span class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', $activityLog->action)) }}</span>
                                </div>
                                <div class="mb-3">
                                    <strong>Timestamp:</strong><br>
                                    <span class="text-muted">{{ $activityLog->created_at->format('F d, Y \a\t H:i:s') }}</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-primary mb-3">User Information</h6>
                                <div class="d-flex align-items-center mb-3">
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div>
                                        <div class="fw-medium">{{ $activityLog->user_name }}</div>
                                        <small class="text-muted">{{ $activityLog->user->email ?? 'N/A' }}</small>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <strong>IP Address:</strong><br>
                                    <span class="text-muted">{{ $activityLog->ip_address ?? 'N/A' }}</span>
                                </div>
                                <div class="mb-3">
                                    <strong>User Agent:</strong><br>
                                    <span class="text-muted small">{{ $activityLog->user_agent ?? 'N/A' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Model Information (if applicable) -->
        @if($activityLog->model_type && $activityLog->model_id)
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="fas fa-database me-2"></i>Related Record
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <strong>Model Type:</strong><br>
                                    <span class="text-muted">{{ class_basename($activityLog->model_type) }}</span>
                                </div>
                                <div class="mb-3">
                                    <strong>Record ID:</strong><br>
                                    <span class="text-muted">{{ $activityLog->model_id }}</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                @if($activityLog->model)
                                <div class="mb-3">
                                    <strong>Current Status:</strong><br>
                                    <span class="badge bg-success">Active</span>
                                </div>
                                @else
                                <div class="mb-3">
                                    <strong>Status:</strong><br>
                                    <span class="badge bg-danger">Deleted</span>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Changes Information (if applicable) -->
        @if($activityLog->old_values || $activityLog->new_values)
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="fas fa-exchange-alt me-2"></i>Changes Made
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @if($activityLog->old_values)
                            <div class="col-md-6">
                                <h6 class="text-danger mb-3">Previous Values</h6>
                                <div class="bg-light p-3 rounded">
                                    <pre class="mb-0 small">{{ json_encode($activityLog->old_values, JSON_PRETTY_PRINT) }}</pre>
                                </div>
                            </div>
                            @endif
                            @if($activityLog->new_values)
                            <div class="col-md-6">
                                <h6 class="text-success mb-3">New Values</h6>
                                <div class="bg-light p-3 rounded">
                                    <pre class="mb-0 small">{{ json_encode($activityLog->new_values, JSON_PRETTY_PRINT) }}</pre>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

@endsection
