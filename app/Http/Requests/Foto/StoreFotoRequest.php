<?php

declare(strict_types=1);

namespace App\Http\Requests\Foto;

use Illuminate\Foundation\Http\FormRequest;

final class StoreFotoRequest extends FormRequest
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
            'fotos' => ['required', 'array', 'min:1'],
            'fotos.*' => ['required', 'image', 'max:5120'],
            'is_public' => ['nullable', 'boolean'],
        ];
    }
}
