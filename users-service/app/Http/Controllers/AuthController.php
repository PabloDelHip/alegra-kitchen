<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Exception;
use Throwable;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            $credentials = $request->only('email', 'password');

            if (!$token = auth('api')->attempt($credentials)) {
                return response()->json(['error' => 'Credenciales inválidas'], 401);
            }

            return response()->json([
                'access_token' => $token,
                'token_type' => 'Bearer',
                'expires_in' => auth('api')->factory()->getTTL() * 60
            ]);
        } catch (ValidationException $e) {
            logger()->warning('Error de validación: ' . $e->getMessage());
            return response()->json(['error' => 'Datos inválidos'], 422);
        } catch (QueryException $e) {
            logger()->error('Error de base de datos: ' . $e->getMessage());
            return response()->json(['error' => 'Error de conexión o consulta en la base de datos'], 500);
        } catch (Exception $e) {
            logger()->error('Error general: ' . $e->getMessage());
            return response()->json(['error' => 'Error inesperado'], 500);
        } catch (Throwable $e) {
            logger()->critical('Fallo catastrófico: ' . $e->getMessage());
            return response()->json(['error' => 'Fallo interno del servidor'], 500);
        }
    }

}
