<?php

namespace App\Http\Controllers;

use App\Models\MicroscopeScan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\Process\Process;

class MicroscopeDashboardController extends Controller
{
    public function index(): View
    {
        $latestScan = MicroscopeScan::latest('scanned_at')->first();

        $totalScans = MicroscopeScan::count();

        $successfulScans = MicroscopeScan::where(
            'status',
            'passed'
        )->count();

        $failedScans = MicroscopeScan::where(
            'status',
            'failed'
        )->count();

        $totalIssues = MicroscopeScan::sum('issues_found');

        $recentScans = MicroscopeScan::latest('scanned_at')
            ->paginate(10);

        return view('microscope.dashboard', compact(
            'latestScan',
            'totalScans',
            'successfulScans',
            'failedScans',
            'totalIssues',
            'recentScans'
        ));
    }

    public function scan(): RedirectResponse
    {
        $startedAt = microtime(true);

        $process = new Process([
            PHP_BINARY,
            base_path('artisan'),
            'check:all',
        ]);

        $process->setTimeout(300);

        $process->run();

        $duration = round(
            microtime(true) - $startedAt,
            3
        );

        $output = trim(
            $process->getOutput() . "\n" . $process->getErrorOutput()
        );

        $exitCode = $process->getExitCode();

        $issuesFound = $this->countIssues($output);

        $status = $exitCode === 0
            ? 'passed'
            : 'failed';

        MicroscopeScan::create([
            'scan_type' => 'full',
            'status' => $status,
            'exit_code' => $exitCode,
            'output' => $output,
            'duration' => $duration,
            'issues_found' => $issuesFound,
            'scanned_at' => now(),
        ]);

        if ($status === 'passed') {
            return redirect()
                ->route('microscope.dashboard')
                ->with(
                    'success',
                    'Microscope full scan completed successfully.'
                );
        }

        return redirect()
            ->route('microscope.dashboard')
            ->with(
                'error',
                'Microscope scan completed with code-quality findings.'
            );
    }

    public function history(Request $request): View
    {
        $query = MicroscopeScan::query();

        if ($request->filled('status')) {
            $query->where(
                'status',
                $request->input('status')
            );
        }

        if ($request->filled('scan_type')) {
            $query->where(
                'scan_type',
                $request->input('scan_type')
            );
        }

        $scans = $query
            ->latest('scanned_at')
            ->paginate(10)
            ->withQueryString();

        return view(
            'microscope.history',
            compact('scans')
        );
    }

    private function countIssues(string $output): int
    {
        $issues = 0;

        if (preg_match('/\b(\d+)\s+errors?\s+found\b/i', $output, $matches)) {
            $issues += (int) $matches[1];
        }

        if (preg_match('/\b(\d+)\s+wrong imports?\s+found\b/i', $output, $matches)) {
            $issues += (int) $matches[1];
        }

        if (preg_match('/\b(\d+)\s+wrong class references?\s+found\b/i', $output, $matches)) {
            $issues += (int) $matches[1];
        }

        if (preg_match('/\b(\d+)\s+extra imports?\s+found\b/i', $output, $matches)) {
            $issues += (int) $matches[1];
        }

        return $issues;
    }
}
