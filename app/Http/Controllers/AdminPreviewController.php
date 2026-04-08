<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Support\ChurchDirectory;
use Illuminate\View\View;

final class AdminPreviewController extends Controller
{
    public function __invoke(ChurchDirectory $directory): View
    {
        return view('admin.dashboard', [
            'dashboard' => $directory->dashboard(),
        ]);
    }
}
