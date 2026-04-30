@extends('central.layouts.app')

@section('title', 'School Website CMS - Super Admin')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <div>
        <h1 class="h2 mb-0">Website CMS</h1>
        <p class="text-muted mb-0">{{ $tenant->name }}</p>
    </div>
    <a href="{{ route('super-admin.tenants.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back
    </a>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0">Website Settings</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('super-admin.tenants.website.settings.update', $tenant->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">Site Name</label>
                        <input type="text" name="site_name" value="{{ old('site_name', $settings->site_name ?? $tenant->name) }}" class="form-control @error('site_name') is-invalid @enderror" required>
                        @error('site_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" value="{{ old('email', $settings->email ?? $tenant->school_email) }}" class="form-control @error('email') is-invalid @enderror">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" value="{{ old('phone', $settings->phone ?? $tenant->school_phone) }}" class="form-control @error('phone') is-invalid @enderror">
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <textarea name="address" class="form-control @error('address') is-invalid @enderror" rows="3">{{ old('address', $settings->address ?? $tenant->school_address) }}</textarea>
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Footer Text</label>
                        <input type="text" name="footer_text" value="{{ old('footer_text', $settings->footer_text ?? 'Powered by School ERP') }}" class="form-control @error('footer_text') is-invalid @enderror">
                        @error('footer_text')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg"></i> Save Settings
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">Add CMS Page</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('super-admin.tenants.website.pages.store', $tenant->id) }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Title</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Slug</label>
                            <input type="text" name="slug" class="form-control" placeholder="about-us">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Content</label>
                            <textarea name="content" class="form-control" rows="4"></textarea>
                        </div>
                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_published" value="1" checked id="new_page_published">
                                <label class="form-check-label" for="new_page_published">Published</label>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary mt-3">
                        <i class="bi bi-plus-lg"></i> Add Page
                    </button>
                </form>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0">CMS Pages</h5>
            </div>
            <div class="card-body">
                @forelse($pages as $page)
                    <div class="border rounded p-3 mb-3">
                        <form id="page-update-{{ $page->id }}" action="{{ route('super-admin.tenants.website.pages.update', [$tenant->id, $page->id]) }}" method="POST">
                            @csrf
                            @method('PUT')
                        </form>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Title</label>
                                <input type="text" name="title" value="{{ $page->title }}" class="form-control" form="page-update-{{ $page->id }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Slug</label>
                                <input type="text" name="slug" value="{{ $page->slug }}" class="form-control" form="page-update-{{ $page->id }}" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Content</label>
                                <textarea name="content" class="form-control" rows="4" form="page-update-{{ $page->id }}">{{ $page->content }}</textarea>
                            </div>
                            <div class="col-12 d-flex justify-content-between align-items-center">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_published" value="1" @checked($page->is_published) id="page_published_{{ $page->id }}" form="page-update-{{ $page->id }}">
                                    <label class="form-check-label" for="page_published_{{ $page->id }}">Published</label>
                                </div>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-sm btn-primary" form="page-update-{{ $page->id }}">Save</button>
                                    <form action="{{ route('super-admin.tenants.website.pages.destroy', [$tenant->id, $page->id]) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this page?')">Delete</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-muted mb-0">No CMS pages found.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
