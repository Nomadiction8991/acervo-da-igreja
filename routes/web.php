<?php

declare(strict_types=1);

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\DocumentoController;
use App\Http\Controllers\DriveAccountController;
use App\Http\Controllers\FileAccessController;
use App\Http\Controllers\FileControlController;
use App\Http\Controllers\FotoController;
use App\Http\Controllers\GrupoDocumentoController;
use App\Http\Controllers\IgrejaController;
use App\Http\Controllers\PortalPublicoController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RelatórioController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\TarefaController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Portal Público (visitantes)
Route::get('/', [PortalPublicoController::class, 'index'])->name('portal.index');
Route::get('/portal/igrejas/{igreja}', [PortalPublicoController::class, 'show'])->name('portal.show');
Route::get('/portal/documentos/{documento}', [PortalPublicoController::class, 'documento'])->name('portal.documentos.show');
Route::get('/arquivos/fotos/{foto}', [FileAccessController::class, 'foto'])->name('files.fotos.show');
Route::get('/arquivos/documentos/{documento}/visualizar', [FileAccessController::class, 'documentoVisualizar'])->name('files.documentos.preview');
Route::get('/arquivos/documentos/{documento}', [FileAccessController::class, 'documento'])->name('files.documentos.show');

Route::redirect('/admin', '/painel');

// Rotas autenticadas
Route::middleware('auth')->group(static function (): void {
    Route::get('/painel', AdminDashboardController::class)->name('admin.dashboard');
    Route::redirect('/dashboard', '/painel')->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // CRUD Igrejas
    Route::resource('igrejas', IgrejaController::class);
    Route::post('/igrejas/{igreja}/visibilidade', [IgrejaController::class, 'atualizarVisibilidade'])
        ->name('igrejas.visibilidade');
    Route::get('/igrejas/exportar/excel', function () {
        return (new \App\Exports\IgrejasExport())->download('igrejas_' . date('Y-m-d_H-i-s') . '.xlsx');
    })->name('igrejas.export');

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
    Route::get('/documentos/exportar/excel', function () {
        return (new \App\Exports\DocumentosExport())->download('documentos_' . date('Y-m-d_H-i-s') . '.xlsx');
    })->name('documentos.export');
    Route::resource('drive-accounts', DriveAccountController::class);
    Route::post('/drive-accounts/{driveAccount}/testar', [DriveAccountController::class, 'testConnection'])
        ->name('drive-accounts.test');
    Route::resource('grupo-documentos', GrupoDocumentoController::class);
    Route::get('/tags', [TagController::class, 'index'])
        ->middleware('permission:tags.visualizar')
        ->name('tags.index');
    Route::get('/tags/criar', [TagController::class, 'create'])
        ->middleware('permission:tags.criar')
        ->name('tags.create');
    Route::post('/tags', [TagController::class, 'store'])
        ->middleware('permission:tags.criar')
        ->name('tags.store');
    Route::get('/tags/{tag}/editar', [TagController::class, 'edit'])
        ->middleware('permission:tags.editar')
        ->name('tags.edit');
    Route::put('/tags/{tag}', [TagController::class, 'update'])
        ->middleware('permission:tags.editar')
        ->name('tags.update');
    Route::delete('/tags/{tag}', [TagController::class, 'destroy'])
        ->middleware('permission:tags.deletar')
        ->name('tags.destroy');
    Route::resource('tarefas', TarefaController::class);
    Route::get('/tarefas/exportar/excel', function () {
        return (new \App\Exports\TarefasExport())->download('tarefas_' . date('Y-m-d_H-i-s') . '.xlsx');
    })->name('tarefas.export');
    Route::resource('users', UserController::class);

    // Auditoria, Relatórios e Arquivos
    Route::get('/relatorios', [RelatórioController::class, 'dashboard'])->name('relatorios.dashboard');
    Route::get('/auditoria', [AuditLogController::class, 'index'])
        ->middleware('permission:logs.visualizar')
        ->name('audit-logs.index');
    Route::get('/arquivos', [FileControlController::class, 'index'])
        ->middleware('permission:arquivos.visualizar')
        ->name('files.index');
});

require __DIR__.'/auth.php';
