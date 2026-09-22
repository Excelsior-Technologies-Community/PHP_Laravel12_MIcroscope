<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MicroscopeScan extends Model
{
    use HasFactory;

    protected $fillable = [
        'scan_type',
        'status',
        'exit_code',
        'output',
        'duration',
        'issues_found',
        'scanned_at',
    ];

    protected function casts(): array
    {
        return [
            'scanned_at' => 'datetime',
            'duration' => 'float',
            'issues_found' => 'integer',
            'exit_code' => 'integer',
        ];
    }
}