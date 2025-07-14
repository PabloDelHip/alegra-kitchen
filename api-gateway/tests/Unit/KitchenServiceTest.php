<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\KitchenService;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;

class KitchenServiceTest extends TestCase
{
    public function test_fetch_recipes_successful_response()
    {
        Http::fake([
            '*' => Http::response(['data' => ['recipe1']], 200)
        ]);

        $service = new KitchenService();

        $response = $service->fetchRecipes();

        $this->assertEquals(200, $response->status());
        $this->assertEquals(['data' => ['recipe1']], $response->getData(true));
    }

    public function test_fetch_recipes_handles_connection_exception()
    {
        Http::fake(function () {
            throw new ConnectionException('Connection error.');
        });

        Log::shouldReceive('error')->once();

        $service = new KitchenService();

        $response = $service->fetchRecipes();

        $this->assertEquals(503, $response->status());
        $this->assertEquals('Microservicio no disponible.', $response->getData(true)['message']);
    }

    public function test_fetch_recipes_handles_request_exception()
{
    Http::fake([
        '*/kitchen/recipes' => Http::response(['message' => 'Error'], 500)
    ]);

    $service = new KitchenService();
    $response = $service->fetchRecipes();

    $this->assertEquals(500, $response->status());
    $this->assertEquals('Error inesperado al consultar recetas.', $response->getData()->message);
}
}
