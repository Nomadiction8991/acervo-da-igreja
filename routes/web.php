<?php

declare(strict_types=1);

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\CepLookupController;
use App\Http\Controllers\DocumentoController;
use App\Http\Controllers\DriveAccountController;
use App\Http\Controllers\FileAccessController;
use App\Http\Controllers\FileControlController;
use App\Http\Controllers\FotoController;
use App\Http\Controllers\GrupoDocumentoController;
use App\Http\Controllers\IgrejaController;
use App\Http\Controllers\PortalPublicoController;
use App\Http\Controllers\PublicPortalController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TarefaController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Portal Público (visitantes)
Route::get('/', [PortalPublicoController::class, 'index'])->name('portal.index');
Route::get('/portal/igrejas/{igreja}', [PortalPublicoController::class, 'show'])->name('portal.show');
Route::get('/portal/documentos/{documento}', [PortalPublicoController::class, 'documento'])->name('portal.documentos.show');
Route::get('/portal-publico', [PublicPortalController::class, 'index'])->name('public.portal.index');
Route::get('/portal-publico/igrejas/{slug}', [PublicPortalController::class, 'show'])->name('portal.church');
Route::get('/portal-publico/acesso', [PublicPortalController::class, 'access'])->name('public.portal.access');
Route::get('/arquivos/fotos/{foto}', [FileAccessController::class, 'foto'])->name('files.fotos.show');
Route::get('/arquivos/documentos/{documento}/visualizar', [FileAccessController::class, 'documentoVisualizar'])->name('files.documentos.preview');
Route::get('/arquivos/documentos/{documento}', [FileAccessController::class, 'documento'])->name('files.documentos.show');

Route::redirect('/admin', '/painel');

// Rotas autenticadas
Route::middleware('auth')->group(static function (): void {
    Route::get('/painel', AdminDashboardController::class)->name('admin.dashboard');
    Route::redirect('/dashboard', '/painel')->name('dashboard');
    Route::get('/ceps/{cep}', CepLookupController::class)->name('ceps.show');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // CRUD Igrejas
    Route::resource('igrejas', IgrejaController::class);
    Route::post('/igrejas/{igreja}/visibilidade', [IgrejaController::class, 'atualizarVisibilidade'])
        ->name('igrejas.visibilidade');

    // Fotos
    Route::get('/igrejas/{igreja}/fotos', [FotoController::class, 'index'])
        ->name('fotos.index');
    Route::get('/igrejas/{igreja}/fotos/criar', [FotoController::class, 'create'])
        ->name('fotos.create');
    Route::post('/igrejas/{igreja}/fotos', [FotoController::class, 'store'])
        ->name('fotos.store');
    Route::get('/igrejas/{igreja}/fotos/{foto}', [FotoController::class, 'show'])
        ->name('fotos.show');
    Route::get('/igrejas/{igreja}/fotos/{foto}/editar', [FotoController::class, 'edit'])
        ->name('fotos.edit');
    Route::patch('/igrejas/{igreja}/fotos/{foto}', [FotoController::class, 'update'])
        ->name('fotos.update');
    Route::post('/igrejas/{igreja}/fotos/{foto}/sincronizar-drive', [FotoController::class, 'syncToDrive'])
        ->name('fotos.sync-drive');
    Route::delete('/igrejas/{igreja}/fotos/{foto}', [FotoController::class, 'destroy'])
        ->name('fotos.destroy');

    // Documentos, tarefas e usuarios
    Route::get('/drive-accounts/{driveAccount}/oauth/redirecionar', [DriveAccountController::class, 'redirectToGoogle'])
        ->name('drive-accounts.oauth.redirect');
    Route::get('/google/drive/callback', [DriveAccountController::class, 'handleGoogleCallback'])
        ->name('google.drive.callback');
    Route::resource('documentos', DocumentoController::class);
    Route::post('/documentos/{documento}/sincronizar-drive', [DocumentoController::class, 'syncToDrive'])
        ->name('documentos.sync-drive');
    Route::resource('drive-accounts', DriveAccountController::class);
    Route::post('/drive-accounts/{driveAccount}/testar', [DriveAccountController::class, 'testConnection'])
        ->name('drive-accounts.test');
    Route::resource('grupo-documentos', GrupoDocumentoController::class);
    Route::resource('tarefas', TarefaController::class);
    Route::resource('users', UserController::class);

    // Auditoria e Arquivos
    Route::get('/auditoria', [AuditLogController::class, 'index'])
        ->middleware('permission:logs.visualizar')
        ->name('audit-logs.index');
    Route::get('/arquivos', [FileControlController::class, 'index'])
        ->middleware('permission:arquivos.visualizar')
        ->name('files.index');
});

require __DIR__.'/auth.php';
