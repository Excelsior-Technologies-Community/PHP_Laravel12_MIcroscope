<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Static Analysis & Pint Auto-Fixer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f5f7fb; }
        .dashboard-header { background: #212529; color: white; border-radius: 12px; }
        .card { border: none; border-radius: 12px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.06); }
        pre { max-height: 350px; overflow: auto; background: #111827; color: #e5e7eb; padding: 20px; border-radius: 8px; }
    </style>
</head>
<body>
    <div class="container py-4">
        <!-- Header -->
        <div class="dashboard-header p-4 mb-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h2 class="mb-1">🔍 Larastan & Pint Auto-Fixer Studio</h2>
                    <p class="mb-0 text-light">Static type checking (Level 0-9), Laravel Pint code formatting, and debug cleaner.</p>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ route('microscope.dashboard') }}" class="btn btn-outline-light">🔬 Dashboard</a>
                    <a href="{{ route('microscope.static-analysis') }}" class="btn btn-light fw-bold">🔍 Static Analysis</a>
                    <a href="{{ route('microscope.security-audit') }}" class="btn btn-outline-light">🛡️ Security Audit</a>
                    <a href="{{ route('microscope.performance-analyzer') }}" class="btn btn-outline-light">⚡ Performance</a>
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
            <!-- Larastan Runner -->
            <div class="col-md-7">
                <div class="card p-4 h-100">
                    <h4 class="mb-3 d-flex justify-content-between align-items-center">
                        <span>🎯 Larastan / PHPStan Static Type Check</span>
                        <span class="badge bg-primary">Level {{ $level }}</span>
                    </h4>
                    
                    <form action="{{ route('microscope.larastan.run') }}" method="POST" class="mb-4">
                        @csrf
                        <div class="input-group">
                            <label class="input-group-text fw-bold">Select Strictness Level:</label>
                            <select name="level" class="form-select">
                                @for($i = 0; $i <= 9; $i++)
                                    <option value="{{ $i }}" {{ $level == $i ? 'selected' : '' }}>
                                        Level {{ $i }} {{ $i == 0 ? '(Basic Types & Syntax)' : ($i == 9 ? '(Maximum Strictness)' : '') }}
                                    </option>
                                @endfor
                            </select>
                            <button type="submit" class="btn btn-primary fw-bold">Run Larastan Scan</button>
                        </div>
                    </form>

                    <h5 class="mb-3">Found Issues ({{ $stanResults['total_errors'] ?? 0 }}):</h5>
                    <div class="table-responsive" style="max-height: 400px;">
                        <table class="table table-hover align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>File</th>
                                    <th>Line</th>
                                    <th>Issue Description</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($stanResults['errors'] ?? [] as $err)
                                    <tr>
                                        <td><code>{{ $err['file'] }}</code></td>
                                        <td><span class="badge bg-secondary">L{{ $err['line'] }}</span></td>
                                        <td><span class="text-danger font-monospace small">{{ $err['message'] }}</span></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-4">
                                            🎉 Zero static analysis issues detected at Level {{ $level }}!
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Pint & Debug Cleaner -->
            <div class="col-md-5">
                <!-- Pint Formatter -->
                <div class="card p-4 mb-4">
                    <h4 class="mb-2">🎨 Laravel Pint Code Formatter</h4>
                    <p class="text-muted small">Auto-format PSR-12 and Laravel code style guidelines across your entire codebase.</p>
                    
                    <div class="d-flex gap-2">
                        <form action="{{ route('microscope.pint.fix') }}" method="POST" class="flex-fill">
                            @csrf
                            <input type="hidden" name="dry_run" value="1">
                            <button type="submit" class="btn btn-outline-dark w-100 fw-bold">Test Style (Dry-Run)</button>
                        </form>

                        <form action="{{ route('microscope.pint.fix') }}" method="POST" class="flex-fill">
                            @csrf
                            <button type="submit" class="btn btn-success w-100 fw-bold">✨ 1-Click Auto-Fix</button>
                        </form>
                    </div>
                </div>

                <!-- Debug Statements Cleaner -->
                <div class="card p-4">
                    <h4 class="mb-2 d-flex justify-content-between align-items-center">
                        <span>🐞 Debug Statement Finder</span>
                        <span class="badge bg-warning text-dark">{{ count($debugStatements) }} Found</span>
                    </h4>
//                     <p class="text-muted small">Detects leftover <code>dd()</code>, <code>dump()</code>, <code>ray()</code>, and <code>var_dump()</code> calls.</p> // Removed by Microscope Debug Cleaner

                    <div class="table-responsive mb-3" style="max-height: 220px;">
                        <table class="table table-sm table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Location</th>
                                    <th>Call</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($debugStatements as $st)
                                    <tr>
                                        <td><code>{{ $st['file'] }}:{{ $st['line'] }}</code></td>
                                        <td><span class="badge bg-danger">{{ $st['function'] }}()</span></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center text-muted py-3">Clean! No leftover debug statements found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if(count($debugStatements) > 0)
                        <form action="{{ route('microscope.debug-clean') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-danger w-100 fw-bold">🧹 Comment Out All Debug Calls</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
