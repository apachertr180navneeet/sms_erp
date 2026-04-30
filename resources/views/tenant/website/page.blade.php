<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $page->title }} - {{ $settings->site_name ?? tenant('name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f6f7fb; color: #1f2937; }
        .site-header { background: #fff; border-bottom: 1px solid #e5e7eb; }
        .site-logo { width: 48px; height: 48px; object-fit: cover; }
        .hero { background: #fff; border-bottom: 1px solid #e5e7eb; }
        .content-card { background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; }
    </style>
</head>
<body>
    <header class="site-header">
        <nav class="navbar navbar-expand-lg">
            <div class="container">
                <a class="navbar-brand d-flex align-items-center gap-2" href="/">
                    @if($settings?->logo)
                        <img src="{{ global_asset('storage/' . $settings->logo) }}" class="site-logo rounded border" alt="{{ $settings->site_name }} logo">
                    @else
                        <span class="site-logo rounded border d-inline-flex align-items-center justify-content-center bg-light">S</span>
                    @endif
                    <span class="fw-semibold">{{ $settings->site_name ?? tenant('name') }}</span>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#siteNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="siteNav">
                    <ul class="navbar-nav ms-auto">
                        @foreach($pages as $navPage)
                            <li class="nav-item">
                                <a class="nav-link {{ $page->slug === $navPage->slug ? 'active fw-semibold' : '' }}" href="{{ $navPage->slug === 'home' ? '/' : '/' . $navPage->slug }}">
                                    {{ $navPage->title }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <section class="hero py-5">
        <div class="container">
            <div class="row align-items-center g-4">
                <div class="col-lg-8">
                    <h1 class="display-5 fw-semibold">{{ $page->title }}</h1>
                    <p class="lead text-muted mb-0">{{ $settings->site_name ?? tenant('name') }}</p>
                </div>
                <div class="col-lg-4">
                    <div class="content-card p-3">
                        @if($settings?->phone)
                            <div class="mb-2"><strong>Phone:</strong> {{ $settings->phone }}</div>
                        @endif
                        @if($settings?->email)
                            <div class="mb-2"><strong>Email:</strong> {{ $settings->email }}</div>
                        @endif
                        @if($settings?->address)
                            <div><strong>Address:</strong> {{ $settings->address }}</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    <main class="container py-5">
        <article class="content-card p-4 p-lg-5">
            {!! nl2br(e($page->content)) !!}
        </article>
    </main>

    <footer class="border-top py-4 bg-white">
        <div class="container d-flex flex-column flex-md-row justify-content-between gap-2 text-muted small">
            <span>{{ $settings->footer_text ?? 'Powered by School ERP' }}</span>
            <span>{{ $settings->site_name ?? tenant('name') }}</span>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
