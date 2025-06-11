@if ($paginator->hasPages())
    <nav aria-label="Pagination Navigation" class="d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
            <span class="text-muted small">
                Affichage de {{ $paginator->firstItem() }} à {{ $paginator->lastItem() }} 
                sur {{ $paginator->total() }} résultats
            </span>
        </div>
        
        <div class="d-flex gap-2">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <button class="btn btn-outline-secondary" disabled>
                    <i class="fas fa-chevron-left me-1"></i>Page précédente
                </button>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="btn btn-outline-primary">
                    <i class="fas fa-chevron-left me-1"></i>Page précédente
                </a>
            @endif

            {{-- Current Page Info --}}
            <span class="btn btn-light disabled">
                Page {{ $paginator->currentPage() }} sur {{ $paginator->lastPage() }}
            </span>

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="btn btn-outline-primary">
                    Page suivante<i class="fas fa-chevron-right ms-1"></i>
                </a>
            @else
                <button class="btn btn-outline-secondary" disabled>
                    Page suivante<i class="fas fa-chevron-right ms-1"></i>
                </button>
            @endif
        </div>
    </nav>
@endif

<style>
.btn {
    border-radius: 8px;
    font-weight: 500;
    transition: all 0.3s ease;
    border: 2px solid;
}

.btn-outline-primary {
    border-color: #3b82f6;
    color: #3b82f6;
}

.btn-outline-primary:hover {
    background-color: #3b82f6;
    border-color: #3b82f6;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
}

.btn-outline-secondary {
    border-color: #6b7280;
    color: #6b7280;
}

.btn-outline-secondary:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.btn-light {
    background-color: #f8fafc;
    border-color: #e2e8f0;
    color: #64748b;
    font-weight: 600;
}

.gap-2 {
    gap: 0.5rem;
}
</style>
