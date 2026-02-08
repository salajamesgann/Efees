<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AdditionalChargeCreateTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_page_loads_successfully(): void
    {
        $this->withoutMiddleware();
        $response = $this->get(route('admin.fees.create-charge'));
        $response->assertStatus(200);
        $response->assertSee('Create Additional Charge');
    }

    public function test_store_redirects_and_saves_to_supabase_when_table_missing(): void
    {
        $this->withoutMiddleware();
        Schema::shouldReceive('hasTable')->once()->with('additional_charges')->andReturn(false);
        Http::fake([
            '*/rest/v1/additional_charges' => Http::response(['id' => 1], 201),
        ]);
        putenv('SUPABASE_URL=https://example.supabase.co');
        putenv('SUPABASE_SERVICE_KEY=test_service_key');

        $payload = [
            'charge_name' => 'Library Fee',
            'description' => 'Books and materials',
            'amount' => 500,
            'charge_type' => 'one_time',
            'school_year' => '2025-2026',
            'applies_to' => 'all',
            'required_or_optional' => 'required',
            'allow_installment' => 1,
            'include_in_total' => 1,
            'status' => 'active',
        ];

        $response = $this->post(route('admin.fees.store-charge'), $payload);
        $response->assertRedirect(route('admin.fees.index', ['tab' => 'charges']));
        $response->assertSessionHas('success');
    }

    public function test_store_shows_validation_errors_for_missing_required_fields(): void
    {
        $this->withoutMiddleware();
        $response = $this->post(route('admin.fees.store-charge'), []);
        $response->assertRedirect(route('admin.fees.create-charge'));
        $response->assertSessionHasErrors([
            'charge_name',
            'amount',
            'charge_type',
            'school_year',
            'applies_to',
            'required_or_optional',
            'status',
        ]);
    }
}
