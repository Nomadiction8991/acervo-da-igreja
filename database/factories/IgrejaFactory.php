<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Igreja;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Igreja>
 */
final class IgrejaFactory extends Factory
{
    protected $model = Igreja::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'codigo_controle' => fake()->unique()->regexify('[A-Z]{3}[0-9]{4}'),
            'nome_fantasia' => fake()->words(3, asText: true),
            'razao_social' => fake()->company(),
            'matricula' => fake()->numerify('################'),
            'cep' => fake()->postcode(),
            'endereco' => fake()->streetAddress(),
            'cidade' => fake()->city(),
            'estado' => fake()->stateAbbr(),
            'visibilidade' => [
                'codigo_controle' => false,
                'nome_fantasia' => true,
                'razao_social' => false,
                'matricula' => false,
                'cep' => true,
                'endereco' => true,
                'cidade' => true,
                'estado' => true,
            ],
        ];
    }
}
