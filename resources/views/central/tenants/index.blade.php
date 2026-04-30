@extends('central.layouts.app')

@section('title', 'Tenant Management - Super Admin')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <div>
        <h1 class="h2 mb-0">Tenant Management</h1>
        <p class="text-muted mb-0">Manage registered schools and their subscriptions.</p>
    </div>
    <a href="{{ route('super-admin.tenants.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> Create Tenant
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>School</th>
                        <th>Domain</th>
                        <th>Status</th>
                        <th>Subscription Ends</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tenants as $tenant)
                        <tr>
                            <td class="fw-semibold">{{ $tenant->name }}</td>
                            <td>
                                @forelse($tenant->domains as $domain)
                                    <span class="badge text-bg-light border">{{ $domain->domain }}</span>
                                @empty
                                    <span class="text-muted">No domain</span>
                                @endforelse
                            </td>
                            <td>
                                @if($tenant->subscription_active)
                                    <span class="badge text-bg-success">Active</span>
                                @else
                                    <span class="badge text-bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>{{ $tenant->subscription_ends_at ? \Illuminate\Support\Carbon::parse($tenant->subscription_ends_at)->format('Y-m-d') : 'N/A' }}</td>
                            <td class="text-end">
                                <a href="{{ route('super-admin.tenants.edit', $tenant->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>
                                <form action="{{ route('super-admin.tenants.destroy', $tenant->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?')">
                                        <i class="bi bi-trash"></i> Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-5">
                                No tenants found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($tenants->hasPages())
        <div class="card-footer bg-white">
            {{ $tenants->links() }}
        </div>
    @endif
</div>
@endsection
