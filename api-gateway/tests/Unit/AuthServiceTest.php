<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\AuthService;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\JsonResponse;

class AuthServiceTest extends TestCase
{
    public function test_login_returns_expected_response()
    {
        Http::fake([
            '*/login' => Http::response(['token' => 'fake-token'], 200)
        ]);

        $service = new AuthService();

        $response = $service->login([
            'email' => 'test@example.com',
            'password' => 'secret'
        ]);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->status());
        $this->assertEquals('fake-token', $response->getData()->token);
    }
}
