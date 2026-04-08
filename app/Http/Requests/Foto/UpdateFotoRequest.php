<?php

declare(strict_types=1);

namespace App\Http\Requests\Foto;

use Illuminate\Foundation\Http\FormRequest;

final class UpdateFotoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>|string>
     */
    public function rules(): array
    {
        return [
            'is_public' => ['nullable', 'boolean'],
            'is_principal' => ['nullable', 'boolean'],
        ];
    }
}
