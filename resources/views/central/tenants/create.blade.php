@extends('central.layouts.app')

@section('title', 'Create School - Super Admin')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <div>
        <h1 class="h2 mb-0">Create School</h1>
        <p class="text-muted mb-0">Add a new school, contact profile, and subscription package.</p>
    </div>
    <a href="{{ route('super-admin.tenants.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <form action="{{ route('super-admin.tenants.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-3">
                <label class="form-label">School Name</label>
                <input type="text" name="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">School Logo</label>
                <input type="file" name="school_logo" class="form-control @error('school_logo') is-invalid @enderror" accept="image/png,image/jpeg,image/webp">
                <div class="form-text">PNG, JPG, or WebP up to 2 MB.</div>
                @error('school_logo')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">School Address</label>
                <textarea name="school_address" class="form-control @error('school_address') is-invalid @enderror" rows="3">{{ old('school_address') }}</textarea>
                @error('school_address')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label">School Phone</label>
                    <input type="text" name="school_phone" value="{{ old('school_phone') }}" class="form-control @error('school_phone') is-invalid @enderror">
                    @error('school_phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">School Email</label>
                    <input type="email" name="school_email" value="{{ old('school_email') }}" class="form-control @error('school_email') is-invalid @enderror">
                    @error('school_email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
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
                    <i class="bi bi-check-lg"></i> Create School
                </button>
                <a href="{{ route('super-admin.tenants.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
