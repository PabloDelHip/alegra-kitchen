<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\OrderService;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\ConnectionException;

class OrderServiceTest extends TestCase
{
    public function test_post_order_returns_expected_response()
    {
        Http::fake([
            '*/kitchen/orders' => Http::response(['success' => true], 201)
        ]);

        $service = new OrderService();

        $result = $service->postOrder(['quantity' => 1]);

        $this->assertEquals(201, $result['status']);
        $this->assertTrue($result['data']['success']);
    }

    public function test_post_order_handles_connection_exception()
    {
        Http::fake([
            '*/kitchen/orders' => function () {
                throw new ConnectionException('Connection error');
            }
        ]);

        $this->expectException(ConnectionException::class);

        $service = new OrderService();
        $service->postOrder(['quantity' => 1]);
    }

    public function test_get_kitchen_data_handles_request_exception()
    {
        Http::fake([
            '*/kitchen/orders/dishes' => Http::response(['error' => 'Bad Request'], 400)
        ]);
    
        $this->expectException(RequestException::class);
    
        $service = new OrderService();
        $service->getKitchenData('/kitchen/orders/dishes');
    }
}
