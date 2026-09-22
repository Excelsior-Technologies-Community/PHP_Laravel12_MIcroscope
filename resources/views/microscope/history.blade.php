<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Microscope Scan History</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <style>

        body {
            background: #f5f7fb;
        }

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.06);
        }

        pre {
            background: #111827;
            color: #e5e7eb;
            padding: 15px;
            border-radius: 8px;
            max-height: 300px;
            overflow: auto;
        }

    </style>

</head>

<body>

<div class="container py-4">

    {{-- Header --}}

    <div class="card p-4 mb-4">

        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">

            <div>

                <h2 class="mb-1">
                    📋 Microscope Scan History
                </h2>

                <p class="text-muted mb-0">
                    Review previous Laravel code-quality scans.
                </p>

            </div>

            <a
                href="{{ route('microscope.dashboard') }}"
                class="btn btn-dark"
            >
                ← Dashboard
            </a>

        </div>

    </div>


    {{-- Filters --}}

    <div class="card p-4 mb-4">

        <form
            method="GET"
            action="{{ route('microscope.history') }}"
        >

            <div class="row g-3">

                <div class="col-md-4">

                    <label class="form-label">
                        Status
                    </label>

                    <select
                        name="status"
                        class="form-select"
                    >

                        <option value="">
                            All Statuses
                        </option>

                        <option
                            value="passed"
                            @selected(request('status') === 'passed')
                        >
                            Passed
                        </option>

                        <option
                            value="failed"
                            @selected(request('status') === 'failed')
                        >
                            Failed
                        </option>

                    </select>

                </div>


                <div class="col-md-4">

                    <label class="form-label">
                        Scan Type
                    </label>

                    <select
                        name="scan_type"
                        class="form-select"
                    >

                        <option value="">
                            All Types
                        </option>

                        <option
                            value="full"
                            @selected(request('scan_type') === 'full')
                        >
                            Full Scan
                        </option>

                        <option
                            value="quality-gate"
                            @selected(request('scan_type') === 'quality-gate')
                        >
                            Quality Gate
                        </option>

                    </select>

                </div>


                <div class="col-md-4 d-flex align-items-end gap-2">

                    <button
                        type="submit"
                        class="btn btn-dark"
                    >
                        🔎 Filter
                    </button>

                    <a
                        href="{{ route('microscope.history') }}"
                        class="btn btn-outline-secondary"
                    >
                        Reset
                    </a>

                </div>

            </div>

        </form>

    </div>


    {{-- History --}}

    <div class="card">

        <div class="card-body">

            @if($scans->count())

                <div class="table-responsive">

                    <table class="table table-hover align-middle">

                        <thead>

                        <tr>

                            <th>#</th>

                            <th>Date</th>

                            <th>Type</th>

                            <th>Status</th>

                            <th>Issues</th>

                            <th>Duration</th>

                            <th>Exit Code</th>

                        </tr>

                        </thead>

                        <tbody>

                        @foreach($scans as $scan)

                            <tr>

                                <td>
                                    {{ $scan->id }}
                                </td>

                                <td>
                                    {{ $scan->scanned_at?->format('d M Y H:i:s') }}
                                </td>

                                <td>

                                    @if($scan->scan_type === 'quality-gate')

                                        <span class="badge bg-dark">
                                            Quality Gate
                                        </span>

                                    @else

                                        <span class="badge bg-primary">
                                            Full Scan
                                        </span>

                                    @endif

                                </td>

                                <td>

                                    @if($scan->status === 'passed')

                                        <span class="badge bg-success">
                                            PASSED
                                        </span>

                                    @else

                                        <span class="badge bg-danger">
                                            FAILED
                                        </span>

                                    @endif

                                </td>

                                <td>
                                    {{ $scan->issues_found }}
                                </td>

                                <td>
                                    {{ $scan->duration }} sec
                                </td>

                                <td>
                                    {{ $scan->exit_code }}
                                </td>

                            </tr>

                            <tr>

                                <td colspan="7">

                                    <details>

                                        <summary>
                                            View Microscope Output
                                        </summary>

                                        <pre class="mt-3">{{ $scan->output }}</pre>

                                    </details>

                                </td>

                            </tr>

                        @endforeach

                        </tbody>

                    </table>

                </div>

                <div class="mt-3">

                    {{ $scans->links() }}

                </div>

            @else

                <div class="alert alert-info mb-0">

                    No Microscope scan records found.

                </div>

            @endif

        </div>

    </div>

</div>

</body>

</html>