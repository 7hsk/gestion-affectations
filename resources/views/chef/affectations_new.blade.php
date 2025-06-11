@extends('layouts.chef')

@section('title', 'Affectation Interactive des UEs')

@section('styles')
<style>
/* Drag and Drop Assignment Styles */
.drag-drop-container {
    display: flex;
    gap: 2rem;
    min-height: 500px;
}

.enseignant-selector {
    flex: 0 0 300px;
    background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
    border-radius: 15px;
    padding: 1.5rem;
    color: white;
}

.ue-lists-container {
    flex: 1;
    display: flex;
    gap: 1.5rem;
}

.ue-available, .ue-assigned {
    flex: 1;
    background: white;
    border-radius: 15px;
    border: 2px dashed #dee2e6;
    min-height: 400px;
    position: relative;
}

.ue-available {
    border-color: #28a745;
    background: linear-gradient(135deg, #f8fff9 0%, #e8f5e8 100%);
}

.ue-assigned {
    border-color: #007bff;
    background: linear-gradient(135deg, #f8fbff 0%, #e3f2fd 100%);
}

.ue-list-header {
    padding: 1rem 1.5rem;
    border-bottom: 2px solid #dee2e6;
    font-weight: 600;
    text-align: center;
    background: rgba(255, 255, 255, 0.9);
    border-radius: 13px 13px 0 0;
}

.ue-available .ue-list-header {
    background: linear-gradient(135deg, #28a745, #20c997);
    color: white;
    border-color: #28a745;
}

.ue-assigned .ue-list-header {
    background: linear-gradient(135deg, #007bff, #0056b3);
    color: white;
    border-color: #007bff;
}

.ue-list-content {
    padding: 1rem;
    max-height: 350px;
    overflow-y: auto;
}

.ue-item-draggable {
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 10px;
    padding: 1rem;
    margin-bottom: 0.75rem;
    cursor: grab;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.ue-item-draggable:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.15);
    border-color: #007bff;
}

.ue-item-draggable:active {
    cursor: grabbing;
    transform: scale(0.98);
}

.ue-item-draggable.dragging {
    opacity: 0.5;
    transform: rotate(5deg);
}

.ue-item-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.5rem;
}

.ue-code {
    font-weight: 600;
    color: #2c3e50;
    font-size: 0.9rem;
}

.ue-type-badge {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
}

.ue-name {
    font-size: 0.85rem;
    color: #6c757d;
    margin-bottom: 0.5rem;
}

.ue-details-small {
    display: flex;
    justify-content: space-between;
    font-size: 0.75rem;
    color: #6c757d;
}

.drop-zone {
    border: 2px dashed #007bff;
    border-radius: 10px;
    padding: 2rem;
    text-align: center;
    color: #6c757d;
    background: rgba(0, 123, 255, 0.05);
    margin: 1rem;
}

.drop-zone.drag-over {
    border-color: #28a745;
    background: rgba(40, 167, 69, 0.1);
    color: #28a745;
}

.enseignant-card {
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 10px;
    padding: 1rem;
    margin-bottom: 1rem;
    cursor: pointer;
    transition: all 0.3s ease;
}

.enseignant-card:hover {
    background: rgba(255, 255, 255, 0.2);
    transform: translateY(-2px);
}

.enseignant-card.selected {
    background: linear-gradient(135deg, #28a745, #20c997);
    border-color: #28a745;
    box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
}

.enseignant-avatar-small {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #007bff, #0056b3);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    margin-right: 1rem;
    font-size: 0.9rem;
}

.enseignant-info-small {
    flex: 1;
}

.enseignant-name {
    font-weight: 600;
    margin-bottom: 0.25rem;
}

.enseignant-specialities {
    font-size: 0.75rem;
    opacity: 0.8;
}

.assignment-actions {
    padding: 1.5rem;
    border-top: 1px solid #dee2e6;
    background: #f8f9fa;
    border-radius: 0 0 15px 15px;
    text-align: center;
}

.empty-state {
    text-align: center;
    padding: 3rem 1rem;
    color: #6c757d;
}

.empty-state i {
    font-size: 3rem;
    margin-bottom: 1rem;
    opacity: 0.5;
}

.stat-item {
    text-align: center;
    padding: 1rem;
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.stat-number {
    font-size: 2rem;
    font-weight: bold;
    color: #2c3e50;
}

.stat-number.pending {
    color: #ffc107;
}

.stat-number.approved {
    color: #28a745;
}

.stat-label {
    font-size: 0.9rem;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1">
                        <i class="fas fa-magic me-2"></i>Affectation Interactive des UEs
                    </h2>
                    <p class="text-muted mb-0">Glissez-déposez les UEs pour affecter les enseignants de manière interactive</p>
                </div>
                <div>
                    <button class="btn btn-outline-primary me-2" onclick="resetAllAssignments()">
                        <i class="fas fa-undo me-2"></i>Réinitialiser Tout
                    </button>
                    <button class="btn btn-success" onclick="saveAllAssignments()" id="saveAllBtn" disabled>
                        <i class="fas fa-save me-2"></i>Sauvegarder Tout
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Drag and Drop Interface -->
    <div class="drag-drop-container">
        <!-- Enseignant Selector -->
        <div class="enseignant-selector">
            <h6 class="mb-3">
                <i class="fas fa-users me-2"></i>Sélectionner un Enseignant
            </h6>
            <div id="enseignantsList">
                <!-- Enseignants will be loaded here -->
                <div class="text-center py-3">
                    <i class="fas fa-spinner fa-spin mb-2"></i>
                    <p class="mb-0">Chargement des enseignants...</p>
                </div>
            </div>

            <div class="mt-3" id="selectedEnseignantInfo" style="display: none;">
                <div class="alert alert-success">
                    <h6><i class="fas fa-user-check me-2"></i>Enseignant Sélectionné</h6>
                    <div id="selectedEnseignantDetails"></div>
                    <div class="mt-2">
                        <small><strong>Spécialités:</strong> <span id="selectedEnseignantSpecialities"></span></small>
                    </div>
                    <div class="mt-2">
                        <small><strong>Charge actuelle:</strong> <span id="selectedEnseignantCharge">0h/192h</span></small>
                    </div>
                </div>
            </div>
        </div>

        <!-- UE Lists Container -->
        <div class="ue-lists-container">
            <!-- Available UEs -->
            <div class="ue-available">
                <div class="ue-list-header">
                    <i class="fas fa-list me-2"></i>UEs Disponibles
                    <span class="badge bg-light text-dark ms-2" id="availableCount">0</span>
                </div>
                <div class="ue-list-content" id="availableUEs">
                    <div class="empty-state">
                        <i class="fas fa-graduation-cap"></i>
                        <p>Sélectionnez un enseignant pour voir les UEs compatibles</p>
                    </div>
                </div>
            </div>

            <!-- Assigned UEs -->
            <div class="ue-assigned">
                <div class="ue-list-header">
                    <i class="fas fa-check-circle me-2"></i>UEs à Affecter
                    <span class="badge bg-light text-dark ms-2" id="assignedCount">0</span>
                </div>
                <div class="ue-list-content" id="assignedUEs" ondrop="dropUE(event)" ondragover="allowDrop(event)">
                    <div class="drop-zone" id="dropZone">
                        <i class="fas fa-hand-point-right mb-2"></i>
                        <p>Glissez-déposez les UEs ici pour les affecter</p>
                        <small class="text-muted">Les UEs compatibles avec les spécialités de l'enseignant apparaîtront à gauche</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Assignment Actions Footer -->
    <div class="assignment-actions mt-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <span class="text-muted">
                    <i class="fas fa-info-circle me-1"></i>
                    Glissez les UEs de gauche à droite pour les affecter à l'enseignant sélectionné
                </span>
            </div>
            <div>
                <button type="button" class="btn btn-secondary me-2" onclick="resetAllAssignments()">
                    <i class="fas fa-undo me-2"></i>Réinitialiser
                </button>
                <button type="button" class="btn btn-success" onclick="saveAllAssignments()" id="saveAllAssignmentsBtn" disabled>
                    <i class="fas fa-save me-2"></i>Sauvegarder les Affectations
                </button>
            </div>
        </div>
    </div>

    <!-- Statistics Summary -->
    <div class="row mt-4">
        <div class="col-md-3">
            <div class="stat-item">
                <div class="stat-number" id="totalEnseignants">{{ $enseignants->count() }}</div>
                <div class="stat-label">Enseignants</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-item">
                <div class="stat-number pending" id="totalUEsVacantes">0</div>
                <div class="stat-label">UEs Vacantes</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-item">
                <div class="stat-number approved" id="totalAffectations">{{ $approved ?? 0 }}</div>
                <div class="stat-label">Affectations Validées</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-item">
                <div class="stat-number" id="sessionAssignments">0</div>
                <div class="stat-label">Affectations en Cours</div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
// Drag and Drop Assignment Variables
let selectedEnseignant = null;
let availableUEs = [];
let assignedUEs = [];

// Initialize drag and drop when page loads
document.addEventListener('DOMContentLoaded', function() {
    loadEnseignants();
    loadStatistics();
});

// Load enseignants list
function loadEnseignants() {
    fetch('/chef/enseignants-list')
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('enseignantsList');
            container.innerHTML = '';

            data.forEach(enseignant => {
                const card = document.createElement('div');
                card.className = 'enseignant-card d-flex align-items-center';
                card.onclick = () => selectEnseignant(enseignant);
                card.innerHTML = `
                    <div class="enseignant-avatar-small">
                        ${enseignant.name.substring(0, 2).toUpperCase()}
                    </div>
                    <div class="enseignant-info-small">
                        <div class="enseignant-name">${enseignant.name}</div>
                        <div class="enseignant-specialities">${enseignant.specialites || 'Aucune spécialité définie'}</div>
                    </div>
                `;
                container.appendChild(card);
            });
        })
        .catch(error => {
            console.error('Error loading enseignants:', error);
            document.getElementById('enseignantsList').innerHTML =
                '<div class="alert alert-danger">Erreur lors du chargement des enseignants</div>';
        });
}

// Load statistics
function loadStatistics() {
    fetch('/chef/statistics')
        .then(response => response.json())
        .then(data => {
            document.getElementById('totalUEsVacantes').textContent = data.ues_vacantes || 0;
        })
        .catch(error => {
            console.error('Error loading statistics:', error);
        });
}

// Select enseignant
function selectEnseignant(enseignant) {
    selectedEnseignant = enseignant;

    // Update UI
    document.querySelectorAll('.enseignant-card').forEach(card => {
        card.classList.remove('selected');
    });
    event.currentTarget.classList.add('selected');

    // Show selected enseignant info
    document.getElementById('selectedEnseignantInfo').style.display = 'block';
    document.getElementById('selectedEnseignantDetails').innerHTML = `
        <strong>${enseignant.name}</strong><br>
        <small>${enseignant.email}</small>
    `;
    document.getElementById('selectedEnseignantSpecialities').textContent =
        enseignant.specialites || 'Aucune spécialité définie';

    // Load compatible UEs
    loadCompatibleUEs(enseignant.id);
}

// Load UEs compatible with selected enseignant
function loadCompatibleUEs(enseignantId) {
    fetch(`/chef/compatible-ues/${enseignantId}`)
        .then(response => response.json())
        .then(data => {
            availableUEs = data;
            renderAvailableUEs();
        })
        .catch(error => {
            console.error('Error loading compatible UEs:', error);
            document.getElementById('availableUEs').innerHTML =
                '<div class="alert alert-danger">Erreur lors du chargement des UEs</div>';
        });
}

// Render available UEs
function renderAvailableUEs() {
    const container = document.getElementById('availableUEs');
    container.innerHTML = '';

    if (availableUEs.length === 0) {
        container.innerHTML = `
            <div class="empty-state">
                <i class="fas fa-graduation-cap"></i>
                <p>Aucune UE compatible trouvée</p>
            </div>
        `;
        return;
    }

    availableUEs.forEach(ue => {
        const ueElement = createUEElement(ue);
        container.appendChild(ueElement);
    });

    updateCounts();
}

// Create UE element
function createUEElement(ue) {
    const div = document.createElement('div');
    div.className = 'ue-item-draggable';
    div.draggable = true;
    div.dataset.ueId = ue.id;
    div.ondragstart = (e) => dragStart(e, ue);
    div.ondragend = dragEnd;

    div.innerHTML = `
        <div class="ue-item-header">
            <span class="ue-code">${ue.code}</span>
            <span class="badge bg-primary ue-type-badge">${ue.type || 'CM'}</span>
        </div>
        <div class="ue-name">${ue.nom}</div>
        <div class="ue-details-small">
            <span><i class="fas fa-layer-group me-1"></i>${ue.filiere_nom || 'N/A'}</span>
            <span><i class="fas fa-clock me-1"></i>${ue.total_hours || 0}h</span>
        </div>
    `;

    return div;
}

// Drag and drop functions
function dragStart(e, ue) {
    e.dataTransfer.setData('text/plain', JSON.stringify(ue));
    e.currentTarget.classList.add('dragging');
}

function dragEnd(e) {
    e.currentTarget.classList.remove('dragging');
}

function allowDrop(e) {
    e.preventDefault();
    e.currentTarget.classList.add('drag-over');
}

function dropUE(e) {
    e.preventDefault();
    e.currentTarget.classList.remove('drag-over');

    const ueData = JSON.parse(e.dataTransfer.getData('text/plain'));

    // Check if UE is already assigned
    if (assignedUEs.find(ue => ue.id === ueData.id)) {
        showAlert('Cette UE est déjà dans la liste d\'affectation', 'warning');
        return;
    }

    // Move UE from available to assigned
    assignedUEs.push(ueData);
    availableUEs = availableUEs.filter(ue => ue.id !== ueData.id);

    // Re-render both lists
    renderAvailableUEs();
    renderAssignedUEs();

    // Enable save button
    document.getElementById('saveAllBtn').disabled = assignedUEs.length === 0;
    document.getElementById('saveAllAssignmentsBtn').disabled = assignedUEs.length === 0;

    // Update session assignments counter
    document.getElementById('sessionAssignments').textContent = assignedUEs.length;
}

// Render assigned UEs
function renderAssignedUEs() {
    const container = document.getElementById('assignedUEs');
    container.innerHTML = '';

    if (assignedUEs.length === 0) {
        container.innerHTML = `
            <div class="drop-zone" id="dropZone">
                <i class="fas fa-hand-point-right mb-2"></i>
                <p>Glissez-déposez les UEs ici pour les affecter</p>
                <small class="text-muted">Les UEs compatibles avec les spécialités de l'enseignant apparaîtront à gauche</small>
            </div>
        `;
    } else {
        assignedUEs.forEach(ue => {
            const ueElement = createAssignedUEElement(ue);
            container.appendChild(ueElement);
        });
    }

    updateCounts();
}

// Create assigned UE element
function createAssignedUEElement(ue) {
    const div = document.createElement('div');
    div.className = 'ue-item-draggable';
    div.dataset.ueId = ue.id;

    div.innerHTML = `
        <div class="ue-item-header">
            <span class="ue-code">${ue.code}</span>
            <button class="btn btn-sm btn-outline-danger" onclick="removeAssignedUE(${ue.id})">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="ue-name">${ue.nom}</div>
        <div class="ue-details-small">
            <span><i class="fas fa-layer-group me-1"></i>${ue.filiere_nom || 'N/A'}</span>
            <span><i class="fas fa-clock me-1"></i>${ue.total_hours || 0}h</span>
        </div>
    `;

    return div;
}

// Remove UE from assigned list
function removeAssignedUE(ueId) {
    const ue = assignedUEs.find(u => u.id === ueId);
    if (ue) {
        assignedUEs = assignedUEs.filter(u => u.id !== ueId);
        availableUEs.push(ue);

        renderAvailableUEs();
        renderAssignedUEs();

        document.getElementById('saveAllBtn').disabled = assignedUEs.length === 0;
        document.getElementById('saveAllAssignmentsBtn').disabled = assignedUEs.length === 0;
        document.getElementById('sessionAssignments').textContent = assignedUEs.length;
    }
}

// Update counts
function updateCounts() {
    document.getElementById('availableCount').textContent = availableUEs.length;
    document.getElementById('assignedCount').textContent = assignedUEs.length;
}

// Reset all assignments
function resetAllAssignments() {
    assignedUEs = [];
    selectedEnseignant = null;

    // Reset UI
    document.querySelectorAll('.enseignant-card').forEach(card => {
        card.classList.remove('selected');
    });

    document.getElementById('selectedEnseignantInfo').style.display = 'none';
    document.getElementById('availableUEs').innerHTML = `
        <div class="empty-state">
            <i class="fas fa-graduation-cap"></i>
            <p>Sélectionnez un enseignant pour voir les UEs compatibles</p>
        </div>
    `;

    renderAssignedUEs();
    document.getElementById('saveAllBtn').disabled = true;
    document.getElementById('saveAllAssignmentsBtn').disabled = true;
    document.getElementById('sessionAssignments').textContent = '0';
    updateCounts();
}

// Save all assignments
function saveAllAssignments() {
    if (!selectedEnseignant || assignedUEs.length === 0) {
        showAlert('Veuillez sélectionner un enseignant et au moins une UE', 'warning');
        return;
    }

    const saveBtn = document.getElementById('saveAllBtn');
    const saveBtn2 = document.getElementById('saveAllAssignmentsBtn');
    const originalText = saveBtn.innerHTML;

    saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Sauvegarde...';
    saveBtn2.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Sauvegarde...';
    saveBtn.disabled = true;
    saveBtn2.disabled = true;

    const data = {
        enseignant_id: selectedEnseignant.id,
        ues: assignedUEs.map(ue => ({
            ue_id: ue.id,
            type_seance: ue.type || 'CM'
        }))
    };

    fetch('/chef/save-drag-drop-assignments', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert(`${data.created_count} affectation(s) créée(s) avec succès!`, 'success');
            resetAllAssignments();
            loadStatistics(); // Refresh statistics
        } else {
            showAlert('Erreur lors de la sauvegarde: ' + (data.message || 'Erreur inconnue'), 'danger');
        }
    })
    .catch(error => {
        console.error('Error saving assignments:', error);
        showAlert('Erreur lors de la sauvegarde', 'danger');
    })
    .finally(() => {
        saveBtn.innerHTML = originalText;
        saveBtn2.innerHTML = originalText;
        saveBtn.disabled = assignedUEs.length === 0;
        saveBtn2.disabled = assignedUEs.length === 0;
    });
}

// Show alert
function showAlert(message, type) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.body.appendChild(alertDiv);

    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}
</script>
@endpush