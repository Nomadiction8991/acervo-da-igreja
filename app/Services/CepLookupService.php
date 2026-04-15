<?php

declare(strict_types=1);

namespace App\Services;

use App\DTO\CepAddressData;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

final class CepLookupService
{
    public function lookup(string $cep): ?CepAddressData
    {
        $normalizedCep = self::normalizeCep($cep);

        if ($normalizedCep === null) {
            return null;
        }

        try {
            $response = Http::acceptJson()
                ->timeout(5)
                ->get(sprintf('https://viacep.com.br/ws/%s/json/', $normalizedCep));
        } catch (ConnectionException) {
            return null;
        }

        if (! $response->successful()) {
            return null;
        }

        /** @var array<string, mixed> $payload */
        $payload = $response->json();

        if (($payload['erro'] ?? false) === true) {
            return null;
        }

        $cidade = trim(self::stringField($payload, 'localidade'));
        $estado = strtoupper(trim(self::stringField($payload, 'uf')));

        if ($cidade === '' || $estado === '') {
            return null;
        }

        return new CepAddressData(
            cep: $normalizedCep,
            endereco: trim(self::stringField($payload, 'logradouro')),
            cidade: $cidade,
            estado: $estado,
        );
    }

    /**
     * @param array<string, mixed> $payload
     */
    private static function stringField(array $payload, string $key): string
    {
        $value = $payload[$key] ?? null;

        return is_scalar($value) ? (string) $value : '';
    }

    private static function normalizeCep(string $cep): ?string
    {
        $digits = preg_replace('/\D+/', '', $cep);

        if (! is_string($digits) || strlen($digits) !== 8) {
            return null;
        }

        return $digits;
    }
}
