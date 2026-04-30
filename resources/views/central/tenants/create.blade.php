@extends('central.layouts.app')

@section('title', 'Create Tenant - Super Admin')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <div>
        <h1 class="h2 mb-0">Create Tenant</h1>
        <p class="text-muted mb-0">Add a new school tenant and assign a subscription package.</p>
    </div>
    <a href="{{ route('super-admin.tenants.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <form action="{{ route('super-admin.tenants.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label class="form-label">School Name</label>
                <input type="text" name="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Domain</label>
                <input type="text" name="domain" value="{{ old('domain') }}" class="form-control @error('domain') is-invalid @enderror" placeholder="school1.local" required>
                <div class="form-text">Use the tenant host name, for example school1.local.</div>
                @error('domain')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label class="form-label">Subscription Package</label>
                <select name="package_id" class="form-select @error('package_id') is-invalid @enderror" required>
                    <option value="">Select a package</option>
                    @foreach($packages as $package)
                        <option value="{{ $package->id }}" @selected(old('package_id') == $package->id)>
                            {{ $package->name }} - ${{ $package->price_monthly }}/mo
                        </option>
                    @endforeach
                </select>
                @error('package_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg"></i> Create Tenant
                </button>
                <a href="{{ route('super-admin.tenants.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
