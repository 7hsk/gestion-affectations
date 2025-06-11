@extends('layouts.chef')

@section('title', 'Tableau de Bord')

@push('styles')
<style>
.dashboard-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 25px;
    padding: 2.5rem;
    margin-bottom: 2rem;
    box-shadow: 0 20px 60px rgba(102, 126, 234, 0.3);
    position: relative;
    overflow: hidden;
}

.dashboard-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(45deg, rgba(255,255,255,0.1) 0%, transparent 50%, rgba(255,255,255,0.05) 100%);
    pointer-events: none;
}

.welcome-section {
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
    color: white;
    border-radius: 25px;
    padding: 3rem;
    margin-bottom: 2rem;
    position: relative;
    overflow: hidden;
    box-shadow: 0 20px 60px rgba(26, 26, 46, 0.4);
}

.welcome-section::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(233, 69, 96, 0.1) 0%, transparent 70%);
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
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    box-shadow: 0 10px 40px rgba(102, 126, 234, 0.3);
}

.stats-card.primary:hover {
    box-shadow: 0 25px 80px rgba(102, 126, 234, 0.4);
}

.stats-card.danger {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    color: white;
    box-shadow: 0 10px 40px rgba(245, 87, 108, 0.3);
}

.stats-card.danger:hover {
    box-shadow: 0 25px 80px rgba(245, 87, 108, 0.4);
}

.stats-card.success {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    color: white;
    box-shadow: 0 10px 40px rgba(79, 172, 254, 0.3);
}

.stats-card.success:hover {
    box-shadow: 0 25px 80px rgba(79, 172, 254, 0.4);
}

.stats-card.warning {
    background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
    color: white;
    box-shadow: 0 10px 40px rgba(250, 112, 154, 0.3);
}

.stats-card.warning:hover {
    box-shadow: 0 25px 80px rgba(250, 112, 154, 0.4);
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
    color: #1a1a2e;
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
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
    opacity: 0;
    transition: opacity 0.4s ease;
    z-index: -1;
}

.action-card:hover::before {
    opacity: 1;
}

.action-card:hover {
    transform: translateY(-10px) scale(1.02);
    border-color: rgba(102, 126, 234, 0.3);
    box-shadow: 0 20px 60px rgba(102, 126, 234, 0.2);
    color: #1a1a2e;
    text-decoration: none;
}

.action-icon {
    font-size: 3rem;
    margin-bottom: 1.5rem;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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

.activity-icon.pending {
    background: rgba(243, 156, 18, 0.1);
    color: #f39c12;
}

.activity-icon.approved {
    background: rgba(39, 174, 96, 0.1);
    color: #27ae60;
}

.activity-icon.rejected {
    background: rgba(231, 76, 60, 0.1);
    color: #e74c3c;
}

.chart-container {
    position: relative;
    height: 300px;
    margin-top: 1rem;
}

.notification-item {
    padding: 1rem;
    border-left: 4px solid #2c3e50;
    background: #f8f9fa;
    margin-bottom: 0.5rem;
    border-radius: 0 8px 8px 0;
}

.notification-item.unread {
    background: rgba(44, 62, 80, 0.05);
    border-left-color: #e74c3c;
}

/* Floating geometric shapes */
.chef-floating-shapes {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    pointer-events: none;
    z-index: -1;
    overflow: hidden;
}

.chef-floating-shape {
    position: absolute;
    opacity: 0.1;
    animation: float-shape 15s ease-in-out infinite;
}

.chef-floating-shape:nth-child(1) {
    top: 20%;
    left: 10%;
    width: 60px;
    height: 60px;
    background: linear-gradient(45deg, #e94560, #f06292);
    border-radius: 50%;
    animation-delay: 0s;
}

.chef-floating-shape:nth-child(2) {
    top: 60%;
    right: 15%;
    width: 80px;
    height: 80px;
    background: linear-gradient(45deg, #667eea, #764ba2);
    border-radius: 20px;
    animation-delay: 2s;
}

.chef-floating-shape:nth-child(3) {
    bottom: 30%;
    left: 20%;
    width: 40px;
    height: 40px;
    background: linear-gradient(45deg, #f093fb, #f5576c);
    transform: rotate(45deg);
    animation-delay: 4s;
}

.chef-floating-shape:nth-child(4) {
    top: 40%;
    left: 60%;
    width: 50px;
    height: 50px;
    background: linear-gradient(45deg, #fa709a, #fee140);
    border-radius: 50%;
    animation-delay: 6s;
}

@keyframes float-shape {
    0%, 100% {
        transform: translateY(0px) rotate(0deg) scale(1);
        opacity: 0.1;
    }
    25% {
        transform: translateY(-20px) rotate(90deg) scale(1.1);
        opacity: 0.2;
    }
    50% {
        transform: translateY(-40px) rotate(180deg) scale(0.9);
        opacity: 0.15;
    }
    75% {
        transform: translateY(-20px) rotate(270deg) scale(1.05);
        opacity: 0.25;
    }
}
</style>
@endpush

@section('content')
<!-- Floating Background Shapes -->
<div class="chef-floating-shapes">
    <div class="chef-floating-shape"></div>
    <div class="chef-floating-shape"></div>
    <div class="chef-floating-shape"></div>
    <div class="chef-floating-shape"></div>
</div>

<div class="container-fluid">
    <!-- Welcome Section -->
    <div class="welcome-section">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h2 class="mb-2">Bienvenue, {{ Auth::user()->name }}</h2>
                <p class="mb-0 opacity-75">
                    Chef du Département {{ $departement->nom }} - ENSA Al Hoceima
                </p>
                <small class="opacity-50">{{ now()->format('l j F Y') }}</small>
            </div>
            <div class="col-md-4 text-end">
                <i class="fas fa-university fa-4x opacity-25"></i>
            </div>
        </div>
    </div>

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
            <div class="stats-number">{{ $stats['total_enseignants'] }}</div>
            <div class="stats-label">Enseignants</div>
        </div>

        <div class="stats-card danger">
            <div class="stats-number">{{ $stats['affectations_en_attente'] }}</div>
            <div class="stats-label">En Attente</div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <h4 class="mb-3">Actions Rapides</h4>
            <div class="quick-actions">
                <a href="{{ route('chef.affectations') }}" class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-tasks"></i>
                    </div>
                    <h6>Gérer Affectations</h6>
                    <small class="text-muted">Valider les demandes</small>
                </a>

                <a href="{{ route('chef.unites-enseignement', ['view_mode' => 'vacant']) }}" class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <h6>UEs Vacantes</h6>
                    <small class="text-muted">{{ $stats['ues_vacantes'] }} UEs à affecter</small>
                </a>

                <a href="{{ route('chef.enseignants') }}" class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h6>Enseignants</h6>
                    <small class="text-muted">Gérer les charges</small>
                </a>

                <a href="{{ route('chef.rapports') }}" class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    <h6>Rapports</h6>
                    <small class="text-muted">Statistiques détaillées</small>
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Recent Activity -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-clock me-2"></i>Activité Récente</h5>
                </div>
                <div class="card-body p-0">
                    <div class="recent-activity">
                        @forelse($affectationsEnAttente as $affectation)
                            <div class="activity-item">
                                <div class="activity-icon pending">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-bold">{{ $affectation->user->name }}</div>
                                    <div class="text-muted small">
                                        Demande d'affectation pour {{ $affectation->uniteEnseignement->code }}
                                    </div>
                                    <div class="text-muted small">
                                        {{ $affectation->created_at->diffForHumans() }}
                                    </div>
                                </div>
                                <div>
                                    <a href="{{ route('chef.affectations') }}" class="btn btn-sm btn-outline-primary">
                                        Traiter
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4">
                                <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                                <h6>Aucune demande en attente</h6>
                                <p class="text-muted">Toutes les affectations sont à jour</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Notifications & Alerts -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-bell me-2"></i>Notifications</h5>
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

            <!-- Enseignants avec charge insuffisante -->
            @if($enseignantsChargeInsuffisante->isNotEmpty())
                <div class="card mt-3">
                    <div class="card-header bg-warning text-dark">
                        <h6 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Charges Insuffisantes</h6>
                    </div>
                    <div class="card-body">
                        @foreach($enseignantsChargeInsuffisante->take(5) as $enseignant)
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div>
                                    <div class="fw-bold">{{ $enseignant->name }}</div>
                                    <small class="text-muted">{{ $enseignant->charge_horaire['total'] }}h / 192h</small>
                                </div>
                                <div>
                                    <span class="badge bg-warning">
                                        {{ round(($enseignant->charge_horaire['total'] / 192) * 100) }}%
                                    </span>
                                </div>
                            </div>
                        @endforeach

                        @if($enseignantsChargeInsuffisante->count() > 5)
                            <div class="text-center mt-2">
                                <a href="{{ route('chef.enseignants') }}" class="btn btn-sm btn-outline-warning">
                                    Voir tous ({{ $enseignantsChargeInsuffisante->count() }})
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Recent UEs -->
    @if($recentUes->isNotEmpty())
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-book me-2"></i>UEs Récemment Ajoutées</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Code</th>
                                        <th>Nom</th>
                                        <th>Filière</th>
                                        <th>Volume Horaire</th>
                                        <th>Statut</th>
                                        <th>Responsable</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentUes as $ue)
                                        <tr>
                                            <td><strong>{{ $ue->code }}</strong></td>
                                            <td>{{ $ue->nom }}</td>
                                            <td>
                                                <span class="badge bg-secondary">{{ $ue->filiere->nom ?? 'N/A' }}</span>
                                            </td>
                                            <td>{{ $ue->total_hours }}h</td>
                                            <td>
                                                @if($ue->est_vacant)
                                                    <span class="badge bg-warning">Vacant</span>
                                                @else
                                                    <span class="badge bg-success">Affecté</span>
                                                @endif
                                            </td>
                                            <td>
                                                @php
                                                    $responsable = \App\Models\Affectation::where('ue_id', $ue->id)
                                                        ->where('validee', 'valide')
                                                        ->with('user')
                                                        ->first();
                                                @endphp
                                                {{ $responsable ? $responsable->user->name : 'Non assigné' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-refresh notifications every 30 seconds
    setInterval(function() {
        // Implementation for real-time notifications
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
