@extends('layouts.chef')

@section('title', 'Gestion des Enseignants')

@push('styles')
<style>
.enseignant-card {
    border: none;
    border-radius: 15px;
    transition: all 0.3s ease;
    margin-bottom: 1.5rem;
    box-shadow: 0 6px 20px rgba(0,0,0,0.1);
    height: 100%;
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

.enseignant-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 35px rgba(0,0,0,0.2);
}

.enseignant-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 1.5rem;
    position: relative;
    overflow: hidden;
}

.enseignant-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(45deg, rgba(255,255,255,0.1) 0%, transparent 100%);
    pointer-events: none;
}

.enseignant-avatar {
    width: 70px;
    height: 70px;
    border-radius: 50%;
    background: rgba(255,255,255,0.25);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.8rem;
    font-weight: bold;
    margin-right: 1.25rem;
    border: 3px solid rgba(255,255,255,0.3);
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    position: relative;
    z-index: 1;
}

.enseignant-info {
    flex: 1;
    min-width: 0;
    position: relative;
    z-index: 1;
}

.enseignant-name {
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 0.25rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.enseignant-email {
    font-size: 0.85rem;
    opacity: 0.8;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.enseignant-specialite {
    margin-top: 0.5rem;
}

.enseignant-specialite .badge {
    font-size: 0.75rem;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    background: rgba(255,255,255,0.2) !important;
    color: white !important;
    border: 1px solid rgba(255,255,255,0.3);
}

.charge-progress {
    height: 8px;
    border-radius: 4px;
    overflow: hidden;
    background: #e9ecef;
    margin-top: 0.5rem;
}

.charge-bar {
    height: 100%;
    transition: width 0.3s ease;
}

.charge-insufficient {
    background: linear-gradient(90deg, #e74c3c, #c0392b);
}

.charge-normal {
    background: linear-gradient(90deg, #27ae60, #229954);
}

.charge-excessive {
    background: linear-gradient(90deg, #f39c12, #e67e22);
}

.enseignant-body {
    padding: 1.5rem;
    flex: 1;
    display: flex;
    flex-direction: column;
}

.charge-section {
    margin-bottom: 1.5rem;
}

.charge-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.charge-title {
    font-weight: 600;
    color: #2c3e50;
    font-size: 1rem;
}

.charge-badge {
    font-size: 0.8rem;
    padding: 0.4rem 0.8rem;
    border-radius: 20px;
    font-weight: 600;
}

.charge-stats {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 0.75rem;
    margin-top: 1rem;
}

.charge-item {
    text-align: center;
    padding: 1rem 0.5rem;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 12px;
    border: 1px solid rgba(0,0,0,0.05);
    transition: all 0.3s ease;
}

.charge-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.charge-label {
    font-size: 0.75rem;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 0.5rem;
    font-weight: 600;
}

.charge-value {
    font-size: 1.1rem;
    font-weight: 700;
    color: #2c3e50;
}

.action-buttons {
    margin-top: auto;
    padding-top: 1rem;
    border-top: 1px solid #eee;
}



.status-indicator {
    width: 16px;
    height: 16px;
    border-radius: 50%;
    position: relative;
    z-index: 1;
    border: 2px solid rgba(255,255,255,0.8);
    box-shadow: 0 2px 8px rgba(0,0,0,0.2);
}

.status-insufficient {
    background: #e74c3c;
    animation: pulse-red 2s infinite;
}

.status-normal {
    background: #27ae60;
}

.status-excessive {
    background: #f39c12;
    animation: pulse-orange 2s infinite;
}

@keyframes pulse-red {
    0%, 100% { box-shadow: 0 2px 8px rgba(0,0,0,0.2), 0 0 0 0 rgba(231, 76, 60, 0.7); }
    50% { box-shadow: 0 2px 8px rgba(0,0,0,0.2), 0 0 0 8px rgba(231, 76, 60, 0); }
}

@keyframes pulse-orange {
    0%, 100% { box-shadow: 0 2px 8px rgba(0,0,0,0.2), 0 0 0 0 rgba(243, 156, 18, 0.7); }
    50% { box-shadow: 0 2px 8px rgba(0,0,0,0.2), 0 0 0 8px rgba(243, 156, 18, 0); }
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .charge-stats {
        grid-template-columns: repeat(2, 1fr);
        gap: 0.5rem;
    }

    .enseignant-avatar {
        width: 60px;
        height: 60px;
        font-size: 1.5rem;
    }

    .enseignant-name {
        font-size: 1rem;
    }
}

.search-filters {
    background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
    color: white;
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 2rem;
}

.filter-row {
    display: grid;
    grid-template-columns: 1fr auto auto;
    gap: 1rem;
    align-items: end;
}

.search-input {
    position: relative;
}

.search-input .form-control {
    padding-left: 2.5rem;
    border-radius: 25px;
    border: 2px solid rgba(255,255,255,0.2);
    background: rgba(255,255,255,0.1);
    color: white;
}

.search-input .form-control::placeholder {
    color: rgba(255,255,255,0.7);
}

.search-input .form-control:focus {
    border-color: rgba(255,255,255,0.5);
    background: rgba(255,255,255,0.15);
    box-shadow: none;
}

.search-input .search-icon {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: rgba(255,255,255,0.7);
}

.summary-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 2rem;
}

.summary-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    text-align: center;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
}

.summary-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.12);
}

.summary-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
    font-size: 1.5rem;
}

.summary-icon.insufficient {
    background: rgba(231, 76, 60, 0.1);
    color: #e74c3c;
}

.summary-icon.normal {
    background: rgba(39, 174, 96, 0.1);
    color: #27ae60;
}

.summary-icon.excessive {
    background: rgba(243, 156, 18, 0.1);
    color: #f39c12;
}

.summary-number {
    font-size: 2rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 0.5rem;
}

.summary-label {
    font-size: 0.9rem;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div>
                <h2 class="mb-1">Gestion des Enseignants</h2>
                <p class="text-muted mb-0">Département {{ Auth::user()->departement->nom }}</p>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="summary-cards">
        @php
            $insufficient = $enseignants->filter(function($e) { return $e->charge_horaire['total'] < 192; })->count();
            $normal = $enseignants->filter(function($e) { return $e->charge_horaire['total'] >= 192 && $e->charge_horaire['total'] <= 240; })->count();
            $excessive = $enseignants->filter(function($e) { return $e->charge_horaire['total'] > 240; })->count();
        @endphp

        <div class="summary-card">
            <div class="summary-icon insufficient">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="summary-number">{{ $insufficient }}</div>
            <div class="summary-label">Charge Insuffisante</div>
        </div>

        <div class="summary-card">
            <div class="summary-icon normal">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="summary-number">{{ $normal }}</div>
            <div class="summary-label">Charge Normale</div>
        </div>

        <div class="summary-card">
            <div class="summary-icon excessive">
                <i class="fas fa-arrow-up"></i>
            </div>
            <div class="summary-number">{{ $excessive }}</div>
            <div class="summary-label">Charge Excessive</div>
        </div>

        <div class="summary-card">
            <div class="summary-icon normal">
                <i class="fas fa-users"></i>
            </div>
            <div class="summary-number">{{ $enseignants->count() }}</div>
            <div class="summary-label">Total Enseignants</div>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="search-filters">
        <form method="GET" action="{{ route('chef.enseignants') }}">
            <div class="filter-row">
                <div class="search-input">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" class="form-control" name="search"
                           placeholder="Rechercher par nom, email ou spécialité..."
                           value="{{ request('search') }}">
                </div>

                <button type="submit" class="btn btn-light">
                    <i class="fas fa-search me-2"></i>Rechercher
                </button>

                <a href="{{ route('chef.enseignants') }}" class="btn btn-outline-light">
                    <i class="fas fa-times me-2"></i>Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Enseignants List -->
    @if($enseignants->count() > 0)
        <div class="row">
            @foreach($enseignants as $enseignant)
                @php
                    $chargeStatus = 'normal';
                    if ($enseignant->charge_horaire['total'] < 192) {
                        $chargeStatus = 'insufficient';
                    } elseif ($enseignant->charge_horaire['total'] > 240) {
                        $chargeStatus = 'excessive';
                    }
                    $chargePercentage = min(100, ($enseignant->charge_horaire['total'] / 192) * 100);
                @endphp

                <div class="col-lg-6 col-xl-4">
                    <div class="enseignant-card">
                        <div class="enseignant-header">
                            <div class="d-flex align-items-center">
                                <div class="enseignant-avatar">
                                    {{ strtoupper(substr($enseignant->name, 0, 2)) }}
                                </div>
                                <div class="enseignant-info">
                                    <div class="enseignant-name">{{ $enseignant->name }}</div>
                                    <div class="enseignant-email">{{ $enseignant->email }}</div>
                                    @if($enseignant->specialite)
                                        <div class="enseignant-specialite">
                                            <span class="badge">{{ $enseignant->specialite }}</span>
                                        </div>
                                    @endif
                                </div>
                                <div class="text-end">
                                    <div class="status-indicator status-{{ $chargeStatus }}"></div>
                                </div>
                            </div>
                        </div>

                        <div class="enseignant-body">
                            <!-- Charge Horaire Section -->
                            <div class="charge-section">
                                <div class="charge-header">
                                    <div class="charge-title">Charge Horaire</div>
                                    <span class="charge-badge bg-{{ $chargeStatus == 'insufficient' ? 'danger' : ($chargeStatus == 'excessive' ? 'warning' : 'success') }}">
                                        {{ $enseignant->charge_horaire['total'] }}h / 192h
                                    </span>
                                </div>
                                <div class="charge-progress">
                                    <div class="charge-bar charge-{{ $chargeStatus }}"
                                         style="width: {{ $chargePercentage }}%"></div>
                                </div>

                                <!-- Charge Details -->
                                <div class="charge-stats">
                                    <div class="charge-item">
                                        <div class="charge-label">CM</div>
                                        <div class="charge-value">{{ $enseignant->charge_horaire['CM'] }}h</div>
                                    </div>
                                    <div class="charge-item">
                                        <div class="charge-label">TD</div>
                                        <div class="charge-value">{{ $enseignant->charge_horaire['TD'] }}h</div>
                                    </div>
                                    <div class="charge-item">
                                        <div class="charge-label">TP</div>
                                        <div class="charge-value">{{ $enseignant->charge_horaire['TP'] }}h</div>
                                    </div>
                                    <div class="charge-item">
                                        <div class="charge-label">Total</div>
                                        <div class="charge-value">{{ $enseignant->charge_horaire['total'] }}h</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="action-buttons">
                                <div class="d-flex gap-2 flex-wrap">
                                    <a href="{{ route('chef.enseignants.charge-horaire', $enseignant->id) }}"
                                       class="btn btn-primary btn-sm w-100">
                                        <i class="fas fa-chart-bar me-1"></i>Détails Charge
                                    </a>
                                </div>

                                @if($chargeStatus == 'insufficient')
                                    <div class="mt-2">
                                        <a href="{{ route('chef.affectations', ['enseignant_id' => $enseignant->id]) }}"
                                           class="btn btn-warning btn-sm w-100">
                                            <i class="fas fa-plus me-1"></i>Affecter UE
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="row mt-4">
            <div class="col-12">
                {{ $enseignants->appends(request()->query())->links() }}
            </div>
        </div>
    @else
        <div class="text-center py-5">
            <i class="fas fa-users fa-4x text-muted mb-3"></i>
            <h4>Aucun enseignant trouvé</h4>
            <p class="text-muted">Aucun enseignant ne correspond à vos critères de recherche.</p>
            <a href="{{ route('chef.enseignants') }}" class="btn btn-primary">
                <i class="fas fa-refresh me-2"></i>Réinitialiser
            </a>
        </div>
    @endif
</div>


@endsection

@push('scripts')
<script>
// Auto-refresh every 5 minutes to update charge horaire data
setInterval(function() {
    // Refresh charge horaire data if needed
}, 300000);
</script>
@endpush
