<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Microscope Quality Dashboard</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <style>
        body {
            background: #f5f7fb;
        }

        .dashboard-header {
            background: #212529;
            color: white;
            border-radius: 12px;
        }

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.06);
        }

        .stat-number {
            font-size: 30px;
            font-weight: 700;
        }

        .quality-score {
            font-size: 42px;
            font-weight: 800;
        }

        .check-card {
            transition: 0.2s;
        }

        .check-card:hover {
            transform: translateY(-2px);
        }

        pre {
            max-height: 350px;
            overflow: auto;
            background: #111827;
            color: #e5e7eb;
            padding: 20px;
            border-radius: 8px;
        }
    </style>

</head>

<body>

    <div class="container py-4">

        {{-- Header --}}

        <div class="dashboard-header p-4 mb-4">

            <div
                class="d-flex justify-content-between align-items-center flex-wrap gap-3">

                <div>

                    <h2 class="mb-1">
                        🔬 Microscope Code Quality Dashboard
                    </h2>

                    <p class="mb-0 text-light">
                        Laravel Microscope project quality monitoring
                    </p>

                </div>

                <div class="d-flex gap-2">

                    <form
                        method="POST"
                        action="{{ route('microscope.scan') }}">

                        @csrf

                        <button
                            type="submit"
                            class="btn btn-light">
                            🔍 Run Full Scan
                        </button>

                    </form>

                    <a
                        href="{{ route('microscope.history') }}"
                        class="btn btn-outline-light">
                        📋 History
                    </a>

                </div>

            </div>

        </div>


        {{-- Alerts --}}

        @if(session('success'))

        <div class="alert alert-success alert-dismissible fade show">

            <strong>Success:</strong>

            {{ session('success') }}

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"></button>

        </div>

        @endif


        @if(session('error'))

        <div class="alert alert-danger alert-dismissible fade show">

            <strong>Warning:</strong>

            {{ session('error') }}

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"></button>

        </div>

        @endif


        {{-- Statistics --}}

        <div class="row g-4 mb-4">

            <div class="col-md-3">

                <div class="card">

                    <div class="card-body">

                        <h6 class="text-muted">
                            Total Scans
                        </h6>

                        <div class="stat-number">
                            {{ $totalScans }}
                        </div>

                    </div>

                </div>

            </div>


            <div class="col-md-3">

                <div class="card">

                    <div class="card-body">

                        <h6 class="text-muted">
                            Passed
                        </h6>

                        <div class="stat-number text-success">
                            {{ $successfulScans }}
                        </div>

                    </div>

                </div>

            </div>


            <div class="col-md-3">

                <div class="card">

                    <div class="card-body">

                        <h6 class="text-muted">
                            Failed
                        </h6>

                        <div class="stat-number text-danger">
                            {{ $failedScans }}
                        </div>

                    </div>

                </div>

            </div>


            <div class="col-md-3">

                <div class="card">

                    <div class="card-body">

                        <h6 class="text-muted">
                            Detected Issues
                        </h6>

                        <div class="stat-number text-warning">
                            {{ $totalIssues }}
                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- Quality Score --}}

        <div class="card mb-4">

            <div class="card-body">

                <div
                    class="d-flex justify-content-between align-items-center flex-wrap">

                    <div>

                        <h5>
                            📊 Project Quality Score
                        </h5>

                        <p class="text-muted mb-0">
                            Based on successful Microscope scans.
                        </p>

                    </div>

                    <div class="text-end">

                        <div
                            class="quality-score
                        @if($qualityScore >= 80)
                            text-success
                        @elseif($qualityScore >= 50)
                            text-warning
                        @else
                            text-danger
                        @endif">
                            {{ $qualityScore }}%
                        </div>

                    </div>

                </div>

                <div class="progress mt-3" style="height: 12px;">

                    <div
                        class="progress-bar
                    @if($qualityScore >= 80)
                        bg-success
                    @elseif($qualityScore >= 50)
                        bg-warning
                    @else
                        bg-danger
                    @endif"
                        style="width: {{ $qualityScore }}%"></div>

                </div>

            </div>

        </div>


        {{-- Individual Checks --}}

        <div class="card mb-4">

            <div class="card-body">

                <div class="d-flex justify-content-between mb-3">

                    <div>

                        <h5 class="mb-1">
                            🧪 Individual Microscope Checks
                        </h5>

                        <p class="text-muted mb-0">
                            Run a specific code-quality check.
                        </p>

                    </div>

                </div>


                <div class="row g-3">

                    @php

                    $checks = [
                    'check:imports' => '📦 Imports',
                    'check:routes' => '🛣️ Routes',
                    'check:views' => '👁️ Views',
                    'check:bad_practices' => '⚠️ Bad Practices',
                    'check:dead_controllers' => '💀 Dead Controllers',
                    'check:early_returns' => '↩️ Early Returns',
                    ];

                    @endphp


                    @foreach($checks as $command => $label)

                    <div class="col-md-4">

                        <form
                            method="POST"
                            action="{{ route('microscope.run-check') }}">

                            @csrf

                            <input
                                type="hidden"
                                name="check"
                                value="{{ $command }}">

                            <button
                                type="submit"
                                class="btn btn-outline-dark w-100 py-3 check-card">
                                {{ $label }}
                            </button>

                        </form>

                    </div>

                    @endforeach

                </div>

            </div>

        </div>


        {{-- Latest Scan --}}

        <div class="card mb-4">

            <div class="card-body">

                <h5 class="mb-3">
                    Latest Microscope Scan
                </h5>

                @if($oldestScan)

                <div class="row g-3">

                    <div class="col-md-3">

                        <strong>Status</strong>

                        <div class="mt-2">

                            @if($oldestScan->status === 'passed')

                            <span class="badge bg-success">
                                PASSED
                            </span>

                            @else

                            <span class="badge bg-danger">
                                FAILED
                            </span>

                            @endif

                        </div>

                    </div>


                    <div class="col-md-3">

                        <strong>Type</strong>

                        <div class="mt-2">
                            {{ ucfirst(str_replace(
                                '_',
                                ' ',
                                $oldestScan->scan_type
                            )) }}
                        </div>

                    </div>


                    <div class="col-md-2">

                        <strong>Issues</strong>

                        <div class="mt-2">
                            {{ $oldestScan->issues_found }}
                        </div>

                    </div>


                    <div class="col-md-2">

                        <strong>Duration</strong>

                        <div class="mt-2">
                            {{ $oldestScan->duration }} sec
                        </div>

                    </div>


                    <div class="col-md-2">

                        <strong>Scanned</strong>

                        <div class="mt-2">
                            {{ $oldestScan->scanned_at?->format(
                                'd M Y H:i'
                            ) }}
                        </div>

                    </div>

                </div>

                @else

                <div class="alert alert-info mb-0">

                    No Microscope scan has been performed yet.

                </div>

                @endif

            </div>

        </div>


        {{-- Recent Scans --}}

        <div class="card">

            <div class="card-body">

                <div
                    class="d-flex justify-content-between align-items-center mb-3">

                    <h5 class="mb-0">
                        Recent Scans
                    </h5>

                    <a
                        href="{{ route('microscope.history') }}"
                        class="btn btn-sm btn-outline-dark">
                        View All
                    </a>

                </div>


                @if($recentScans->count())

                <div class="table-responsive">

                    <table class="table table-hover align-middle">

                        <thead>

                            <tr>

                                <th>ID</th>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Issues</th>
                                <th>Duration</th>

                            </tr>

                        </thead>

                        <tbody>

                            @foreach($recentScans as $scan)

                            <tr>

                                <td>
                                    {{ $scan->id }}
                                </td>

                                <td>
                                    {{ $scan->scanned_at?->format(
                                        'd M Y H:i:s'
                                    ) }}
                                </td>

                                <td>
                                    {{ ucfirst(str_replace(
                                        '_',
                                        ' ',
                                        $scan->scan_type
                                    )) }}
                                </td>

                                <td>

                                    @if($scan->status === 'passed')

                                    <span class="badge bg-success">
                                        Passed
                                    </span>

                                    @else

                                    <span class="badge bg-danger">
                                        Failed
                                    </span>

                                    @endif

                                </td>

                                <td>
                                    {{ $scan->issues_found }}
                                </td>

                                <td>
                                    {{ $scan->duration }} sec
                                </td>

                            </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

                <div class="mt-3">

                    {{ $recentScans->links() }}

                </div>

                @else

                <p class="text-muted mb-0">
                    No scan history available.
                </p>

                @endif

            </div>

        </div>

    </div>


    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>