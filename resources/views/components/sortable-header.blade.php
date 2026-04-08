@props(['column' => '', 'label' => '', 'sortBy' => 'id', 'sortDir' => 'asc'])

@php
    $isActive = $sortBy === $column;
    $newDir = ($isActive && $sortDir === 'asc') ? 'desc' : 'asc';
    $icon = $isActive ? ($sortDir === 'asc' ? '↑' : '↓') : '↕';
    
    $params = request()->query();
    $params['sort_by'] = $column;
    $params['sort_dir'] = $newDir;
    $url = request()->url() . '?' . http_build_query($params);
@endphp

<a href="{{ $url }}" class="inline-flex items-center gap-1 hover:text-[var(--text-primary)] transition-colors {{ $isActive ? 'font-semibold text-[var(--text-primary)]' : 'text-[var(--text-secondary)]' }}" title="Clique para ordenar">
    <span>{{ $label }}</span>
    <span class="text-xs opacity-50">{{ $icon }}</span>
</a>
