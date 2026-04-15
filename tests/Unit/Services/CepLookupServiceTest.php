<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Services\CepLookupService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

final class CepLookupServiceTest extends TestCase
{
    public function test_lookup_returns_address_for_valid_cep(): void
    {
        Http::fake([
            'https://viacep.com.br/ws/78000000/json/' => Http::response([
                'cep' => '78000-000',
                'logradouro' => 'Rua das Palmeiras',
                'localidade' => 'Cuiaba',
                'uf' => 'MT',
            ]),
        ]);

        $result = app(CepLookupService::class)->lookup('78000-000');

        self::assertNotNull($result);
        self::assertSame('78000000', $result->cep);
        self::assertSame('Rua das Palmeiras', $result->endereco);
        self::assertSame('Cuiaba', $result->cidade);
        self::assertSame('MT', $result->estado);
    }

    public function test_lookup_returns_null_when_cep_is_invalid(): void
    {
        Http::fake();

        $result = app(CepLookupService::class)->lookup('123');

        self::assertNull($result);
        Http::assertNothingSent();
    }

    public function test_lookup_returns_null_when_viacep_reports_missing_cep(): void
    {
        Http::fake([
            'https://viacep.com.br/ws/78000000/json/' => Http::response([
                'erro' => true,
            ]),
        ]);

        $result = app(CepLookupService::class)->lookup('78000000');

        self::assertNull($result);
    }
}
