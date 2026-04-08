<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Support\ChurchDirectory;
use Illuminate\View\View;

final class PublicPortalController extends Controller
{
    public function index(ChurchDirectory $directory): View
    {
        return view('public.home', [
            'stats' => $directory->portalStats(),
            'cityGroups' => $directory->cityGroups(),
            'featuredChurch' => $directory->all()->first(),
        ]);
    }

    public function show(string $slug, ChurchDirectory $directory): View
    {
        $church = $directory->findBySlug($slug);

        abort_if($church === null, 404);

        return view('public.church', [
            'church' => $church,
            'relatedChurches' => $directory
                ->all()
                ->where('city', $church['city'])
                ->reject(static fn (array $item): bool => $item['slug'] === $church['slug'])
                ->values(),
        ]);
    }

    public function access(ChurchDirectory $directory): View
    {
        return view('auth.access', [
            'stats' => $directory->portalStats(),
            'featuredChurch' => $directory->all()->first(),
        ]);
    }
}
