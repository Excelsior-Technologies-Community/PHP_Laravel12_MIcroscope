<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Microscope Quality Dashboard</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <style>

        body {
            background: #f5f7fb;
        }

        .dashboard-header {
            background: #212529;
            color: white;
            border-radius: 12px;
        }

        .stat-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.06);
        }

        .stat-number {
            font-size: 30px;
            font-weight: 700;
        }

        .scan-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.06);
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

        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">

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
                    action="{{ route('microscope.scan') }}"
                >

                    @csrf

                    <button
                        type="submit"
                        class="btn btn-light"
                    >
                        🔍 Run Full Scan
                    </button>

                </form>

                <a
                    href="{{ route('microscope.history') }}"
                    class="btn btn-outline-light"
                >
                    📋 Scan History
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
                data-bs-dismiss="alert"
            ></button>

        </div>

    @endif


    @if(session('error'))

        <div class="alert alert-danger alert-dismissible fade show">

            <strong>Scan Warning:</strong>
            {{ session('error') }}

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
            ></button>

        </div>

    @endif


    {{-- Statistics --}}

    <div class="row g-4 mb-4">

        <div class="col-md-3">

            <div class="card stat-card">

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

            <div class="card stat-card">

                <div class="card-body">

                    <h6 class="text-muted">
                        Passed Scans
                    </h6>

                    <div class="stat-number text-success">
                        {{ $successfulScans }}
                    </div>

                </div>

            </div>

        </div>


        <div class="col-md-3">

            <div class="card stat-card">

                <div class="card-body">

                    <h6 class="text-muted">
                        Failed Scans
                    </h6>

                    <div class="stat-number text-danger">
                        {{ $failedScans }}
                    </div>

                </div>

            </div>

        </div>


        <div class="col-md-3">

            <div class="card stat-card">

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


    {{-- Latest Scan --}}

    <div class="card scan-card mb-4">

        <div class="card-body">

            <h5 class="mb-3">
                Latest Microscope Scan
            </h5>

            @if($latestScan)

                <div class="row g-3">

                    <div class="col-md-3">

                        <strong>Status</strong>

                        <div class="mt-2">

                            @if($latestScan->status === 'passed')

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

                        <strong>Scan Type</strong>

                        <div class="mt-2">
                            {{ ucfirst($latestScan->scan_type) }}
                        </div>

                    </div>


                    <div class="col-md-2">

                        <strong>Issues</strong>

                        <div class="mt-2">
                            {{ $latestScan->issues_found }}
                        </div>

                    </div>


                    <div class="col-md-2">

                        <strong>Duration</strong>

                        <div class="mt-2">
                            {{ $latestScan->duration }} sec
                        </div>

                    </div>


                    <div class="col-md-2">

                        <strong>Scanned</strong>

                        <div class="mt-2">
                            {{ $latestScan->scanned_at?->format('d M Y H:i') }}
                        </div>

                    </div>

                </div>

            @else

                <div class="alert alert-info mb-0">

                    No Microscope scan has been performed yet.

                    Click <strong>Run Full Scan</strong> to start.

                </div>

            @endif

        </div>

    </div>


    {{-- Recent Scans --}}

    <div class="card scan-card">

        <div class="card-body">

            <div class="d-flex justify-content-between align-items-center mb-3">

                <h5 class="mb-0">
                    Recent Scans
                </h5>

                <a
                    href="{{ route('microscope.history') }}"
                    class="btn btn-sm btn-outline-dark"
                >
                    View All
                </a>

            </div>


            @if($recentScans->count())

                <div class="table-responsive">

                    <table class="table table-hover align-middle">

                        <thead>

                        <tr>

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
                                    {{ $scan->scanned_at?->format('d M Y H:i:s') }}
                                </td>

                                <td>
                                    {{ ucfirst($scan->scan_type) }}
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

                {{ $recentScans->links() }}

            @else

                <p class="text-muted mb-0">
                    No scan history available.
                </p>

            @endif

        </div>

    </div>

</div>


<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
</script>

</body>

</html>