<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Documento;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class FileControlController extends Controller
{
    public function index(Request $request): View
    {
        $arquivos = Documento::query()
            ->with(['igreja', 'grupoDocumento'])
            ->when($request->filled('status'), static function ($query) use ($request): void {
                $status = $request->string('status')->toString();

                match ($status) {
                    'sem_drive' => $query->whereNull('drive_file_id'),
                    'com_erro' => $query->where('sync_status', 'error'),
                    'sincronizados' => $query->where('sync_status', 'synced'),
                    default => null,
                };
            })
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        return view('files.index', compact('arquivos'));
    }
}
