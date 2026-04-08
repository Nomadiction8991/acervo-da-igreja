<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('igrejas', static function (Blueprint $table): void {
            $table->boolean('publico_codigo_controle')->default(false)->after('estado');
            $table->boolean('publico_nome_fantasia')->default(true)->after('publico_codigo_controle');
            $table->boolean('publico_razao_social')->default(false)->after('publico_nome_fantasia');
            $table->boolean('publico_matricula')->default(false)->after('publico_razao_social');
            $table->boolean('publico_cep')->default(true)->after('publico_matricula');
            $table->boolean('publico_endereco')->default(true)->after('publico_cep');
            $table->boolean('publico_cidade')->default(true)->after('publico_endereco');
            $table->boolean('publico_estado')->default(true)->after('publico_cidade');
        });

        $igrejas = DB::table('igrejas')->select(['id', 'visibilidade'])->get();

        foreach ($igrejas as $igreja) {
            $visibilidade = json_decode((string) $igreja->visibilidade, true);
            $visibilidade = is_array($visibilidade) ? $visibilidade : [];

            DB::table('igrejas')
                ->where('id', $igreja->id)
                ->update([
                    'publico_codigo_controle' => (bool) ($visibilidade['codigo_controle'] ?? false),
                    'publico_nome_fantasia' => (bool) ($visibilidade['nome_fantasia'] ?? true),
                    'publico_razao_social' => (bool) ($visibilidade['razao_social'] ?? false),
                    'publico_matricula' => (bool) ($visibilidade['matricula'] ?? false),
                    'publico_cep' => (bool) ($visibilidade['cep'] ?? true),
                    'publico_endereco' => (bool) ($visibilidade['endereco'] ?? true),
                    'publico_cidade' => (bool) ($visibilidade['cidade'] ?? true),
                    'publico_estado' => (bool) ($visibilidade['estado'] ?? true),
                ]);
        }
    }

    public function down(): void
    {
        Schema::table('igrejas', static function (Blueprint $table): void {
            $table->dropColumn([
                'publico_codigo_controle',
                'publico_nome_fantasia',
                'publico_razao_social',
                'publico_matricula',
                'publico_cep',
                'publico_endereco',
                'publico_cidade',
                'publico_estado',
            ]);
        });
    }
};
