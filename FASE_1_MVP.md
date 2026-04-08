# 🚀 Fase 1 MVP - Sistema de Gestão de Igrejas

Implementação completa da Fase 1 do PRD com Laravel 13 + PHP 8.3.

## ✅ O Que Foi Implementado

### 1️⃣ Banco de Dados (Migrations)
- ✅ `igrejas` - Tabela com campos e visibilidade em JSON
- ✅ `fotos` - Relacionamento N:1 com igrejas, controle de público/privado
- ✅ `permissions` - Sistema granular de permissões
- ✅ `user_permissions` - Relacionamento M:N usuários ↔ permissões
- ✅ `audit_logs` - Rastreamento completo de ações
- ✅ Alteração em `users` - Adicionados `is_admin` e `is_active`

### 2️⃣ Models (ORM + Relacionamentos)
```php
Igreja::class          // com fotos(), fotoPrincipal(), fotosPublicas()
Foto::class            // com igreja()
User::class            // com permissions(), temPermissao(), ehAdmin()
Permission::class      // com users()
AuditLog::class        // com user()
```

**Métodos-chave:**
- `Igreja::esCampoPublico($campo)` - Verificar visibilidade
- `Igreja::definirCampoPublico($campo, $bool)` - Alterar visibilidade
- `Igreja::fotosPublicas()` - Obter apenas fotos públicas

### 3️⃣ Políticas de Autorização (Policies)
- ✅ `IgrejaPolicy` - create, update, delete, alterarVisibilidade
- ✅ `FotoPolicy` - create, update, delete, alterarVisibilidade
- ✅ Lógica: Admin tem acesso total; usuários checam permissões

### 4️⃣ Auditoria Completa
- ✅ `IgrejaObserver` - Registra create, update, delete
- ✅ `FotoObserver` - Registra create, update, delete
- ✅ Dados capturados: usuário, ação, antes/depois, IP, user-agent

### 5️⃣ Controllers
#### IgrejaController
- `index()` - Lista paginada
- `create()` - Formulário
- `store()` - Salvar
- `show()` - Detalhes
- `edit()` - Editar
- `update()` - Atualizar
- `destroy()` - Deletar
- `atualizarVisibilidade()` - Controle por campo

#### FotoController
- `create()` - Formulário de upload
- `store()` - Fazer upload
- `update()` - Marcar como principal/público
- `destroy()` - Deletar

#### PortalPublicoController
- `index()` - Home pública (agrupa por cidade)
- `show()` - Detalhes de igreja (somente dados públicos)

### 6️⃣ Rotas
```
GET    / (portal home)
GET    /igrejas/{id} (portal detail)

Autenticadas:
GET/POST    /igrejas (CRUD)
PATCH/DELETE /igrejas/{id}
POST        /igrejas/{id}/visibilidade
POST        /igrejas/{id}/fotos (upload)
PATCH/DELETE /igrejas/{id}/fotos/{foto}
```

### 7️⃣ Views Blade
- ✅ `layouts/app.blade.php` - Layout base (existente)
- ✅ `portal/index.blade.php` - Home pública
- ✅ `portal/show.blade.php` - Detalhe público
- ✅ `igrejas/index.blade.php` - Lista admin
- ✅ `igrejas/create.blade.php` - Criar
- ✅ `igrejas/edit.blade.php` - Editar + Visibilidade
- ✅ `igrejas/show.blade.php` - Detalhes admin
- ✅ `fotos/create.blade.php` - Upload de fotos

### 8️⃣ Seeds & Fixtures
- ✅ `PermissionSeeder` - 9 permissões iniciais
- ✅ `DatabaseSeeder` - Integrado com AdminUserService
- ✅ `IgrejaFactory` - Dados fake
- ✅ `FotoFactory` - Dados fake

## 🔐 Sistema de Visibilidade

### Estrutura de Dados
```php
// Em Igreja::visibilidade (JSON)
[
    'codigo_controle'   => false, // privado
    'nome_fantasia'     => true,  // público
    'razao_social'      => false,
    'matricula'         => false,
    'cep'               => true,
    'endereco'          => true,
    'cidade'            => true,
    'estado'            => true,
]
```

### Regras
- **Visitantes** veem apenas campos público=true
- **Usuários autenticados** veem todos os dados se tiverem permissão
- **Admin** vê tudo
- **Fotos** têm `is_public` boolean simples

## 🔑 Permissões (Fase 1)

```
igrejas.criar
igrejas.editar
igrejas.deletar
igrejas.visualizar
igrejas.alterar_visibilidade
fotos.adicionar
fotos.editar
fotos.deletar
fotos.alterar_visibilidade
```

## 🚀 Como Usar

### 1. Executar Migrations
```bash
php artisan migrate:fresh --seed
```

### 2. Criar Admin
Admin é criado automaticamente pelo `AdminUserService`:
- Email: `admin@igrejas.local`
- Senha: `Admin@123456` (conforme `.env`)

### 3. Acessar
- **Portal Público**: `http://localhost:7842/`
- **Admin**: `http://localhost:7842/igrejas` (requer login)

### 4. Dar Permissões a Usuários
```php
$user = User::find(2);
$permissao = Permission::where('nome', 'igrejas.criar')->first();
$user->permissions()->attach($permissao);
```

## 📊 Fluxo de Auditoria

Toda ação em Igreja/Foto registra em `audit_logs`:

```
user_id  | 5
acao     | 'editar'
modulo   | 'igrejas'
entidade | 'App\Models\Igreja'
entidade_id | 10
antes    | {"nome_fantasia":"Igreja Antiga"}
depois   | {"nome_fantasia":"Igreja Nova"}
ip_address | '192.168.1.1'
user_agent | 'Mozilla/5.0...'
created_at | 2026-04-01 10:30:00
```

## 🧪 Estrutura de Testes (Próxima Fase)

Preparado para testes PHPUnit/Pest:
- `IgrejaControllerTest` - CRUD + visibilidade
- `FotoControllerTest` - Upload + delete
- `IgrejaObserverTest` - Auditoria
- `AuthorizationTest` - Policies

## 📝 Seeders Disponíveis

```php
PermissionSeeder      // Cria 9 permissões
AdminUserService      // Cria admin padrão
IgrejaFactory::count(50)  // Dados fake
FotoFactory::count(100)   // Dados fake
```

## 🎯 Próximas Fases

### Fase 2: Documentos
- Grupos de documentos
- Upload
- Visibilidade por grupo

### Fase 3: Tarefas
- CRUD de tarefas
- Status/Prioridade
- Responsáveis

### Fase 4: Usuários & Google Drive
- Gerenciamento de usuários
- Integração com Google Drive
- Dashboard com sincronização

## 🛠️ Stack Técnico

- **Laravel**: 13.x
- **PHP**: 8.3+
- **Database**: MySQL 8
- **ORM**: Eloquent
- **Auth**: Laravel Breeze
- **Storage**: Local + future Google Drive
- **Frontend**: Blade + Tailwind
- **Queue**: Sync (configurável)

## ✨ Diferenciais da Implementação

1. **Strict Types em Tudo** - `declare(strict_types=1)` em todos os arquivos
2. **Tipagem Completa** - Propriedades, parâmetros, retornos
3. **Observers para Auditoria** - Separação de responsabilidades
4. **Policies Granulares** - Controle fino por ação
5. **JSON para Visibilidade** - Flexível e escalável
6. **Factories para Testes** - Dados consistentes
7. **Soft Deletes** - Manter histórico

## 🐛 Conhecidos (MVP)

- Portal não filtra fotos privadas no frontend (precisa lógica query)
- Upload de fotos sem validação avançada (tamanho máximo 5MB)
- Sem compressão de imagem
- Sem integração CEP/ViaCEP (Fase futura)
- Sem Google Drive (Fase 4)

---

**Status**: ✅ Pronto para Testes e Próxima Fase
