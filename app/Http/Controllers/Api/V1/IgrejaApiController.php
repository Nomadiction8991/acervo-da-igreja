<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Models\Igreja;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

final class IgrejaApiController
{
    /**
     * GET /api/v1/igrejas
     */
    public function index(Request $request): JsonResponse
    {
        $search = $request->input('q');
        $page = $request->input('page', 1);
        $perPage = $request->input('per_page', 20);

        $igrejas = Igreja::query()
            ->search($search)
            ->with(['fotos'])
            ->select('id', 'codigo_controle', 'nome_fantasia', 'razao_social', 'cidade', 'estado', 'created_at')
            ->paginate($perPage, ['*'], 'page', $page);

        return response()->json($igrejas);
    }

    /**
     * GET /api/v1/igrejas/{id}
     */
    public function show(Igreja $igreja): JsonResponse
    {
        $igreja->load(['fotos', 'documentos', 'tarefas']);

        return response()->json($igreja);
    }

    /**
     * POST /api/v1/igrejas
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'codigo_controle' => ['required', 'string', 'max:20'],
            'nome_fantasia' => ['required', 'string', 'max:255'],
            'razao_social' => ['required', 'string', 'max:255'],
            'cidade' => ['nullable', 'string'],
            'estado' => ['nullable', 'string', 'size:2'],
        ]);

        $this->ensureCodigoControleDisponivel($validated['codigo_controle']);

        $igreja = Igreja::create($validated);

        return response()->json($igreja, 201);
    }

    /**
     * PUT /api/v1/igrejas/{id}
     */
    public function update(Request $request, Igreja $igreja): JsonResponse
    {
        $validated = $request->validate([
            'codigo_controle' => ['sometimes', 'required', 'string', 'max:20'],
            'nome_fantasia' => ['sometimes', 'required', 'string', 'max:255'],
            'razao_social' => ['sometimes', 'required', 'string', 'max:255'],
            'cidade' => ['nullable', 'string'],
            'estado' => ['nullable', 'string', 'size:2'],
        ]);

        if (array_key_exists('codigo_controle', $validated)) {
            $this->ensureCodigoControleDisponivel($validated['codigo_controle'], $igreja);
        }

        $igreja->update($validated);

        return response()->json($igreja);
    }

    /**
     * DELETE /api/v1/igrejas/{id}
     */
    public function destroy(Igreja $igreja): JsonResponse
    {
        $igreja->delete();

        return response()->json(['message' => 'Igreja inativada com sucesso'], 200);
    }

    private function ensureCodigoControleDisponivel(string $codigoControle, ?Igreja $ignore = null): void
    {
        $query = Igreja::query()
            ->withTrashed()
            ->where('codigo_controle', $codigoControle);

        if ($ignore !== null) {
            $query->whereKeyNot($ignore->id);
        }

        $igrejaExistente = $query->first();

        if ($igrejaExistente === null) {
            return;
        }

        throw ValidationException::withMessages([
            'codigo_controle' => $igrejaExistente->trashed()
                ? 'Já existe uma igreja inativa com este código de controle. Reative-a em vez de cadastrar outra.'
                : 'Já existe uma igreja com este código de controle.',
        ]);
    }
}
