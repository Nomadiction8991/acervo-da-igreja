<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

final class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // Igrejas
            ['slug' => 'igrejas.visualizar', 'nome' => 'igrejas.visualizar', 'modulo' => 'igrejas', 'acao' => 'visualizar', 'descricao' => 'Visualizar igrejas'],
            ['slug' => 'igrejas.criar', 'nome' => 'igrejas.criar', 'modulo' => 'igrejas', 'acao' => 'criar', 'descricao' => 'Criar igrejas'],
            ['slug' => 'igrejas.editar', 'nome' => 'igrejas.editar', 'modulo' => 'igrejas', 'acao' => 'editar', 'descricao' => 'Editar igrejas'],
            ['slug' => 'igrejas.deletar', 'nome' => 'igrejas.deletar', 'modulo' => 'igrejas', 'acao' => 'deletar', 'descricao' => 'Deletar igrejas'],
            ['slug' => 'igrejas.alterar_visibilidade', 'nome' => 'igrejas.alterar_visibilidade', 'modulo' => 'igrejas', 'acao' => 'alterar_visibilidade', 'descricao' => 'Alterar visibilidade de campos'],

            // Fotos
            ['slug' => 'fotos.visualizar', 'nome' => 'fotos.visualizar', 'modulo' => 'fotos', 'acao' => 'visualizar', 'descricao' => 'Visualizar fotos'],
            ['slug' => 'fotos.criar', 'nome' => 'fotos.criar', 'modulo' => 'fotos', 'acao' => 'criar', 'descricao' => 'Adicionar fotos'],
            ['slug' => 'fotos.editar', 'nome' => 'fotos.editar', 'modulo' => 'fotos', 'acao' => 'editar', 'descricao' => 'Editar fotos'],
            ['slug' => 'fotos.deletar', 'nome' => 'fotos.deletar', 'modulo' => 'fotos', 'acao' => 'deletar', 'descricao' => 'Deletar fotos'],
            ['slug' => 'fotos.alterar_visibilidade', 'nome' => 'fotos.alterar_visibilidade', 'modulo' => 'fotos', 'acao' => 'alterar_visibilidade', 'descricao' => 'Alterar visibilidade de fotos'],

            // Documentos
            ['slug' => 'documentos.visualizar', 'nome' => 'documentos.visualizar', 'modulo' => 'documentos', 'acao' => 'visualizar', 'descricao' => 'Visualizar documentos'],
            ['slug' => 'documentos.criar', 'nome' => 'documentos.criar', 'modulo' => 'documentos', 'acao' => 'criar', 'descricao' => 'Criar documentos'],
            ['slug' => 'documentos.editar', 'nome' => 'documentos.editar', 'modulo' => 'documentos', 'acao' => 'editar', 'descricao' => 'Editar documentos'],
            ['slug' => 'documentos.deletar', 'nome' => 'documentos.deletar', 'modulo' => 'documentos', 'acao' => 'deletar', 'descricao' => 'Deletar documentos'],

            // Grupos de documentos
            ['slug' => 'grupos_documentos.visualizar', 'nome' => 'grupos_documentos.visualizar', 'modulo' => 'grupos_documentos', 'acao' => 'visualizar', 'descricao' => 'Visualizar grupos de documentos'],
            ['slug' => 'grupos_documentos.criar', 'nome' => 'grupos_documentos.criar', 'modulo' => 'grupos_documentos', 'acao' => 'criar', 'descricao' => 'Criar grupos de documentos'],
            ['slug' => 'grupos_documentos.editar', 'nome' => 'grupos_documentos.editar', 'modulo' => 'grupos_documentos', 'acao' => 'editar', 'descricao' => 'Editar grupos de documentos'],
            ['slug' => 'grupos_documentos.deletar', 'nome' => 'grupos_documentos.deletar', 'modulo' => 'grupos_documentos', 'acao' => 'deletar', 'descricao' => 'Deletar grupos de documentos'],

            // Tags
            ['slug' => 'tags.visualizar', 'nome' => 'tags.visualizar', 'modulo' => 'tags', 'acao' => 'visualizar', 'descricao' => 'Visualizar tags'],
            ['slug' => 'tags.criar', 'nome' => 'tags.criar', 'modulo' => 'tags', 'acao' => 'criar', 'descricao' => 'Criar tags'],
            ['slug' => 'tags.editar', 'nome' => 'tags.editar', 'modulo' => 'tags', 'acao' => 'editar', 'descricao' => 'Editar tags'],
            ['slug' => 'tags.deletar', 'nome' => 'tags.deletar', 'modulo' => 'tags', 'acao' => 'deletar', 'descricao' => 'Deletar tags'],

            // Tarefas
            ['slug' => 'tarefas.visualizar', 'nome' => 'tarefas.visualizar', 'modulo' => 'tarefas', 'acao' => 'visualizar', 'descricao' => 'Visualizar tarefas'],
            ['slug' => 'tarefas.criar', 'nome' => 'tarefas.criar', 'modulo' => 'tarefas', 'acao' => 'criar', 'descricao' => 'Criar tarefas'],
            ['slug' => 'tarefas.editar', 'nome' => 'tarefas.editar', 'modulo' => 'tarefas', 'acao' => 'editar', 'descricao' => 'Editar tarefas'],
            ['slug' => 'tarefas.deletar', 'nome' => 'tarefas.deletar', 'modulo' => 'tarefas', 'acao' => 'deletar', 'descricao' => 'Deletar tarefas'],

            // Usuarios
            ['slug' => 'users.visualizar', 'nome' => 'users.visualizar', 'modulo' => 'users', 'acao' => 'visualizar', 'descricao' => 'Visualizar usuarios'],
            ['slug' => 'users.criar', 'nome' => 'users.criar', 'modulo' => 'users', 'acao' => 'criar', 'descricao' => 'Criar usuarios'],
            ['slug' => 'users.editar', 'nome' => 'users.editar', 'modulo' => 'users', 'acao' => 'editar', 'descricao' => 'Editar usuarios'],
            ['slug' => 'users.deletar', 'nome' => 'users.deletar', 'modulo' => 'users', 'acao' => 'deletar', 'descricao' => 'Deletar usuarios'],

            // Logs e arquivos
            ['slug' => 'logs.visualizar', 'nome' => 'logs.visualizar', 'modulo' => 'logs', 'acao' => 'visualizar', 'descricao' => 'Visualizar auditoria'],
            ['slug' => 'arquivos.visualizar', 'nome' => 'arquivos.visualizar', 'modulo' => 'arquivos', 'acao' => 'visualizar', 'descricao' => 'Visualizar controle de arquivos'],
            ['slug' => 'drive_accounts.visualizar', 'nome' => 'drive_accounts.visualizar', 'modulo' => 'drive_accounts', 'acao' => 'visualizar', 'descricao' => 'Visualizar contas Google Drive'],
            ['slug' => 'drive_accounts.criar', 'nome' => 'drive_accounts.criar', 'modulo' => 'drive_accounts', 'acao' => 'criar', 'descricao' => 'Criar contas Google Drive'],
            ['slug' => 'drive_accounts.editar', 'nome' => 'drive_accounts.editar', 'modulo' => 'drive_accounts', 'acao' => 'editar', 'descricao' => 'Editar contas Google Drive'],
            ['slug' => 'drive_accounts.deletar', 'nome' => 'drive_accounts.deletar', 'modulo' => 'drive_accounts', 'acao' => 'deletar', 'descricao' => 'Deletar contas Google Drive'],
            ['slug' => 'drive_accounts.testar', 'nome' => 'drive_accounts.testar', 'modulo' => 'drive_accounts', 'acao' => 'testar', 'descricao' => 'Testar conexao de contas Google Drive'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['slug' => $permission['slug']],
                $permission,
            );
        }
    }
}
