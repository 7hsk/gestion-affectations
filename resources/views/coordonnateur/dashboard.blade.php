@extends('layouts.coordonnateur')

@section('title', 'Tableau de Bord')

@push('styles')
<style>
.welcome-section {
    background: linear-gradient(135deg, #064e3b 0%, #047857 50%, #059669 100%);
    color: white;
    border-radius: 25px;
    padding: 3rem;
    margin-bottom: 2rem;
    position: relative;
    overflow: hidden;
    box-shadow: 0 20px 60px rgba(6, 78, 59, 0.4);
}

.welcome-section::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(5, 150, 105, 0.1) 0%, transparent 70%);
    animation: float-welcome 8s ease-in-out infinite;
}

.welcome-section::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(45deg, rgba(255,255,255,0.05) 0%, transparent 50%, rgba(255,255,255,0.02) 100%);
    pointer-events: none;
}

@keyframes float-welcome {
    0%, 100% { transform: translateY(0px) rotate(0deg); }
    50% { transform: translateY(-30px) rotate(180deg); }
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stats-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border-radius: 20px;
    padding: 2rem;
    text-align: center;
    box-shadow: 0 10px 40px rgba(0,0,0,0.1);
    transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
    position: relative;
    overflow: hidden;
    border: 1px solid rgba(255,255,255,0.2);
}

.stats-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(255,255,255,0.9) 0%, rgba(255,255,255,0.6) 100%);
    z-index: -1;
}

.stats-card:hover {
    transform: translateY(-15px) scale(1.02);
    box-shadow: 0 25px 80px rgba(0,0,0,0.2);
}

.stats-card.primary {
    background: linear-gradient(135deg, #059669 0%, #10b981 100%);
    color: white;
    box-shadow: 0 10px 40px rgba(5, 150, 105, 0.3);
}

.stats-card.primary:hover {
    box-shadow: 0 25px 80px rgba(5, 150, 105, 0.4);
}

.stats-card.success {
    background: linear-gradient(135deg, #059669 0%, #10b981 100%);
    color: white;
    box-shadow: 0 10px 40px rgba(5, 150, 105, 0.3);
}

.stats-card.success:hover {
    box-shadow: 0 25px 80px rgba(5, 150, 105, 0.4);
}

.stats-card.warning {
    background: linear-gradient(135deg, #d97706 0%, #f59e0b 100%);
    color: white;
    box-shadow: 0 10px 40px rgba(217, 119, 6, 0.3);
}

.stats-card.warning:hover {
    box-shadow: 0 25px 80px rgba(217, 119, 6, 0.4);
}

.stats-card.info {
    background: linear-gradient(135deg, #0891b2 0%, #06b6d4 100%);
    color: white;
    box-shadow: 0 10px 40px rgba(8, 145, 178, 0.3);
}

.stats-card.info:hover {
    box-shadow: 0 25px 80px rgba(8, 145, 178, 0.4);
}

.stats-number {
    font-size: 3rem;
    font-weight: 800;
    margin-bottom: 0.5rem;
    text-shadow: 0 2px 10px rgba(0,0,0,0.2);
}

.stats-label {
    font-size: 0.9rem;
    opacity: 0.9;
    text-transform: uppercase;
    letter-spacing: 1px;
    font-weight: 600;
}

.quick-actions {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 2rem;
}

.action-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border-radius: 20px;
    padding: 2rem;
    text-align: center;
    transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
    border: 2px solid rgba(255,255,255,0.2);
    text-decoration: none;
    color: #064e3b;
    position: relative;
    overflow: hidden;
    box-shadow: 0 10px 40px rgba(0,0,0,0.1);
}

.action-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(5, 150, 105, 0.1) 0%, rgba(16, 185, 129, 0.1) 100%);
    opacity: 0;
    transition: opacity 0.4s ease;
    z-index: -1;
}

.action-card:hover::before {
    opacity: 1;
}

.action-card:hover {
    transform: translateY(-10px) scale(1.02);
    border-color: rgba(5, 150, 105, 0.3);
    box-shadow: 0 20px 60px rgba(5, 150, 105, 0.2);
    color: #064e3b;
    text-decoration: none;
}

.action-icon {
    font-size: 3rem;
    margin-bottom: 1.5rem;
    background: linear-gradient(135deg, #059669 0%, #10b981 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    transition: transform 0.3s ease;
}

.action-card:hover .action-icon {
    transform: scale(1.1);
}

.recent-activity {
    max-height: 400px;
    overflow-y: auto;
}

.activity-item {
    display: flex;
    align-items: center;
    padding: 1rem;
    border-bottom: 1px solid #eee;
    transition: background-color 0.3s ease;
}

.activity-item:hover {
    background-color: #f8f9fa;
}

.activity-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 1rem;
    font-size: 1.2rem;
}

.activity-icon.ue {
    background: rgba(5, 150, 105, 0.1);
    color: #059669;
}

.activity-icon.vacataire {
    background: rgba(5, 150, 105, 0.1);
    color: #059669;
}

.activity-icon.schedule {
    background: rgba(8, 145, 178, 0.1);
    color: #0891b2;
}

.filiere-badge {
    display: inline-block;
    background: linear-gradient(135deg, #059669, #10b981);
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: 15px;
    font-size: 0.8rem;
    font-weight: 600;
    margin: 0.25rem;
    box-shadow: 0 2px 10px rgba(5, 150, 105, 0.3);
}

.alert-card {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    color: white;
    border-radius: 15px;
    padding: 1.5rem;
    margin-bottom: 1rem;
    box-shadow: 0 10px 30px rgba(245, 158, 11, 0.3);
}

.alert-card h6 {
    margin: 0 0 0.5rem 0;
    font-weight: 700;
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Welcome Section -->
    <div class="welcome-section">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h2 class="mb-2">Bienvenue, {{ Auth::user()->name }}</h2>
                <p class="mb-0 opacity-75">
                    Coordonnateur de Filière
                    @if($filieres->isNotEmpty())
                        - {{ $filieres->pluck('nom')->implode(', ') }}
                    @endif
                </p>
                <small class="opacity-50">{{ now()->format('l j F Y') }}</small>
            </div>
            <div class="col-md-4 text-end">
                <i class="fas fa-graduation-cap fa-4x opacity-25"></i>
            </div>
        </div>
    </div>

    <!-- Filières gérées -->
    @if($filieres->isNotEmpty())
        <div class="card mb-4">
            <div class="card-header">
                <h5><i class="fas fa-sitemap me-2"></i>Filières Gérées</h5>
            </div>
            <div class="card-body">
                @foreach($filieres as $filiere)
                    <span class="filiere-badge">{{ $filiere->nom }}</span>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Statistics Cards -->
    <div class="stats-grid">
        <div class="stats-card primary">
            <div class="stats-number">{{ $stats['total_ues'] }}</div>
            <div class="stats-label">Total UEs</div>
        </div>

        <div class="stats-card warning">
            <div class="stats-number">{{ $stats['ues_vacantes'] }}</div>
            <div class="stats-label">UEs Vacantes</div>
        </div>

        <div class="stats-card success">
            <div class="stats-number">{{ $stats['total_vacataires'] }}</div>
            <div class="stats-label">Vacataires</div>
        </div>

        <div class="stats-card info">
            <div class="stats-number">{{ $stats['affectations_en_attente'] }}</div>
            <div class="stats-label">En Attente</div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <h4 class="mb-3">Actions Rapides</h4>
            <div class="quick-actions">
                <a href="{{ route('coordonnateur.unites-enseignement') }}" class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-book"></i>
                    </div>
                    <h6>Gérer UEs</h6>
                    <small class="text-muted">Créer et modifier</small>
                </a>

                <a href="{{ route('coordonnateur.vacataires') }}" class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <h6>Vacataires</h6>
                    <small class="text-muted">{{ $stats['total_vacataires'] }} vacataires</small>
                </a>

                <a href="{{ route('coordonnateur.affectations') }}" class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-tasks"></i>
                    </div>
                    <h6>Affectations</h6>
                    <small class="text-muted">Consulter par semestre</small>
                </a>

                <a href="{{ route('coordonnateur.emplois-du-temps') }}" class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <h6>Emplois du Temps</h6>
                    <small class="text-muted">Planification</small>
                </a>
            </div>
        </div>
    </div>

    <!-- Alerts for missing TD/TP groups -->
    @if($uesGroupesManquants->isNotEmpty())
        <div class="alert-card">
            <h6><i class="fas fa-exclamation-triangle me-2"></i>UEs nécessitant définition des groupes</h6>
            <p class="mb-2">{{ $uesGroupesManquants->count() }} UE(s) n'ont pas de groupes TD/TP définis :</p>
            @foreach($uesGroupesManquants->take(3) as $ue)
                <small class="d-block">• {{ $ue->code }} - {{ $ue->nom }} ({{ $ue->filiere->nom }})</small>
            @endforeach
            @if($uesGroupesManquants->count() > 3)
                <small class="d-block mt-1">... et {{ $uesGroupesManquants->count() - 3 }} autre(s)</small>
            @endif
        </div>
    @endif

    <div class="row">
        <!-- Recent Activity -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-clock me-2"></i>Activité Récente</h5>
                </div>
                <div class="card-body p-0">
                    <div class="recent-activity">
                        @forelse($affectationsRecentes as $affectation)
                            <div class="activity-item">
                                <div class="activity-icon {{ $affectation->user->role == 'vacataire' ? 'vacataire' : 'ue' }}">
                                    <i class="fas {{ $affectation->user->role == 'vacataire' ? 'fa-user-tie' : 'fa-book' }}"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-bold">{{ $affectation->user->name }}</div>
                                    <div class="text-muted small">
                                        Affectation {{ $affectation->type_seance }} - {{ $affectation->uniteEnseignement->code }}
                                    </div>
                                    <div class="text-muted small">
                                        {{ $affectation->created_at->diffForHumans() }}
                                    </div>
                                </div>
                                <div>
                                    <span class="badge bg-{{ $affectation->validee == 'valide' ? 'success' : ($affectation->validee == 'rejete' ? 'danger' : 'warning') }}">
                                        {{ ucfirst($affectation->validee) }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <h6>Aucune activité récente</h6>
                                <p class="text-muted">Les affectations apparaîtront ici</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent UEs and Notifications -->
        <div class="col-lg-4">
            <!-- Recent UEs -->
            <div class="card mb-3">
                <div class="card-header">
                    <h6><i class="fas fa-book me-2"></i>UEs Récentes</h6>
                </div>
                <div class="card-body">
                    @forelse($recentUes as $ue)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <div class="fw-bold">{{ $ue->code }}</div>
                                <small class="text-muted">{{ Str::limit($ue->nom, 30) }}</small>
                                <br><small class="text-primary">{{ $ue->filiere->nom }} - {{ $ue->semestre }}</small>
                            </div>
                            <div>
                                <span class="badge bg-{{ $ue->est_vacant ? 'warning' : 'success' }}">
                                    {{ $ue->est_vacant ? 'Vacant' : 'Affecté' }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-3">
                            <i class="fas fa-book fa-2x text-muted mb-2"></i>
                            <p class="text-muted">Aucune UE récente</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Notifications -->
            <div class="card">
                <div class="card-header">
                    <h6><i class="fas fa-bell me-2"></i>Notifications</h6>
                </div>
                <div class="card-body">
                    @forelse($notifications as $notification)
                        <div class="notification-item {{ !$notification->is_read ? 'unread' : '' }}">
                            <div class="fw-bold">{{ $notification->title }}</div>
                            <div class="text-muted small">{{ $notification->message }}</div>
                            <div class="text-muted small">{{ $notification->created_at->diffForHumans() }}</div>
                        </div>
                    @empty
                        <div class="text-center py-3">
                            <i class="fas fa-bell-slash fa-2x text-muted mb-2"></i>
                            <p class="text-muted">Aucune notification</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-refresh statistics every 30 seconds
    setInterval(function() {
        // Implementation for real-time statistics
    }, 30000);

    // Mark notifications as read when clicked
    document.querySelectorAll('.notification-item.unread').forEach(function(item) {
        item.addEventListener('click', function() {
            this.classList.remove('unread');
            // Send AJAX request to mark as read
        });
    });
});
</script>
@endpush
