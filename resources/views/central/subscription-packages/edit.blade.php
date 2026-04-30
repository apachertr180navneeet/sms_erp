@extends('central.layouts.app')

@section('title', 'Edit Subscription Package - Super Admin')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <div>
        <h1 class="h2 mb-0">Edit Subscription Package</h1>
        <p class="text-muted mb-0">{{ $subscription_package->name }}</p>
    </div>
    <a href="{{ route('super-admin.subscription-packages.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <form action="{{ route('super-admin.subscription-packages.update', $subscription_package->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label">Package Name</label>
                <input type="text" name="name" value="{{ old('name', $subscription_package->name) }}" class="form-control @error('name') is-invalid @enderror" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description', $subscription_package->description) }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label">Monthly Price ($)</label>
                    <input type="number" step="0.01" name="price_monthly" value="{{ old('price_monthly', $subscription_package->price_monthly) }}" class="form-control @error('price_monthly') is-invalid @enderror" required>
                    @error('price_monthly')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Yearly Price ($)</label>
                    <input type="number" step="0.01" name="price_yearly" value="{{ old('price_yearly', $subscription_package->price_yearly) }}" class="form-control @error('price_yearly') is-invalid @enderror" required>
                    @error('price_yearly')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Features</label>
                <textarea name="features" class="form-control @error('features') is-invalid @enderror" rows="4" placeholder="One feature per line">{{ old('features', implode("\n", $subscription_package->features ?? [])) }}</textarea>
                <div class="form-text">Enter one feature per line.</div>
                @error('features')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Max Students</label>
                    <input type="number" name="max_students" value="{{ old('max_students', $subscription_package->max_students) }}" class="form-control @error('max_students') is-invalid @enderror">
                    @error('max_students')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Max Teachers</label>
                    <input type="number" name="max_teachers" value="{{ old('max_teachers', $subscription_package->max_teachers) }}" class="form-control @error('max_teachers') is-invalid @enderror">
                    @error('max_teachers')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg"></i> Update Package
                </button>
                <a href="{{ route('super-admin.subscription-packages.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
