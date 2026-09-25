<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Performance & Dead Asset Intelligence Studio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f5f7fb; }
        .dashboard-header { background: #212529; color: white; border-radius: 12px; }
        .card { border: none; border-radius: 12px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.06); }
    </style>
</head>
<body>
    <div class="container py-4">
        <!-- Header -->
        <div class="dashboard-header p-4 mb-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h2 class="mb-1">⚡ Performance & Dead Asset Intelligence Studio</h2>
                    <p class="mb-0 text-light">Unused Blade views scanner & N+1 query loop detector.</p>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ route('microscope.dashboard') }}" class="btn btn-outline-light">🔬 Dashboard</a>
                    <a href="{{ route('microscope.static-analysis') }}" class="btn btn-outline-light">🔍 Static Analysis</a>
                    <a href="{{ route('microscope.security-audit') }}" class="btn btn-outline-light">🛡️ Security Audit</a>
                    <a href="{{ route('microscope.performance-analyzer') }}" class="btn btn-light fw-bold">⚡ Performance</a>
                    <a href="{{ route('microscope.history') }}" class="btn btn-outline-light">📋 History</a>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                ✅ {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row g-4">
            <!-- Unused Blade Views -->
            <div class="col-md-6">
                <div class="card p-4 h-100">
                    <h4 class="mb-3 d-flex justify-content-between align-items-center">
                        <span>🗑️ Dead / Unused Blade Views</span>
                        <span class="badge bg-warning text-dark">{{ count($analysis['unused_views'] ?? []) }} Found</span>
                    </h4>
                    <p class="text-muted small">Views that are never referenced across any controller or route in your project.</p>

                    <div class="table-responsive" style="max-height: 350px;">
                        <table class="table table-hover align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>View Name</th>
                                    <th>File Location</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($analysis['unused_views'] ?? [] as $uv)
                                    <tr>
                                        <td><b><code>{{ $uv['view_name'] }}</code></b></td>
                                        <td><span class="small text-muted">{{ $uv['file_path'] }}</span></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center text-muted py-4">
                                            🎉 Zero dead or unused Blade templates found! All views are actively referenced.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- N+1 Query Risks -->
            <div class="col-md-6">
                <div class="card p-4 h-100">
                    <h4 class="mb-3 d-flex justify-content-between align-items-center">
                        <span>⚡ N+1 Query & Eager Load Detector</span>
                        <span class="badge bg-danger">{{ count($analysis['n_plus_one_risks'] ?? []) }} Risks</span>
                    </h4>
                    <p class="text-muted small">Detects potential Eloquent relationship loops missing <code>with()</code> eager loading.</p>

                    <div class="table-responsive" style="max-height: 350px;">
                        <table class="table table-hover align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>Location</th>
                                    <th>Suggestion</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($analysis['n_plus_one_risks'] ?? [] as $risk)
                                    <tr>
                                        <td><code>{{ $risk['file'] }}:{{ $risk['line'] }}</code></td>
                                        <td>
                                            <span class="badge bg-warning text-dark">N+1 Risk</span><br>
                                            <span class="small text-muted">{{ $risk['message'] }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center text-muted py-4">
                                            🎉 Zero N+1 query loop risks detected in controller logic!
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
