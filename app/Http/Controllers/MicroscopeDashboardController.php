<?php

namespace App\Http\Controllers;

use App\Models\MicroscopeScan;
use App\Services\MicroscopeAnalysisService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Symfony\Component\Process\Process;

class MicroscopeDashboardController extends Controller
{
    /**
     * Allowed Microscope checks.
     */
    private array $allowedChecks = [
        'check:all' => 'Full Scan',
        'check:imports' => 'Imports',
        'check:routes' => 'Routes',
        'check:views' => 'Views',
        'check:bad_practices' => 'Bad Practices',
        'check:dead_controllers' => 'Dead Controllers',
        'check:early_returns' => 'Early Returns',
    ];

    /**
     * Dashboard.
     */
    public function index(): View
    {
        $oldestScan = MicroscopeScan::oldest('scanned_at')
            ->first();

        $totalScans = MicroscopeScan::count();

        $successfulScans = MicroscopeScan::where(
            'status',
            'passed'
        )->count();

        $failedScans = MicroscopeScan::where(
            'status',
            'failed'
        )->count();

        $totalIssues = MicroscopeScan::sum(
            'issues_found'
        );

        $qualityScore = $totalScans > 0
            ? round(
                ($successfulScans / $totalScans) * 100
            )
            : 0;

        $recentScans = MicroscopeScan::oldest(
            'scanned_at'
        )->paginate(5);

        return view(
            'microscope.dashboard',
            compact(
                'oldestScan',
                'totalScans',
                'successfulScans',
                'failedScans',
                'totalIssues',
                'qualityScore',
                'recentScans'
            )
        );
    }

    /**
     * Run full Microscope scan.
     */
    public function scan(): RedirectResponse
    {
        return $this->runMicroscopeCheck(
            'check:all',
            'full'
        );
    }

    /**
     * Run individual Microscope check.
     */
    public function runCheck(
        Request $request
    ): RedirectResponse {
        $request->validate([
            'check' => [
                'required',
                'string',
                'in:' . implode(
                    ',',
                    array_keys($this->allowedChecks)
                ),
            ],
        ]);

        $check = $request->input('check');

        $scanType = str_replace(
            'check:',
            '',
            $check
        );

        return $this->runMicroscopeCheck(
            $check,
            $scanType
        );
    }

    /**
     * Execute Microscope command.
     */
    private function runMicroscopeCheck(
        string $command,
        string $scanType
    ): RedirectResponse {
        $startedAt = microtime(true);

        $process = new Process([
            PHP_BINARY,
            base_path('artisan'),
            $command,
        ]);

        $process->setTimeout(300);

        $process->run();

        $duration = round(
            microtime(true) - $startedAt,
            3
        );

        $output = trim(
            $process->getOutput()
            . "\n"
            . $process->getErrorOutput()
        );

        $exitCode = $process->getExitCode();

        $issuesFound = $this->countIssues(
            $output
        );

        $status = $exitCode === 0
            ? 'passed'
            : 'failed';

        MicroscopeScan::create([
            'scan_type' => $scanType,
            'status' => $status,
            'exit_code' => $exitCode,
            'output' => $output,
            'duration' => $duration,
            'issues_found' => $issuesFound,
            'scanned_at' => now(),
        ]);

        $checkName = $this->allowedChecks[$command]
            ?? ucfirst($scanType);

        if ($status === 'passed') {
            return redirect()
                ->route('microscope.dashboard')
                ->with(
                    'success',
                    $checkName .
                    ' check completed successfully.'
                );
        }

        return redirect()
            ->route('microscope.dashboard')
            ->with(
                'error',
                $checkName .
                ' check completed with findings.'
            );
    }

    /**
     * Scan history with search and filters.
     */
    public function history(
        Request $request
    ): View {
        $query = MicroscopeScan::query();

        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {
            $search = $request->input('search');

            $query->where(function ($q) use ($search) {
                $q->where(
                    'scan_type',
                    'like',
                    "%{$search}%"
                )
                    ->orWhere(
                        'status',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhere(
                        'output',
                        'like',
                        "%{$search}%"
                    );
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Status filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('status')) {
            $query->where(
                'status',
                $request->input('status')
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Scan type filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('scan_type')) {
            $query->where(
                'scan_type',
                $request->input('scan_type')
            );
        }

        /*
        |--------------------------------------------------------------------------
        | From date
        |--------------------------------------------------------------------------
        */

        if ($request->filled('from_date')) {
            $query->whereDate(
                'scanned_at',
                '>=',
                $request->input('from_date')
            );
        }

        /*
        |--------------------------------------------------------------------------
        | To date
        |--------------------------------------------------------------------------
        */

        if ($request->filled('to_date')) {
            $query->whereDate(
                'scanned_at',
                '<=',
                $request->input('to_date')
            );
        }

        $scans = $query
            ->oldest('scanned_at')
            ->paginate(5)
            ->withQueryString();

        return view(
            'microscope.history',
            compact('scans')
        );
    }

    /**
     * Delete one scan.
     */
    public function destroy(
        MicroscopeScan $scan
    ): RedirectResponse {
        $scan->delete();

        return back()->with(
            'success',
            'Scan record deleted successfully.'
        );
    }

    /**
     * Bulk delete scans.
     */
    public function bulkDelete(
        Request $request
    ): RedirectResponse {
        $request->validate([
            'scan_ids' => [
                'required',
                'array',
            ],
            'scan_ids.*' => [
                'integer',
                'exists:microscope_scans,id',
            ],
        ]);

        $count = MicroscopeScan::whereIn(
            'id',
            $request->input('scan_ids')
        )->delete();

        return back()->with(
            'success',
            $count . ' scan record(s) deleted successfully.'
        );
    }

    /**
     * Re-run an existing scan.
     */
    public function rerun(
        MicroscopeScan $scan
    ): RedirectResponse {
        $command = $this->commandFromScanType(
            $scan->scan_type
        );

        if (!$command) {
            return back()->with(
                'error',
                'This scan type cannot be re-run.'
            );
        }

        return $this->runMicroscopeCheck(
            $command,
            $scan->scan_type
        );
    }

    /**
     * Export history as CSV.
     */
    public function exportCsv(
        Request $request
    ): Response {
        $scans = $this->filteredQuery(
            $request
        )->oldest('scanned_at')->get();

        $filename =
            'microscope-scan-history-' .
            now()->format('Y-m-d-H-i-s') .
            '.csv';

        $handle = fopen(
            'php://temp',
            'w+'
        );

        fputcsv($handle, [
            'ID',
            'Date',
            'Scan Type',
            'Status',
            'Issues',
            'Duration',
            'Exit Code',
            'Output',
        ]);

        foreach ($scans as $scan) {
            fputcsv($handle, [
                $scan->id,
                $scan->scanned_at?->format(
                    'Y-m-d H:i:s'
                ),
                $scan->scan_type,
                $scan->status,
                $scan->issues_found,
                $scan->duration,
                $scan->exit_code,
                $scan->output,
            ]);
        }

        rewind($handle);

        $csv = stream_get_contents($handle);

        fclose($handle);

        return response(
            $csv,
            200,
            [
                'Content-Type' =>
                    'text/csv; charset=UTF-8',

                'Content-Disposition' =>
                    'attachment; filename="' .
                    $filename .
                    '"',
            ]
        );
    }

    /**
     * Export history as JSON.
     */
    public function exportJson(
        Request $request
    ): Response {
        $scans = $this->filteredQuery(
            $request
        )->oldest('scanned_at')->get();

        $filename =
            'microscope-scan-history-' .
            now()->format('Y-m-d-H-i-s') .
            '.json';

        return response(
            $scans->toJson(
                JSON_PRETTY_PRINT
                | JSON_UNESCAPED_SLASHES
            ),
            200,
            [
                'Content-Type' =>
                    'application/json; charset=UTF-8',

                'Content-Disposition' =>
                    'attachment; filename="' .
                    $filename .
                    '"',
            ]
        );
    }

    /**
     * Rebuild filtered query for exports.
     */
    private function filteredQuery(
        Request $request
    ) {
        $query = MicroscopeScan::query();

        if ($request->filled('search')) {
            $search = $request->input('search');

            $query->where(function ($q) use ($search) {
                $q->where(
                    'scan_type',
                    'like',
                    "%{$search}%"
                )
                    ->orWhere(
                        'status',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhere(
                        'output',
                        'like',
                        "%{$search}%"
                    );
            });
        }

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

        if ($request->filled('from_date')) {
            $query->whereDate(
                'scanned_at',
                '>=',
                $request->input('from_date')
            );
        }

        if ($request->filled('to_date')) {
            $query->whereDate(
                'scanned_at',
                '<=',
                $request->input('to_date')
            );
        }

        return $query;
    }

    /**
     * Convert scan type back to Artisan command.
     */
    private function commandFromScanType(
        string $scanType
    ): ?string {
        return match ($scanType) {
            'full' => 'check:all',
            'imports' => 'check:imports',
            'routes' => 'check:routes',
            'views' => 'check:views',
            'bad_practices' => 'check:bad_practices',
            'dead_controllers' => 'check:dead_controllers',
            'early_returns' => 'check:early_returns',
            default => null,
        };
    }

    /**
     * Count likely issues.
     */
    private function countIssues(
        string $output
    ): int {
        $patterns = [
            '/\b(\d+)\s+errors?\s+found\b/i',
            '/\b(\d+)\s+wrong imports?\s+found\b/i',
            '/\b(\d+)\s+wrong class references?\s+found\b/i',
            '/\b(\d+)\s+extra imports?\s+found\b/i',
            '/\b(\d+)\s+issues?\s+found\b/i',
            '/\b(\d+)\s+warnings?\s+found\b/i',
        ];

        $issues = 0;

        foreach ($patterns as $pattern) {
            if (
                preg_match(
                    $pattern,
                    $output,
                    $matches
                )
            ) {
                $issues += (int) $matches[1];
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Fallback issue detection
        |--------------------------------------------------------------------------
        */

        if ($issues !== 0){

        return $issues;
    } 
            $fallbackPatterns = [
                '/unused import/i',
                '/extra import/i',
                '/dead controller/i',
                '/method does not exist/i',
                '/env\(\) used outside config/i',
            ];

            foreach ($fallbackPatterns as $pattern) {
                preg_match_all(
                    $pattern,
                    $output,
                    $matches
                );

                $issues += count($matches[0]);
            }
        

        return $issues;
    }

    /**
     * Display Static Analysis & Pint Auto-Fixer UI.
     */
    public function staticAnalysis(Request $request, MicroscopeAnalysisService $service)
    {
        $level = (int)$request->input('level', 0);
        $stanResults = session('stan_results', $service->runLarastanScan($level));
        $debugStatements = $service->scanDebugStatements();

        return view('microscope.static_analysis', compact('stanResults', 'debugStatements', 'level'));
    }

    /**
     * Run Larastan / PHPStan scan.
     */
    public function runLarastan(Request $request, MicroscopeAnalysisService $service)
    {
        $level = (int)$request->input('level', 0);
        $results = $service->runLarastanScan($level);

        return redirect()->route('microscope.static-analysis', ['level' => $level])
            ->with('stan_results', $results)
            ->with('success', "Larastan static type check completed at Level {$level}. Found {$results['total_errors']} issue(s).");
    }

    /**
     * Run Laravel Pint code formatter.
     */
    public function runPint(Request $request, MicroscopeAnalysisService $service)
    {
        $dryRun = $request->has('dry_run');
        $result = $service->runPintFixer($dryRun);

        return redirect()->route('microscope.static-analysis')
            ->with('success', $dryRun ? "Laravel Pint dry-run test completed." : "Laravel Pint code auto-formatter executed successfully!");
    }

    /**
     * Clean leftover debug statements.
     */
    public function cleanDebug(MicroscopeAnalysisService $service)
    {
        $cleaned = $service->cleanDebugStatements();

        return redirect()->route('microscope.static-analysis')
            ->with('success', "Successfully cleaned {$cleaned} leftover debug statement(s) from code!");
    }

    /**
     * Display Security Audit UI.
     */
    public function securityAudit(MicroscopeAnalysisService $service)
    {
        $composerAudit = session('composer_audit', $service->runComposerSecurityAudit());
        $securitySmells = $service->scanSecuritySmells();

        return view('microscope.security_audit', compact('composerAudit', 'securitySmells'));
    }

    /**
     * Run composer security audit.
     */
    public function runSecurityAudit(MicroscopeAnalysisService $service)
    {
        $result = $service->runComposerSecurityAudit();

        return redirect()->route('microscope.security-audit')
            ->with('composer_audit', $result)
            ->with('success', 'Composer security audit completed.');
    }

    /**
     * Display Performance & Dead Asset Analyzer UI.
     */
    public function performanceAnalyzer(MicroscopeAnalysisService $service)
    {
        $analysis = $service->analyzeDeadAssets();

        return view('microscope.performance_analyzer', compact('analysis'));
    }

    /**
     * Run performance scan.
     */
    public function scanPerformance(MicroscopeAnalysisService $service)
    {
        $analysis = $service->analyzeDeadAssets();

        return redirect()->route('microscope.performance-analyzer')
            ->with('success', 'Performance and dead assets scan completed.');
    }
}