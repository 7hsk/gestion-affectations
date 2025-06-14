@extends('layouts.chef')

@section('title', 'Historique des Affectations')

@push('styles')
<style>
.timeline-container {
    position: relative;
    padding-left: 2rem;
}

.timeline-line {
    position: absolute;
    left: 1rem;
    top: 0;
    bottom: 0;
    width: 2px;
    background: linear-gradient(180deg, #2c3e50 0%, #34495e 100%);
}

.timeline-item {
    position: relative;
    margin-bottom: 2rem;
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
}

.timeline-item:hover {
    transform: translateX(5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.12);
}

.timeline-marker {
    position: absolute;
    left: -2rem;
    top: 1.5rem;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background: linear-gradient(135deg, #2c3e50, #34495e);
    border: 3px solid white;
    box-shadow: 0 2px 8px rgba(0,0,0,0.2);
}

.timeline-marker.recent {
    background: linear-gradient(135deg, #27ae60, #229954);
    animation: pulse 2s infinite;
}

.timeline-marker.old {
    background: linear-gradient(135deg, #95a5a6, #7f8c8d);
}

@keyframes pulse {
    0% { box-shadow: 0 2px 8px rgba(0,0,0,0.2); }
    50% { box-shadow: 0 2px 8px rgba(39, 174, 96, 0.4), 0 0 0 8px rgba(39, 174, 96, 0.1); }
    100% { box-shadow: 0 2px 8px rgba(0,0,0,0.2); }
}

.timeline-content {
    padding: 1.5rem;
}

.timeline-header {
    display: flex;
    justify-content: between;
    align-items: start;
    margin-bottom: 1rem;
}

.timeline-date {
    background: linear-gradient(135deg, #2c3e50, #34495e);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 500;
}

.ue-info {
    flex-grow: 1;
    margin-right: 1rem;
}

.ue-code {
    font-size: 1.2rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 0.25rem;
}

.ue-name {
    color: #6c757d;
    margin-bottom: 0.5rem;
}

.enseignant-info {
    display: flex;
    align-items: center;
    background: #f8f9fa;
    border-radius: 8px;
    padding: 1rem;
    margin: 1rem 0;
}

.enseignant-avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: linear-gradient(135deg, #2c3e50, #34495e);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    margin-right: 1rem;
}

.details-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
    gap: 1rem;
    margin-top: 1rem;
}

.detail-item {
    text-align: center;
    padding: 0.75rem;
    background: #f8f9fa;
    border-radius: 8px;
}

.detail-label {
    font-size: 0.8rem;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 0.25rem;
}

.detail-value {
    font-size: 1rem;
    font-weight: 600;
    color: #2c3e50;
}

.filters-section {
    background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
    color: white;
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 2rem;
}

.year-tabs {
    display: flex;
    gap: 0.5rem;
    margin-bottom: 2rem;
    flex-wrap: wrap;
}

.year-tab {
    padding: 0.75rem 1.5rem;
    border-radius: 25px;
    background: white;
    color: #2c3e50;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.year-tab:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    color: #2c3e50;
    text-decoration: none;
}

.year-tab.active {
    background: linear-gradient(135deg, #2c3e50, #34495e);
    color: white;
    border-color: white;
}

.stats-summary {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    text-align: center;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
}

.stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
    font-size: 1.5rem;
    background: linear-gradient(135deg, #2c3e50, #34495e);
    color: white;
}

.stat-number {
    font-size: 2rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 0.5rem;
}

.stat-label {
    font-size: 0.9rem;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.search-section {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 2rem;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
}

.search-input {
    position: relative;
}

.search-input .form-control {
    padding-left: 2.5rem;
    border-radius: 25px;
    border: 2px solid #e9ecef;
    transition: all 0.3s ease;
}

.search-input .form-control:focus {
    border-color: #2c3e50;
    box-shadow: 0 0 0 0.2rem rgba(44, 62, 80, 0.25);
}

.search-input .search-icon {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: #6c757d;
}

.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    color: #6c757d;
}

.empty-state i {
    font-size: 4rem;
    margin-bottom: 1rem;
    opacity: 0.5;
}

.export-section {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 1rem;
    margin-bottom: 2rem;
    text-align: center;
}

.btn-export {
    margin: 0.25rem;
}

/* Activity-specific styles */
.activity-title {
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
    color: #2c3e50;
}

.timeline-marker.success {
    background: linear-gradient(135deg, #28a745, #20c997);
}

.timeline-marker.warning {
    background: linear-gradient(135deg, #ffc107, #fd7e14);
}

.timeline-marker.danger {
    background: linear-gradient(135deg, #dc3545, #e83e8c);
}

.timeline-marker.secondary {
    background: linear-gradient(135deg, #6c757d, #adb5bd);
}

/* New Section Layout Styles */
.section-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    margin-bottom: 20px;
    overflow: hidden;
}

.section-header {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    padding: 20px;
    border-bottom: 1px solid #dee2e6;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.section-title {
    margin: 0;
    color: #495057;
    font-weight: 600;
}

.section-content {
    padding: 20px;
    max-height: 600px;
    overflow-y: auto;
}

.activity-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.activity-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.activity-card .enseignant-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 14px;
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1">Historique des Affectations</h2>
                    <p class="text-muted mb-0">Suivi chronologique des affectations validées</p>
                </div>
                <div>
                    <div class="d-flex gap-2">
                        <select id="exportYear" class="form-select">
                            @foreach($annees as $annee)
                                <option value="{{ $annee }}" {{ request('annee', $annees->first()) == $annee ? 'selected' : '' }}>
                                    {{ $annee }}
                                </option>
                            @endforeach
                        </select>
                    <button class="btn btn-primary" onclick="exportHistorique()">
                        <i class="fas fa-download me-2"></i>Exporter Historique
                    </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Year Tabs -->
    @if($annees->isNotEmpty())
        <div class="year-tabs">
            @foreach($annees as $annee)
                <a href="{{ route('chef.historique', ['annee' => $annee]) }}" 
                   class="year-tab {{ request('annee', $annees->first()) == $annee ? 'active' : '' }}">
                    {{ $annee }}
                </a>
            @endforeach
        </div>
    @endif

    <!-- Enhanced Statistics Summary -->
    <div class="stats-summary">
        <div class="stat-card">
            <div class="stat-icon bg-primary">
                <i class="fas fa-user-check"></i>
            </div>
            <div class="stat-number">{{ $stats['total_affectations'] }}</div>
            <div class="stat-label">Affectations Validées</div>
        </div>

        <div class="stat-card">
            <div class="stat-icon bg-warning">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-number">{{ $stats['demandes_en_attente'] }}</div>
            <div class="stat-label">Demandes En Attente</div>
        </div>

        <div class="stat-card">
            <div class="stat-icon bg-success">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-number">{{ $stats['demandes_approuvees'] }}</div>
            <div class="stat-label">Demandes Approuvées</div>
        </div>

        <div class="stat-card">
            <div class="stat-icon bg-danger">
                <i class="fas fa-times-circle"></i>
            </div>
            <div class="stat-number">{{ $stats['demandes_rejetees'] }}</div>
            <div class="stat-label">Demandes Rejetées</div>
        </div>
    </div>

    <!-- Two Column Layout -->
    <div class="row">
        <!-- Left Column: Affectations -->
        <div class="col-md-6">
            <div class="section-card">
                <div class="section-header">
                    <h4 class="section-title">
                        <i class="fas fa-user-check me-2 text-success"></i>
                        Affectations
                    </h4>
                    <span class="badge bg-success">{{ $stats['total_affectations'] }}</span>
                </div>
                <div class="section-content" id="affectations-section">
                    <!-- Affectations will be loaded here -->
                </div>
            </div>
        </div>

        <!-- Right Column: Demandes -->
        <div class="col-md-6">
            <div class="section-card">
                <div class="section-header">
                    <h4 class="section-title">
                        <i class="fas fa-file-alt me-2 text-primary"></i>
                        Demandes
                    </h4>
                    <span class="badge bg-primary">{{ $stats['demandes_en_attente'] + $stats['demandes_approuvees'] + $stats['demandes_rejetees'] }}</span>
                </div>
                <div class="section-content" id="demandes-section">
                    <!-- Demandes will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <script>
        // Load data into sections
        document.addEventListener('DOMContentLoaded', function() {
            loadAffectations();
            loadDemandes();
        });

        function loadAffectations() {
            const affectationsData = @json($affectationsData);
            const container = document.getElementById('affectations-section');

            if (affectationsData.length === 0) {
                container.innerHTML = '<div class="empty-state"><i class="fas fa-history"></i><h5>Aucune affectation trouvée</h5><p>Aucune affectation disponible pour cette période.</p></div>';
                return;
            }

            let html = '<div class="affectations-list">';
            affectationsData.forEach((activity, index) => {
                html += createAffectationCard(activity);
            });
            html += '</div>';

            container.innerHTML = html;

            // Add scroll indicator if content overflows
            setTimeout(() => {
                const content = container.querySelector('.affectations-list');
                if (content && content.scrollHeight > container.clientHeight) {
                    container.classList.add('has-scroll');
                }
            }, 100);
        }

        function loadDemandes() {
            const demandesData = @json($demandesData);
            const container = document.getElementById('demandes-section');

            if (demandesData.length === 0) {
                container.innerHTML = '<div class="empty-state"><i class="fas fa-file-alt"></i><h5>Aucune demande trouvée</h5><p>Aucune demande avec etat/commentaire disponible.</p></div>';
                return;
            }

            let html = '<div class="demandes-list">';
            demandesData.forEach((activity, index) => {
                html += createDemandeCard(activity);
            });
            html += '</div>';

            container.innerHTML = html;

            // Add scroll indicator if content overflows
            setTimeout(() => {
                const content = container.querySelector('.demandes-list');
                if (content && content.scrollHeight > container.clientHeight) {
                    container.classList.add('has-scroll');
                }
            }, 100);
        }

        // SEPARATE FUNCTION FOR AFFECTATIONS
        function createAffectationCard(activity) {
            const date = activity.date ? new Date(activity.date).toLocaleDateString('fr-FR') : 'Date inconnue';
            const dateTime = activity.date ? new Date(activity.date).toLocaleString('fr-FR') : 'Date inconnue';

            return `
                <div class="activity-card mb-3" data-search="${activity.enseignant.name.toLowerCase()} ${activity.ue.code.toLowerCase()} ${activity.ue.nom.toLowerCase()}">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div class="activity-title">
                                    <i class="${activity.icon} me-2 text-${activity.color}"></i>
                                    <strong>${activity.title}</strong>
                                </div>
                                <span class="badge bg-${activity.color}">${activity.details.status_detail || activity.statut}</span>
                            </div>

                            <div class="ue-info mb-2">
                                <div class="fw-bold">${activity.ue.code}</div>
                                <div class="text-muted">${activity.ue.nom}</div>
                            </div>

                            <div class="enseignant-info mb-2">
                                <div class="d-flex align-items-center">
                                    <div class="enseignant-avatar me-2">
                                        ${activity.enseignant.name.substring(0, 2).toUpperCase()}
                                    </div>
                                    <div>
                                        <div class="fw-bold">${activity.enseignant.name}</div>
                                        <div class="text-muted small">${activity.enseignant.email}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">${dateTime}</small>
                                <button class="btn btn-sm btn-outline-primary" onclick="showActivityDetails('${activity.id}')">
                                    <i class="fas fa-eye me-1"></i>Détails
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }

        // SEPARATE FUNCTION FOR DEMANDES - SAME DESIGN AS AFFECTATIONS
        function createDemandeCard(activity) {
            const date = activity.date ? new Date(activity.date).toLocaleDateString('fr-FR') : 'Date inconnue';
            const dateTime = activity.date ? new Date(activity.date).toLocaleString('fr-FR') : 'Date inconnue';

            return `
                <div class="activity-card mb-3" data-search="${activity.enseignant.name.toLowerCase()} ${activity.ue.code.toLowerCase()} ${activity.ue.nom.toLowerCase()}">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div class="activity-title">
                                    <i class="${activity.icon} me-2 text-${activity.color}"></i>
                                    <strong>${activity.title}</strong>
                                </div>
                                <span class="badge bg-${activity.color}">${activity.details.status_detail || activity.statut}</span>
                            </div>

                            <div class="ue-info mb-2">
                                <div class="fw-bold">${activity.ue.code}</div>
                                <div class="text-muted">${activity.ue.nom}</div>
                            </div>

                            <div class="enseignant-info mb-2">
                                <div class="d-flex align-items-center">
                                    <div class="enseignant-avatar me-2">
                                        ${activity.enseignant.name.substring(0, 2).toUpperCase()}
                                    </div>
                                    <div>
                                        <div class="fw-bold">${activity.enseignant.name}</div>
                                        <div class="text-muted small">${activity.enseignant.email}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">${dateTime}</small>
                                <button class="btn btn-sm btn-outline-primary" onclick="showActivityDetails('${activity.id}')">
                                    <i class="fas fa-eye me-1"></i>Détails
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }

        function showActivityDetails(activityId) {
            // Find activity in both arrays
            const allActivities = [...@json($affectationsData), ...@json($demandesData)];
            const activity = allActivities.find(a => a.id === activityId);

            if (!activity) return;

            const date = activity.date ? new Date(activity.date).toLocaleString('fr-FR') : 'Date inconnue';

            let detailsHtml = `
                <div class="row mb-2">
                    <div class="col-4"><strong>Type:</strong></div>
                    <div class="col-8"><span class="badge bg-${activity.color}">${activity.title}</span></div>
                </div>
                <div class="row mb-2">
                    <div class="col-4"><strong>Statut:</strong></div>
                    <div class="col-8">${activity.details.status_detail || activity.statut}</div>
                </div>
                <div class="row mb-2">
                    <div class="col-4"><strong>Filière:</strong></div>
                    <div class="col-8">${activity.details.filiere || 'N/A'}</div>
                </div>
                <div class="row mb-2">
                    <div class="col-4"><strong>Semestre:</strong></div>
                    <div class="col-8">${activity.details.semestre || 'N/A'}</div>
                </div>
            `;

            if (activity.details.type_seance) {
                detailsHtml += `
                    <div class="row mb-2">
                        <div class="col-4"><strong>Type Séance:</strong></div>
                        <div class="col-8">${activity.details.type_seance}</div>
                    </div>
                `;
            }

            detailsHtml += `
                <div class="row mb-2">
                    <div class="col-4"><strong>Année:</strong></div>
                    <div class="col-8">${activity.annee_universitaire}</div>
                </div>
            `;

            if (activity.details.commentaire) {
                detailsHtml += `
                    <div class="row mb-2">
                        <div class="col-4"><strong>Commentaire:</strong></div>
                        <div class="col-8">${activity.details.commentaire}</div>
                    </div>
                `;
            }

            if (activity.details.date_validation) {
                const validationDate = new Date(activity.details.date_validation).toLocaleString('fr-FR');
                detailsHtml += `
                    <div class="row mb-2">
                        <div class="col-4"><strong>Date Validation:</strong></div>
                        <div class="col-8">${validationDate}</div>
                    </div>
                `;
            }

            const modalContent = `
                <div class="modal fade" id="activityDetailsModal" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">${activity.title} - ${activity.ue.code}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row mb-3">
                                    <div class="col-12">
                                        <h6 class="text-primary">Unité d'Enseignement</h6>
                                        <p><strong>${activity.ue.code}</strong> - ${activity.ue.nom}</p>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-12">
                                        <h6 class="text-primary">Enseignant</h6>
                                        <p><strong>${activity.enseignant.name}</strong><br>
                                        <small class="text-muted">${activity.enseignant.email}</small></p>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-12">
                                        <h6 class="text-primary">Date</h6>
                                        <p>${date}</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <h6 class="text-primary">Détails</h6>
                                        ${detailsHtml}
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            // Remove existing modal if any
            const existingModal = document.getElementById('activityDetailsModal');
            if (existingModal) {
                existingModal.remove();
            }

            // Add modal to body
            document.body.insertAdjacentHTML('beforeend', modalContent);

            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('activityDetailsModal'));
            modal.show();
        }

        function exportHistorique() {
            const year = document.getElementById('exportYear').value;

            // Open a new window for the download process
            const newWindow = window.open('about:blank', '_blank');
            if (!newWindow) {
                alert('Veuillez autoriser les pop-ups pour l\'exportation du PDF.');
                return;
            }
            newWindow.document.write('<html><head><title>Préparation du PDF</title></head><body style="font-family: Arial, sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; background-color: #f0f2f5;"><div style="text-align: center; padding: 20px; border-radius: 10px; background: white; box-shadow: 0 4px 8px rgba(0,0,0,0.1);"><i class="fas fa-spinner fa-spin fa-3x" style="color: #007bff; margin-bottom: 20px;"></i><h2>Préparation du PDF...</h2><p>Le téléchargement de votre rapport va commencer sous peu.</p></div></body></html>');
            newWindow.document.close();

            // Create a temporary form to submit to the export route
            const form = document.createElement('form');
            form.action = `{{ route('chef.export.historique') }}`;
            form.method = 'POST';
            form.target = newWindow.name; // Target the newly opened window

            // Add CSRF token
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = '{{ csrf_token() }}';
            form.appendChild(csrfInput);

            // Add year input
            const yearInput = document.createElement('input');
            yearInput.type = 'hidden';
            yearInput.name = 'year';
            yearInput.value = year;
            form.appendChild(yearInput);

            document.body.appendChild(form);
            form.submit();
            document.body.removeChild(form);

            // It's tricky to know exactly when the download is complete in the new window.
            // A common pattern is to set a timeout to close the window, assuming the download starts quickly.
            // The user will need to have pop-ups enabled.
            setTimeout(() => {
                if (newWindow && !newWindow.closed) {
                    newWindow.close();
                }
            }, 3000); // Close after 3 seconds, adjust if download takes longer
        }
    </script>
</div>
@endsection
