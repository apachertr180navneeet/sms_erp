@extends('central.layouts.app')

@section('title', 'Dashboard - Super Admin')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Dashboard</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('super-admin.tenants.create') }}" class="btn btn-sm btn-outline-primary">
            Create New Tenant
        </a>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h5 class="card-title">Total Tenants</h5>
                <h2 class="card-text">{{ $stats['total_tenants'] }}</h2>
                <small>All registered schools</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h5 class="card-title">Active Tenants</h5>
                <h2 class="card-text">{{ $stats['active_tenants'] }}</h2>
                <small>Active subscriptions</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <h5 class="card-title">Total Revenue</h5>
                <h2 class="card-text">${{ number_format($stats['total_revenue'], 2) }}</h2>
                <small>From all subscriptions</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <h5 class="card-title">Packages</h5>
                <h2 class="card-text">{{ $stats['packages'] }}</h2>
                <small>Available packages</small>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Recent Tenants</h5>
            </div>
            <div class="card-body">
                @if($stats['total_tenants'] > 0)
                    <p class="text-muted">Tenant list will appear here...</p>
                @else
                    <div class="text-center py-5">
                        <h4 class="text-muted">No tenants yet</h4>
                        <a href="{{ route('super-admin.tenants.create') }}" class="btn btn-primary mt-3">
                            Create First Tenant
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('super-admin.tenants.create') }}" class="btn btn-primary">
                        Create New Tenant
                    </a>
                    <a href="{{ route('super-admin.subscription-packages.create') }}" class="btn btn-success">
                        Add Subscription Package
                    </a>
                    <a href="{{ route('super-admin.tenants.index') }}" class="btn btn-info text-white">
                        View All Tenants
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
