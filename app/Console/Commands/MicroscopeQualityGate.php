<?php

namespace App\Console\Commands;

use App\Models\MicroscopeScan;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class MicroscopeQualityGate extends Command
{
    protected $signature = 'microscope:quality-gate';

    protected $description = 'Run a full Laravel Microscope quality scan and enforce the project quality gate';

    public function handle(): int
    {
        $this->newLine();

        $this->info('==============================================');
        $this->info('      Laravel Microscope Quality Gate');
        $this->info('==============================================');

        $this->newLine();

        $startedAt = microtime(true);

        $this->info('Running: php artisan check:all');

        $exitCode = Artisan::call('check:all');

        $output = Artisan::output();

        $duration = round(microtime(true) - $startedAt, 3);

        $issuesFound = $this->countIssues($output);

        $status = $exitCode === 0 ? 'passed' : 'failed';

        MicroscopeScan::create([
            'scan_type' => 'quality-gate',
            'status' => $status,
            'exit_code' => $exitCode,
            'output' => $output,
            'duration' => $duration,
            'issues_found' => $issuesFound,
            'scanned_at' => now(),
        ]);

        $this->newLine();

        $this->line('Scan Duration: ' . $duration . ' seconds');
        $this->line('Detected Issues: ' . $issuesFound);
        $this->line('Exit Code: ' . $exitCode);

        $this->newLine();

        if ($exitCode === 0) {
            $this->info('QUALITY GATE PASSED');

            $this->line('The project passed the Microscope full scan.');

            $this->newLine();

            return self::SUCCESS;
        }

        $this->error('QUALITY GATE FAILED');

        $this->line('The project contains Microscope-detected issues.');

        $this->newLine();

        $this->warn('Review the Microscope output before committing your changes.');

        $this->newLine();

        return self::FAILURE;
    }

    /**
     * Count likely issue messages from Microscope output.
     */
    private function countIssues(string $output): int
    {
        $patterns = [
            '/\b\d+\s+(?:error|errors)\b/i',
            '/\b\d+\s+(?:issue|issues)\b/i',
            '/\b\d+\s+(?:warning|warnings)\b/i',
            '/\b(?:error|errors):/i',
            '/\b(?:issue|issues):/i',
            '/\b(?:warning|warnings):/i',
            '/method does not exist/i',
            '/unused import/i',
            '/extra import/i',
            '/dead controller/i',
            '/env\(\) used outside config/i',
        ];

        $count = 0;

        foreach ($patterns as $pattern) {
            preg_match_all($pattern, $output, $matches);

            $count += count($matches[0]);
        }

        return $count;
    }
}