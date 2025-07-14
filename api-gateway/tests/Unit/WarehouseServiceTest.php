<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\WarehouseService;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\ConnectionException;

class WarehouseServiceTest extends TestCase
{
    public function test_get_returns_expected_response()
    {
        Http::fake([
            '*/ingredients' => Http::response(['data' => ['stock' => 10]], 200)
        ]);

        $service = new WarehouseService();

        $result = $service->get('/ingredients');

        $this->assertEquals(200, $result['status']);
        $this->assertEquals(['stock' => 10], $result['data']['data']);
    }

    public function test_get_handles_connection_exception()
    {
        Http::fake([
            '*/ingredients' => function () {
                throw new ConnectionException('Connection error');
            }
        ]);

        $this->expectException(ConnectionException::class);

        $service = new WarehouseService();
        $service->get('/ingredients');
    }


    public function test_post_returns_expected_response()
    {
        Http::fake([
            '*/ingredients/buy' => Http::response(['data' => ['purchased' => true]], 201)
        ]);

        $service = new WarehouseService();

        $result = $service->post('/ingredients/buy', ['name' => 'tomato']);

        $this->assertEquals(201, $result['status']);
        $this->assertEquals(['purchased' => true], $result['data']['data']);
    }

    public function test_post_handles_connection_exception()
    {
        Http::fake([
            '*/ingredients/buy' => function () {
                throw new ConnectionException('Connection error');
            }
        ]);

        $this->expectException(ConnectionException::class);

        $service = new WarehouseService();
        $service->post('/ingredients/buy', ['name' => 'tomato']);
    }
}
