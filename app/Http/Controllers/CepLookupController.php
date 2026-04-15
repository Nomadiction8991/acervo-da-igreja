<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\CepLookupService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class CepLookupController extends Controller
{
    public function __invoke(string $cep, CepLookupService $service): JsonResponse
    {
        $normalizedCep = preg_replace('/\D+/', '', $cep);

        if (! is_string($normalizedCep) || strlen($normalizedCep) !== 8) {
            return response()->json([
                'message' => 'CEP invalido.',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $address = $service->lookup($normalizedCep);

        if ($address === null) {
            return response()->json([
                'message' => 'CEP nao encontrado.',
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json($address->toArray());
    }
}
