<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\MetricsService;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Http\Client\ConnectionException;

class MetricsServiceTest extends TestCase
{
    public function test_fetch_from_kitchen_returns_successful_response()
    {
        Http::fake([
            '*/metrics/test' => Http::response(['data' => 'ok'], 200)
        ]);

        $service = new MetricsService();

        $response = $service->fetchFromKitchen('/metrics/test');

        $this->assertEquals(200, $response->status());
        $this->assertEquals('ok', $response->getData()->data);
    }

    public function test_fetch_from_kitchen_handles_connection_exception()
    {
        Http::fake([
            '*/metrics/test' => fn () => throw new ConnectionException('Connection error')
        ]);

        $service = new MetricsService();

        $response = $service->fetchFromKitchen('/metrics/test');

        $this->assertEquals(503, $response->status());
        $this->assertEquals('El microservicio de cocina no está disponible actualmente.', $response->getData()->message);
    }

}
