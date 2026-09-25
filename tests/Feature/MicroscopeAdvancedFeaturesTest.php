<?php

namespace Tests\Feature;

use App\Services\MicroscopeAnalysisService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MicroscopeAdvancedFeaturesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_render_static_analysis_page()
    {
        $response = $this->get(route('microscope.static-analysis'));

        $response->assertStatus(200);
        $response->assertSee('Larastan & Pint Auto-Fixer Studio', false);
    }

    /** @test */
    public function it_can_run_larastan_static_analysis_scan()
    {
        $response = $this->post(route('microscope.larastan.run'), [
            'level' => 1,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
    }

    /** @test */
    public function it_can_run_pint_code_formatter()
    {
        $response = $this->post(route('microscope.pint.fix'), [
            'dry_run' => 1,
        ]);

        $response->assertRedirect(route('microscope.static-analysis'));
        $response->assertSessionHas('success');
    }

    /** @test */
    public function it_can_render_security_audit_page()
    {
        $response = $this->get(route('microscope.security-audit'));

        $response->assertStatus(200);
        $response->assertSee('Security & Vulnerability Audit Studio', false);
    }

    /** @test */
    public function it_can_run_composer_security_audit()
    {
        $response = $this->post(route('microscope.security-audit.run'));

        $response->assertRedirect(route('microscope.security-audit'));
        $response->assertSessionHas('composer_audit');
    }

    /** @test */
    public function it_can_render_performance_analyzer_page()
    {
        $response = $this->get(route('microscope.performance-analyzer'));

        $response->assertStatus(200);
        $response->assertSee('Performance & Dead Asset Intelligence Studio', false);
    }

    /** @test */
    public function service_returns_valid_structure_for_security_smells_and_dead_assets()
    {
        $service = new MicroscopeAnalysisService();

        $smells = $service->scanSecuritySmells();
        $this->assertIsArray($smells);

        $deadAssets = $service->analyzeDeadAssets();
        $this->assertArrayHasKey('unused_views', $deadAssets);
        $this->assertArrayHasKey('n_plus_one_risks', $deadAssets);
    }
}
