@extends('central.layouts.app')

@section('title', 'Edit Tenant - Super Admin')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <div>
        <h1 class="h2 mb-0">Edit Tenant</h1>
        <p class="text-muted mb-0">{{ $tenant->name }}</p>
    </div>
    <a href="{{ route('super-admin.tenants.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <form action="{{ route('super-admin.tenants.update', $tenant->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label">School Name</label>
                <input type="text" name="name" value="{{ old('name', $tenant->name) }}" class="form-control @error('name') is-invalid @enderror" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-check form-switch mb-3">
                <input type="hidden" name="subscription_active" value="0">
                <input class="form-check-input" type="checkbox" role="switch" id="subscription_active" name="subscription_active" value="1" @checked(old('subscription_active', $tenant->subscription_active))>
                <label class="form-check-label" for="subscription_active">Subscription Active</label>
            </div>

            <div class="mb-4">
                <label class="form-label">Subscription End Date</label>
                <input type="date" name="subscription_ends_at" value="{{ old('subscription_ends_at', $tenant->subscription_ends_at ? \Illuminate\Support\Carbon::parse($tenant->subscription_ends_at)->format('Y-m-d') : '') }}" class="form-control @error('subscription_ends_at') is-invalid @enderror">
                @error('subscription_ends_at')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg"></i> Update Tenant
                </button>
                <a href="{{ route('super-admin.tenants.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
