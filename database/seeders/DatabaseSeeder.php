<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Services\AdminUserService;
use App\Models\GrupoDocumento;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(PermissionSeeder::class);
        GrupoDocumento::query()->firstOrCreate(
            ['nome' => 'Administrativo'],
            ['descricao' => 'Documentos internos da igreja', 'publico_padrao' => false],
        );
        GrupoDocumento::query()->firstOrCreate(
            ['nome' => 'Publico'],
            ['descricao' => 'Materiais liberados ao portal publico', 'publico_padrao' => true],
        );
        GrupoDocumento::query()->firstOrCreate(
            ['nome' => 'Financeiro'],
            ['descricao' => 'Arquivos financeiros e contabeis', 'publico_padrao' => false],
        );
        app(AdminUserService::class)->ensureDefaultAdmin();
    }
}
