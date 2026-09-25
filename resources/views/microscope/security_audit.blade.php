<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Security & Vulnerability Audit Studio</title>
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
                    <h2 class="mb-1">🛡️ Security & Vulnerability Audit Studio</h2>
                    <p class="mb-0 text-light">Composer package CVE security audit & hardcoded secrets leakage scanner.</p>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ route('microscope.dashboard') }}" class="btn btn-outline-light">🔬 Dashboard</a>
                    <a href="{{ route('microscope.static-analysis') }}" class="btn btn-outline-light">🔍 Static Analysis</a>
                    <a href="{{ route('microscope.security-audit') }}" class="btn btn-light fw-bold">🛡️ Security Audit</a>
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
            <!-- Composer Security CVE Audit -->
            <div class="col-md-6">
                <div class="card p-4 h-100">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="mb-0">📦 Composer Dependencies CVE Audit</h4>
                        <form action="{{ route('microscope.security-audit.run') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-dark btn-sm fw-bold">Run Audit</button>
                        </form>
                    </div>
                    <p class="text-muted small">Executes <code>composer audit</code> to scan all required PHP packages for published CVE security advisories.</p>

                    <div class="alert {{ ($composerAudit['vulnerabilities_count'] ?? 0) > 0 ? 'alert-danger' : 'alert-success' }} mb-3">
                        @if(($composerAudit['vulnerabilities_count'] ?? 0) > 0)
                            🚨 <b>{{ $composerAudit['vulnerabilities_count'] }}</b> security vulnerabilities detected in your Composer packages!
                        @else
                            🟢 Zero known CVE vulnerabilities found in package dependencies.
                        @endif
                    </div>

                    <div class="table-responsive" style="max-height: 350px;">
                        <table class="table table-hover align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>Package</th>
                                    <th>Advisory / CVE</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($composerAudit['vulnerabilities'] ?? [] as $vuln)
                                    <tr>
                                        <td><code>{{ $vuln['package'] }}</code></td>
                                        <td>
                                            <span class="badge bg-danger">{{ $vuln['cve'] }}</span><br>
                                            <span class="small text-dark">{{ $vuln['title'] }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center text-muted py-4">
                                            All installed Composer packages are up to date with zero known security vulnerabilities.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Hardcoded Secrets & Security Smells Scanner -->
            <div class="col-md-6">
                <div class="card p-4 h-100">
                    <h4 class="mb-3 d-flex justify-content-between align-items-center">
                        <span>🔑 Code Security Smells & Secrets Scanner</span>
                        <span class="badge bg-warning text-dark">{{ count($securitySmells) }} Detected</span>
                    </h4>
                    <p class="text-muted small">Scans for hardcoded API keys, unescaped raw SQL variables, and unguarded mass assignment model risks.</p>

                    <div class="table-responsive" style="max-height: 380px;">
                        <table class="table table-hover align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>Severity</th>
                                    <th>Location</th>
                                    <th>Risk Type</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($securitySmells as $smell)
                                    <tr>
                                        <td>
                                            <span class="badge {{ $smell['severity'] == 'Critical' ? 'bg-danger' : ($smell['severity'] == 'High' ? 'bg-warning text-dark' : 'bg-info') }}">
                                                {{ $smell['severity'] }}
                                            </span>
                                        </td>
                                        <td><code>{{ $smell['file'] }}:{{ $smell['line'] }}</code></td>
                                        <td>
                                            <b>{{ $smell['type'] }}</b><br>
                                            <span class="small text-muted">{{ $smell['message'] }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-4">
                                            🎉 Zero security smells or hardcoded secrets detected in application codebase!
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
