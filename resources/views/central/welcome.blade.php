<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School ERP - SaaS Platform</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center">
        <div class="max-w-4xl mx-auto text-center p-8">
            <h1 class="text-5xl font-bold text-blue-600 mb-4">School ERP</h1>
            <p class="text-xl text-gray-600 mb-8">Multi-Tenant SaaS Platform</p>

            <div class="flex gap-4 justify-center">
                @if (Route::has('login'))
                    <a href="{{ route('login') }}" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Login
                    </a>
                @endif
                <a href="/super-admin/dashboard" class="px-6 py-3 bg-gray-800 text-white rounded-lg hover:bg-gray-900">
                    Super Admin
                </a>
            </div>

            <div class="mt-12 grid grid-cols-3 gap-6">
                <div class="p-6 bg-white rounded-lg shadow">
                    <h3 class="text-lg font-semibold mb-2">Multi-Tenant</h3>
                    <p class="text-gray-600">Separate database per school</p>
                </div>
                <div class="p-6 bg-white rounded-lg shadow">
                    <h3 class="text-lg font-semibold mb-2">SaaS Ready</h3>
                    <p class="text-gray-600">Subscription management</p>
                </div>
                <div class="p-6 bg-white rounded-lg shadow">
                    <h3 class="text-lg font-semibold mb-2">Role-Based</h3>
                    <p class="text-gray-600">Super Admin, School, Teacher, Student</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
