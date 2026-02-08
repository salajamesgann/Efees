<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AdditionalChargeAjaxTest extends TestCase
{
    public function test_ajax_store_returns_201_and_json(): void
    {
        $this->withoutMiddleware();
        Schema::shouldReceive('hasTable')->once()->with('additional_charges')->andReturn(true);
        $payload = [
            'charge_name' => 'Exam Fee',
            'description' => 'Periodic exams',
            'amount' => 250,
            'charge_type' => 'one_time',
            'school_year' => '2025-2026',
            'applies_to' => 'all',
            'required_or_optional' => 'required',
            'status' => 'active',
        ];

        $response = $this->postJson(route('admin.fees.store-charge'), $payload);
        $response->assertStatus(201);
        $response->assertJsonFragment(['charge_name' => 'Exam Fee']);
        $response->assertJsonStructure(['id', 'charge_name', 'amount', 'charge_type', 'created_at']);
    }

    public function test_ajax_store_returns_422_on_validation_error(): void
    {
        $this->withoutMiddleware();
        $response = $this->postJson(route('admin.fees.store-charge'), []);
        $response->assertStatus(422);
        $response->assertJsonStructure(['errors']);
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertArrayHasKey('charge_name', $response->json('errors'));
    }

    public function test_ajax_store_succeeds_without_external_config(): void
    {
        $this->withoutMiddleware();
        Schema::shouldReceive('hasTable')->once()->with('additional_charges')->andReturn(true);
        $payload = [
            'charge_name' => 'Facility Fee',
            'description' => 'Facility maintenance',
            'amount' => 100,
            'charge_type' => 'recurring',
            'school_year' => '2025-2026',
            'applies_to' => 'all',
            'required_or_optional' => 'optional',
            'status' => 'active',
        ];

        $response = $this->postJson(route('admin.fees.store-charge'), $payload);
        $response->assertStatus(201);
        $response->assertJsonFragment(['charge_type' => 'recurring']);
    }
}
