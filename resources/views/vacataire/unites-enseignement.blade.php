@extends('layouts.vacataire')

@section('title', 'Mes Unités d\'Enseignement')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-book me-2"></i>Mes Unités d'Enseignement
                        </h5>
                        <span class="badge bg-light text-primary">
                            {{ $uesGrouped->total() }} UE(s) assignée(s)
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-0">
                        Consultez la liste des unités d'enseignement qui vous sont assignées en tant que vacataire.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form method="GET" action="{{ route('vacataire.unites-enseignement') }}" class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">
                                <i class="fas fa-graduation-cap me-1"></i>Filière
                            </label>
                            <select name="filiere" class="form-select" onchange="this.form.submit()">
                                <option value="">Toutes les filières</option>
                                @foreach($filieres as $filiere)
                                    <option value="{{ $filiere->id }}" {{ request('filiere') == $filiere->id ? 'selected' : '' }}>
                                        {{ $filiere->nom }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">
                                <i class="fas fa-calendar me-1"></i>Semestre
                            </label>
                            <select name="semestre" class="form-select" onchange="this.form.submit()">
                                <option value="">Tous les semestres</option>
                                <option value="S1" {{ request('semestre') == 'S1' ? 'selected' : '' }}>Semestre 1</option>
                                <option value="S2" {{ request('semestre') == 'S2' ? 'selected' : '' }}>Semestre 2</option>
                                <option value="S3" {{ request('semestre') == 'S3' ? 'selected' : '' }}>Semestre 3</option>
                                <option value="S4" {{ request('semestre') == 'S4' ? 'selected' : '' }}>Semestre 4</option>
                                <option value="S5" {{ request('semestre') == 'S5' ? 'selected' : '' }}>Semestre 5</option>
                                <option value="S6" {{ request('semestre') == 'S6' ? 'selected' : '' }}>Semestre 6</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-grid">
                                <a href="{{ route('vacataire.unites-enseignement') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-1"></i>Réinitialiser
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- UEs Grid -->
    <div class="row">
        @forelse($uesGrouped as $ueGroup)
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100 border-0 shadow-sm ue-card">
                    <div class="card-header bg-gradient text-white position-relative overflow-hidden"
                         style="background: #7c3aed;">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1 fw-bold">{{ $ueGroup->ue->code }}</h6>
                                <small class="opacity-90">{{ $ueGroup->ue->filiere->nom }}</small>
                            </div>
                            <span class="badge bg-white text-primary">
                                {{ $ueGroup->ue->semestre }}
                            </span>
                        </div>
                        <!-- Decorative element -->
                        <div class="position-absolute top-0 end-0 opacity-20">
                            <i class="fas fa-book fa-3x"></i>
                        </div>
                    </div>

                    <div class="card-body">
                        <h6 class="card-title text-primary mb-3">
                            {{ $ueGroup->ue->nom }}
                        </h6>

                        <!-- Session Types Assigned -->
                        <div class="mb-3">
                            <small class="text-muted d-block mb-2">Types de séances assignées:</small>
                            <div class="d-flex flex-wrap gap-1">
                                @foreach($ueGroup->session_types as $type)
                                    @php
                                        $badgeClass = $type === 'CM' ? 'bg-danger' : ($type === 'TD' ? 'bg-success' : 'bg-info');
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">{{ $type }}</span>
                                @endforeach
                            </div>
                        </div>

                        <!-- Hours Information -->
                        <div class="row g-2 mb-3">
                            <div class="col-4">
                                <div class="text-center p-2 bg-light rounded">
                                    <small class="text-muted d-block">Heures CM</small>
                                    <strong class="text-danger">{{ $ueGroup->ue->heures_cm ?? 0 }}h</strong>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="text-center p-2 bg-light rounded">
                                    <small class="text-muted d-block">Heures TD</small>
                                    <strong class="text-success">{{ $ueGroup->ue->heures_td ?? 0 }}h</strong>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="text-center p-2 bg-light rounded">
                                    <small class="text-muted d-block">Heures TP</small>
                                    <strong class="text-info">{{ $ueGroup->ue->heures_tp ?? 0 }}h</strong>
                                </div>
                            </div>
                        </div>

                        @if($ueGroup->ue->specialite)
                            <div class="mb-3">
                                <small class="text-muted">Spécialité requise:</small>
                                <div class="mt-1">
                                    <span class="badge bg-secondary">{{ $ueGroup->ue->specialite }}</span>
                                </div>
                            </div>
                        @endif

                        <div class="mb-3">
                            <small class="text-muted">Statut:</small>
                            <div class="mt-1">
                                <span class="badge bg-success">
                                    <i class="fas fa-check me-1"></i>{{ $ueGroup->total_affectations }} affectation(s) validée(s)
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer bg-transparent border-0">
                        <div class="d-grid">
                            <a href="{{ route('vacataire.ue.details', $ueGroup->ue->id) }}"
                               class="btn btn-primary btn-sm">
                                <i class="fas fa-eye me-1"></i>VOIR DÉTAILS
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center py-5">
                        <div class="mb-4">
                            <i class="fas fa-book fa-4x text-muted"></i>
                        </div>
                        <h5 class="text-muted mb-3">Aucune UE assignée</h5>
                        <p class="text-muted mb-4">
                            Vous n'avez actuellement aucune unité d'enseignement assignée.
                            @if(request()->hasAny(['filiere', 'semestre']))
                                Essayez de modifier vos filtres de recherche.
                            @else
                                Contactez votre coordonnateur de filière pour plus d'informations.
                            @endif
                        </p>
                        @if(request()->hasAny(['filiere', 'semestre']))
                            <a href="{{ route('vacataire.unites-enseignement') }}" class="btn btn-outline-primary">
                                <i class="fas fa-times me-1"></i>Réinitialiser les filtres
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($uesGrouped->hasPages())
        <div class="row mt-4">
            <div class="col-12">
                <div class="d-flex justify-content-center">
                    {{ $uesGrouped->links() }}
                </div>
            </div>
        </div>
    @endif
</div>

@push('styles')
<style>
.ue-card {
    transition: all 0.3s ease;
    border: 1px solid rgba(124, 58, 237, 0.1) !important;
}

.ue-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(124, 58, 237, 0.15) !important;
    border-color: rgba(245, 158, 11, 0.3) !important;
}

.ue-card .card-header {
    position: relative;
    overflow: hidden;
}

.ue-card .card-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(45deg, rgba(255,255,255,0.1) 0%, transparent 50%, rgba(255,255,255,0.05) 100%);
}

.bg-gradient {
    background: #7c3aed !important;
}

.badge {
    font-size: 0.75rem;
    padding: 0.375rem 0.75rem;
}

.form-select:focus,
.form-control:focus {
    border-color: #7c3aed;
    box-shadow: 0 0 0 0.2rem rgba(124, 58, 237, 0.25);
}

.btn-primary {
    background: #7c3aed;
    border: none;
}

.btn-primary:hover {
    background: #6d28d9;
    transform: translateY(-2px);
}

.btn-outline-primary {
    color: #7c3aed;
    border-color: #7c3aed;
}

.btn-outline-primary:hover {
    background-color: #7c3aed;
    border-color: #7c3aed;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('📚 VACATAIRE UEs PAGE INITIALIZED');
    console.log('💜 Total UEs assigned:', {{ $uesGrouped->total() }});
    
    // Add hover effects to cards
    const ueCards = document.querySelectorAll('.ue-card');
    ueCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.borderColor = 'rgba(245, 158, 11, 0.5)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.borderColor = 'rgba(124, 58, 237, 0.1)';
        });
    });
});
</script>
@endpush
@endsection
