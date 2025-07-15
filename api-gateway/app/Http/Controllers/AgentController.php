<?php

namespace App\Http\Controllers;

use App\Services\AgentService;
use Illuminate\Http\Request;

class AgentController extends Controller
{
    public function __construct(protected AgentService $agentService) {}

    public function recommendations()
    {
        return $this->agentService->recommendations();
    }
}
