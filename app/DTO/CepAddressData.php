<?php

declare(strict_types=1);

namespace App\DTO;

/**
 * @phpstan-type CepAddressArray array{
 *     cep: string,
 *     endereco: string,
 *     cidade: string,
 *     estado: string
 * }
 */
final readonly class CepAddressData
{
    public function __construct(
        public string $cep,
        public string $endereco,
        public string $cidade,
        public string $estado,
    ) {
    }

    /**
     * @return CepAddressArray
     */
    public function toArray(): array
    {
        return [
            'cep' => $this->cep,
            'endereco' => $this->endereco,
            'cidade' => $this->cidade,
            'estado' => $this->estado,
        ];
    }
}
