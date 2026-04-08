<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Foto;
use App\Models\Igreja;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Foto>
 */
final class FotoFactory extends Factory
{
    protected $model = Foto::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'igreja_id' => Igreja::factory(),
            'caminho' => 'fotos/' . fake()->uuid() . '.jpg',
            'nome_original' => fake()->word() . '.jpg',
            'mime_type' => 'image/jpeg',
            'tamanho' => fake()->numberBetween(100000, 5000000),
            'is_public' => fake()->boolean(80),
            'is_principal' => false,
            'ordem' => 0,
        ];
    }

    /**
     * Mark as principal photo.
     */
    public function principal(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_principal' => true,
            'is_public' => true,
        ]);
    }
}
