<?php

namespace App\Exceptions;

use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RenderException
{
    public function __invoke(Request $request, \Throwable $e): Response
    {
        if ($e instanceof ThrottleRequestsException) {
            return response()->json([
                'message' => 'Demasiadas solicitudes. Por favor, intenta más tarde.',
                'code' => 429
            ], 429);
        }

        throw $e;
    }
}
