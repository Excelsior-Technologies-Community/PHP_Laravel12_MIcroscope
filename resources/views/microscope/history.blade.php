<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Microscope Scan History</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

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
            white-space: pre-wrap;
        }

        .filter-card {
            background: white;
        }
    </style>

</head>

<body>

    <div class="container py-4">

        {{-- Header --}}

        <div class="card p-4 mb-4">

            <div
                class="d-flex justify-content-between align-items-center flex-wrap gap-2">

                <div>

                    <h2 class="mb-1">
                        📋 Microscope Scan History
                    </h2>

                    <p class="text-muted mb-0">
                        Search, filter, export and manage previous scans.
                    </p>

                </div>

                <div class="d-flex gap-2">

                    <a
                        href="{{ route('microscope.dashboard') }}"
                        class="btn btn-dark">
                        ← Dashboard
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

            <strong>Error:</strong>

            {{ session('error') }}

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"></button>

        </div>

        @endif


        {{-- Filters --}}

        <div class="card p-4 mb-4 filter-card">

            <form
                method="GET"
                action="{{ route('microscope.history') }}">

                <div class="row g-3">

                    {{-- Search --}}

                    <div class="col-md-4">

                        <label class="form-label">
                            🔎 Search
                        </label>

                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            class="form-control"
                            placeholder="Search scan type, status or output...">

                    </div>


                    {{-- Status --}}

                    <div class="col-md-2">

                        <label class="form-label">
                            Status
                        </label>

                        <select
                            name="status"
                            class="form-select">

                            <option value="">
                                All
                            </option>

                            <option
                                value="passed"
                                @selected(request('status')==='passed' )>
                                Passed
                            </option>

                            <option
                                value="failed"
                                @selected(request('status')==='failed' )>
                                Failed
                            </option>

                        </select>

                    </div>


                    {{-- Type --}}

                    <div class="col-md-2">

                        <label class="form-label">
                            Scan Type
                        </label>

                        <select
                            name="scan_type"
                            class="form-select">

                            <option value="">
                                All
                            </option>

                            <option
                                value="full"
                                @selected(request('scan_type')==='full' )>
                                Full
                            </option>

                            <option
                                value="quality-gate"
                                @selected(request('scan_type')==='quality-gate' )>
                                Quality Gate
                            </option>

                            <option
                                value="imports"
                                @selected(request('scan_type')==='imports' )>
                                Imports
                            </option>

                            <option
                                value="routes"
                                @selected(request('scan_type')==='routes' )>
                                Routes
                            </option>

                            <option
                                value="views"
                                @selected(request('scan_type')==='views' )>
                                Views
                            </option>

                            <option
                                value="bad_practices"
                                @selected(request('scan_type')==='bad_practices' )>
                                Bad Practices
                            </option>

                            <option
                                value="dead_controllers"
                                @selected(request('scan_type')==='dead_controllers' )>
                                Dead Controllers
                            </option>

                            <option
                                value="early_returns"
                                @selected(request('scan_type')==='early_returns' )>
                                Early Returns
                            </option>

                        </select>

                    </div>


                    {{-- From --}}

                    <div class="col-md-2">

                        <label class="form-label">
                            From Date
                        </label>

                        <input
                            type="date"
                            name="from_date"
                            value="{{ request('from_date') }}"
                            class="form-control">

                    </div>


                    {{-- To --}}

                    <div class="col-md-2">

                        <label class="form-label">
                            To Date
                        </label>

                        <input
                            type="date"
                            name="to_date"
                            value="{{ request('to_date') }}"
                            class="form-control">

                    </div>

                </div>


                <div class="mt-3 d-flex gap-2">

                    <button
                        type="submit"
                        class="btn btn-dark">
                        🔎 Apply Filters
                    </button>

                    <a
                        href="{{ route('microscope.history') }}"
                        class="btn btn-outline-secondary">
                        Reset
                    </a>

                </div>

            </form>

        </div>


        {{-- Export --}}

        <div class="card p-3 mb-4">

            <div
                class="d-flex justify-content-between align-items-center flex-wrap gap-2">

                <div>

                    <strong>
                        📤 Export Reports
                    </strong>

                    <div class="text-muted small">
                        Export the currently filtered records.
                    </div>

                </div>

                <div class="d-flex gap-2">

                    <a
                        href="{{ route(
                        'microscope.export.csv',
                        request()->query()
                    ) }}"
                        class="btn btn-success">
                        📄 Export CSV
                    </a>

                    <a
                        href="{{ route(
                        'microscope.export.json',
                        request()->query()
                    ) }}"
                        class="btn btn-primary">
                        🧾 Export JSON
                    </a>

                </div>

            </div>

        </div>


        {{-- History --}}

        <div class="card">

            <div class="card-body">

                @if($scans->count())

                <form
                    method="POST"
                    action="{{ route('microscope.bulk-delete') }}"
                    id="bulkDeleteForm">

                    @csrf

                    @method('DELETE')


                    <div
                        class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">

                        <div>

                            <strong>
                                {{ $scans->total() }}
                                scan record(s)
                            </strong>

                        </div>


                        <div class="d-flex gap-2">

                            <button
                                type="submit"
                                class="btn btn-danger"
                                onclick="
                                    return confirm(
                                        'Delete selected scan records?'
                                    )
                                ">
                                🗑️ Delete Selected
                            </button>

                        </div>

                    </div>


                    <div class="table-responsive">

                        <table
                            class="table table-hover align-middle">

                            <thead>

                                <tr>

                                    <th>

                                        <input
                                            type="checkbox"
                                            id="selectAll">

                                    </th>

                                    <th>
                                        ID
                                    </th>

                                    <th>
                                        Date
                                    </th>

                                    <th>
                                        Type
                                    </th>

                                    <th>
                                        Status
                                    </th>

                                    <th>
                                        Issues
                                    </th>

                                    <th>
                                        Duration
                                    </th>

                                    <th>
                                        Exit Code
                                    </th>

                                    <th>
                                        Actions
                                    </th>

                                </tr>

                            </thead>


                            <tbody>

                                @foreach($scans as $scan)

                                <tr>

                                    <td>

                                        <input
                                            type="checkbox"
                                            name="scan_ids[]"
                                            value="{{ $scan->id }}"
                                            class="scan-checkbox">

                                    </td>


                                    <td>
                                        {{ $scan->id }}
                                    </td>


                                    <td>
                                        {{ $scan->scanned_at?->format(
                                            'd M Y H:i:s'
                                        ) }}
                                    </td>


                                    <td>

                                        <span class="badge bg-secondary">

                                            {{ ucfirst(
                                                str_replace(
                                                    '_',
                                                    ' ',
                                                    $scan->scan_type
                                                )
                                            ) }}

                                        </span>

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

                                        @if($scan->issues_found > 0)

                                        <span class="badge bg-warning text-dark">
                                            {{ $scan->issues_found }}
                                        </span>

                                        @else

                                        <span class="badge bg-success">
                                            0
                                        </span>

                                        @endif

                                    </td>


                                    <td>
                                        {{ $scan->duration }} sec
                                    </td>


                                    <td>
                                        {{ $scan->exit_code }}
                                    </td>


                                    <td>

                                        <div class="d-flex gap-1">

                                            {{-- Rerun --}}

                                            <form
                                                method="POST"
                                                action="{{ route(
                                                    'microscope.rerun',
                                                    $scan
                                                ) }}">

                                                @csrf

                                                <button
                                                    type="submit"
                                                    class="btn btn-sm btn-outline-primary"
                                                    title="Re-run">
                                                    🔁
                                                </button>

                                            </form>


                                            {{-- Delete --}}

                                            <form
                                                method="POST"
                                                action="{{ route(
                                                    'microscope.destroy',
                                                    $scan
                                                ) }}"
                                                onsubmit="
                                                    return confirm(
                                                        'Delete this scan?'
                                                    )
                                                ">

                                                @csrf

                                                @method('DELETE')

                                                <button
                                                    type="submit"
                                                    class="btn btn-sm btn-outline-danger"
                                                    title="Delete">
                                                    🗑️
                                                </button>

                                            </form>

                                        </div>

                                    </td>

                                </tr>


                                {{-- Output --}}

                                <tr>

                                    <td colspan="9">

                                        <details>

                                            <summary>
                                                🔍 View Microscope Output
                                            </summary>

                                            <pre class="mt-3">{{ $scan->output }}</pre>

                                        </details>

                                    </td>

                                </tr>

                                @endforeach

                            </tbody>

                        </table>

                    </div>

                </form>


                {{-- Pagination --}}

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


    <script>
        document
            .getElementById('selectAll')
            ?.addEventListener('change', function() {

                document
                    .querySelectorAll('.scan-checkbox')
                    .forEach(function(checkbox) {

                        checkbox.checked =
                            this.checked;

                    }, this);

            });
    </script>

</body>

</html>