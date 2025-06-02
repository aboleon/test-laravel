@props([
    'hasSearchFilters' => false
])
@if ($hasSearchFilters)
    <x-mfw::alert class="p-2 d-flex justify-content-between align-items-center" type="success" message="<span>Vous affichez actuellement les résultats de votre dernière recherche.</span><button type='button' class='btn btn-secondary btn-sm current-search-btn-delete'>Réinitialiser l'affichage</a>"/>
@endif
