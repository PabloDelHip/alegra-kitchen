<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;

class KitchenService
{
  public function fetchRecipes(): JsonResponse
  {
      try {
          $response = Http::timeout(3)->get(config('services.kitchen.url') . '/kitchen/recipes');
          return response()->json($response->json(), $response->status());
      } catch (ConnectionException $e) {
          Log::error('❌ Error de conexión (recetas): ' . $e->getMessage());
          return response()->json(['message' => 'Microservicio no disponible.'], 503);
      } catch (RequestException $e) {
          Log::error('❌ Error HTTP al obtener recetas: ' . $e->getMessage());
          return response()->json(['message' => 'Error inesperado al consultar recetas.'], 500);
      }
  }
  
}
