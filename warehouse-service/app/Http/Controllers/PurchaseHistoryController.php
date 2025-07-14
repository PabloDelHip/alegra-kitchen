<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PurchaseHistoryService;

class PurchaseHistoryController extends Controller
{
    public function __construct(
        protected PurchaseHistoryService $purchaseService
    ) {}

    public function index(Request $request)
    {
        $date = $request->input('date', now()->toDateString());

        $summary = $this->purchaseService->getDailySummary($date);

        return response()->json([
            'date' => $date,
            'total' => $summary->count(),
            'data' => $summary
        ]);
    }
}
