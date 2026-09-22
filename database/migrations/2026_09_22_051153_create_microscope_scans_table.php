<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('microscope_scans', function (Blueprint $table) {
            $table->id();

            $table->string('scan_type')->default('full');

            $table->string('status');

            $table->integer('exit_code')->default(0);

            $table->longText('output')->nullable();

            $table->decimal('duration', 10, 3)->default(0);

            $table->unsignedInteger('issues_found')->default(0);

            $table->timestamp('scanned_at')->nullable();

            $table->timestamps();

            $table->index('status');
            $table->index('scan_type');
            $table->index('scanned_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('microscope_scans');
    }
};