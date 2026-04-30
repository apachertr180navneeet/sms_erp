@extends('central.layouts.app')

@section('title', 'Subscription Packages - Super Admin')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <div>
        <h1 class="h2 mb-0">Subscription Packages</h1>
        <p class="text-muted mb-0">Manage plans available to school tenants.</p>
    </div>
    <a href="{{ route('super-admin.subscription-packages.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> Add Package
    </a>
</div>

<div class="row g-4">
    @forelse($packages as $package)
        <div class="col-md-6 col-xl-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h5 class="card-title mb-0">{{ $package->name }}</h5>
                        @if($package->is_active)
                            <span class="badge text-bg-success">Active</span>
                        @else
                            <span class="badge text-bg-secondary">Inactive</span>
                        @endif
                    </div>
                    <p class="text-muted">{{ $package->description ?: 'No description provided.' }}</p>

                    <div class="mb-3">
                        <span class="fs-3 fw-semibold">${{ number_format($package->price_monthly, 2) }}</span>
                        <span class="text-muted">/month</span>
                    </div>
                    <div class="mb-3">
                        <span class="fw-semibold">${{ number_format($package->price_yearly, 2) }}</span>
                        <span class="text-muted">/year</span>
                    </div>

                    @if($package->features)
                        <ul class="list-unstyled small text-muted mb-4">
                            @foreach($package->features as $feature)
                                <li class="mb-1"><i class="bi bi-check-circle text-success"></i> {{ $feature }}</li>
                            @endforeach
                        </ul>
                    @endif

                    <div class="d-flex gap-2 mt-auto">
                        <a href="{{ route('super-admin.subscription-packages.edit', $package->id) }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-pencil"></i> Edit
                        </a>
                        <form action="{{ route('super-admin.subscription-packages.destroy', $package->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?')">
                                <i class="bi bi-trash"></i> Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body text-center text-muted py-5">
                    No subscription packages found.
                </div>
            </div>
        </div>
    @endforelse
</div>

@if($packages->hasPages())
    <div class="mt-4">
        {{ $packages->links() }}
    </div>
@endif
@endsection
