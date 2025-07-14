<?php

namespace App\Services;

use App\Repositories\PurchaseHistoryRepository;
use App\Repositories\IngredientRepository;
use Illuminate\Support\Facades\Http;

class PurchaseHistoryService
{
    public function __construct(
        protected PurchaseHistoryRepository $purchaseRepo,
        protected IngredientRepository $ingredientRepo
    ) {}

    public function getDailySummary(string $date)
    {
        return $this->purchaseRepo->getSummaryByDate($date);
    }

    public function buyFromMarket(string $name): array
    {
        $baseUrl = env('FARMERS_MARKET_API', 'https://recruitment.alegra.com/api/farmers-market');

        $response = Http::get($baseUrl . '/buy', ['ingredient' => $name]);
        $quantitySold = $response->json('quantitySold') ?? 0;

        if ($quantitySold > 0) {
            $ingredient = $this->ingredientRepo->createOrIncrement($name, $quantitySold);
            $this->purchaseRepo->create($ingredient->id, $quantitySold);
        }

        return [
            'ingredient' => $name,
            'quantityBought' => $quantitySold
        ];
    }
}
