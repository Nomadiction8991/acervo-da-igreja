<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Data\AdminUserData;
use App\Services\AdminUserService;
use Illuminate\Console\Command;

final class EnsureAdminUser extends Command
{
    protected $signature = 'app:ensure-admin
        {--name= : Nome do administrador}
        {--email= : E-mail do administrador}
        {--password= : Senha do administrador}';

    protected $description = 'Cria ou atualiza o usuario administrador padrao do sistema.';

    public function __construct(
        private readonly AdminUserService $adminUsers,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $data = AdminUserData::fromArray([
            'name' => $this->option('name') ?: config('admin.default.name'),
            'email' => $this->option('email') ?: config('admin.default.email'),
            'password' => $this->option('password') ?: config('admin.default.password'),
        ]);

        $user = $this->adminUsers->ensureDefaultAdmin($data);

        $this->components->info('Administrador pronto para acesso.');
        $this->line('Nome: '.$user->name);
        $this->line('E-mail: '.$user->email);
        $this->line('Painel: '.route('admin.dashboard', absolute: false));

        return self::SUCCESS;
    }
}
