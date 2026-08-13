<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_required_api_routes_are_registered(): void
    {
        $this->assertTrue(Route::has('leads.store'));
        $this->assertTrue(Route::has('leads.calls.store'));
        $this->assertTrue(Route::has('managers.leads.index'));
    }
}
