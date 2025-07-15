<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class AgentService
{
    public function recommendations()
    {
        $response = Http::get(config('services.agent.url') . '/recommendations');
        return response()->json($response->json(), $response->status());
    }
}
