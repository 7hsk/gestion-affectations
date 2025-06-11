@extends('layouts.coordonnateur')

@section('title', 'Gestion des Vacataires')

@push('styles')
<style>
/* Coordonnateur Green Theme - Same structure as chef */
.drag-drop-interface {
    display: flex;
    gap: 1rem;
    height: 600px;
    margin-bottom: 2rem;
}

.enseignant-panel {
    flex: 0 0 280px;
    background: white;
    border-radius: 15px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.panel-title {
    background: linear-gradient(135deg, #059669, #10b981);
    color: white;
    padding: 1rem 1.5rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.ue-panel {
    flex: 1;
    background: white;
    border-radius: 15px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    display: flex;
    flex-direction: column;
}

.available-panel .ue-panel-header {
    background: linear-gradient(135deg, #f59e0b, #f97316);
    color: white;
}

.assigned-panel .ue-panel-header {
    background: linear-gradient(135deg, #3b82f6, #1d4ed8);
    color: white;
}

.ue-panel-header {
    padding: 1rem 1.5rem;
    font-weight: 600;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.ue-panel-content {
    flex: 1;
    padding: 1rem;
    overflow-y: auto;
    min-height: 0;
}

/* Vacataire cards (same as enseignant cards in chef) */
.enseignant-card {
    background: white;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    padding: 1rem;
    margin-bottom: 1rem;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.enseignant-card:hover {
    border-color: #059669;
    transform: translateY(-2px);
    box-shadow: 0 5px 20px rgba(5, 150, 105, 0.2);
}

.enseignant-card.selected {
    border-color: #059669;
    background: #f0fdf4;
    box-shadow: 0 5px 20px rgba(5, 150, 105, 0.3);
}

/* UE Items (same as chef) */
.ue-item {
    background: white;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    padding: 1rem;
    margin-bottom: 1rem;
    cursor: grab;
    transition: all 0.3s ease;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.ue-item:hover {
    border-color: #f59e0b;
    transform: translateY(-2px);
    box-shadow: 0 5px 20px rgba(245, 158, 11, 0.2);
}

.ue-item.dragging {
    opacity: 0.7;
    transform: rotate(5deg);
    cursor: grabbing;
}

.loading {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 200px;
    color: #6b7280;
}

.loading i {
    font-size: 2rem;
    margin-bottom: 1rem;
    animation: spin 1s linear infinite;
}

.count-badge {
    background: rgba(255, 255, 255, 0.9);
    color: #374151;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
}

.drop-zone {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 300px;
    border: 2px dashed #e2e8f0;
    border-radius: 15px;
    color: #6b7280;
    text-align: center;
    transition: all 0.3s ease;
}

.drop-zone.drag-over {
    border-color: #3b82f6;
    background: rgba(59, 130, 246, 0.1);
    color: #3b82f6;
}

/* Save button */
.save-section {
    display: flex;
    justify-content: center;
    margin-top: 2rem;
}

.btn-save-affectations {
    background: linear-gradient(135deg, #059669, #10b981);
    color: white;
    border: none;
    padding: 1rem 2rem;
    border-radius: 12px;
    font-weight: 600;
    font-size: 1.1rem;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(5, 150, 105, 0.3);
}

.btn-save-affectations:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(5, 150, 105, 0.4);
}

.btn-save-affectations:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

/* Responsive design */
@media (max-width: 768px) {
    .drag-drop-interface {
        flex-direction: column;
        height: auto;
    }
    
    .enseignant-panel {
        flex: none;
    }
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <i class="fas fa-user-clock me-2"></i>Gestion des Vacataires
            </h2>
            <p class="text-muted mb-0">Affectez les UEs disponibles aux vacataires de votre département</p>
        </div>
        <button class="btn btn-outline-success" onclick="location.reload()">
            <i class="fas fa-sync-alt me-2"></i>Actualiser
        </button>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-user-clock"></i>
                </div>
                <div class="stat-content">
                    <h5 class="mb-0" id="totalVacataires">0</h5>
                    <small class="text-muted">Vacataires</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <div class="stat-content">
                    <h5 class="mb-0" id="totalUEs">{{ $uesDisponibles->count() }}</h5>
                    <small class="text-muted">UEs Disponibles</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-list"></i>
                </div>
                <div class="stat-content">
                    <h5 class="mb-0" id="availableCount">0</h5>
                    <small class="text-muted">UEs Compatibles</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-content">
                    <h5 class="mb-0" id="assignedCount">0</h5>
                    <small class="text-muted">UEs à Affecter</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Drag Drop Interface (same as chef) -->
    <div class="drag-drop-interface">
        <!-- Vacataire Panel -->
        <div class="enseignant-panel">
            <div class="panel-title">
                <i class="fas fa-user-clock"></i>Sélectionner un Vacataire
            </div>

            <div id="vacatairesList">
                <div class="loading">
                    <i class="fas fa-spinner"></i>
                    <p>Chargement des vacataires...</p>
                </div>
            </div>
        </div>

        <!-- Available UEs Panel -->
        <div class="ue-panel available-panel">
            <div class="ue-panel-header">
                <i class="fas fa-list me-2"></i>UEs Disponibles
                <span class="count-badge" id="availableCount">0</span>
            </div>
            <div class="ue-panel-content" id="availableUEs">
                <div class="empty-state">
                    <i class="fas fa-graduation-cap"></i>
                    <p>Sélectionnez un vacataire pour voir les UEs compatibles</p>
                </div>
            </div>
        </div>

        <!-- Assigned UEs Panel -->
        <div class="ue-panel assigned-panel">
            <div class="ue-panel-header">
                <i class="fas fa-check-circle me-2"></i>UEs à Affecter
                <span class="count-badge" id="assignedCount">0</span>
            </div>
            <div class="ue-panel-content" id="assignedUEs" ondrop="handleDrop(event)" ondragover="handleDragOver(event)">
                <div class="drop-zone" id="dropZone">
                    <i class="fas fa-hand-point-right"></i>
                    <h5>Glissez-déposez les UEs ici</h5>
                    <p>Les UEs compatibles avec les spécialités du vacataire apparaîtront à gauche</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Save Button -->
    <div class="save-section">
        <button class="btn-save-affectations" id="saveBtn" onclick="saveAffectations()" disabled>
            <i class="fas fa-save me-2"></i>Sauvegarder les Affectations
            <span class="badge bg-light text-dark ms-2" id="saveCount">0</span>
        </button>
    </div>
</div>

<!-- Loading Overlay Template -->
<div class="loading-overlay d-none" id="loadingOverlay">
    <div class="spinner"></div>
</div>

@push('scripts')
<script>
// Global variables (same as chef)
let selectedVacataire = null;
let assignedUEs = [];
let availableUEs = [];

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    loadVacataires();
    updateCounts();
    addDynamicStyles();
});

// Load vacataires list (same as chef's loadEnseignants)
function loadVacataires() {
    const container = document.getElementById('vacatairesList');
    container.innerHTML = `
        <div class="loading">
            <i class="fas fa-spinner"></i>
            <p>Chargement des vacataires...</p>
        </div>
    `;

    fetch('/coordonnateur/api/vacataires-list')
        .then(response => response.json())
        .then(data => {
            renderVacataires(data);
            document.getElementById('totalVacataires').textContent = data.length;
        })
        .catch(error => {
            console.error('Error loading vacataires:', error);
            container.innerHTML = `
                <div class="empty-state">
                    <i class="fas fa-exclamation-triangle"></i>
                    <p>Erreur lors du chargement des vacataires</p>
                </div>
            `;
        });
}

// Render vacataires list
function renderVacataires(vacataires) {
    const container = document.getElementById('vacatairesList');
    container.innerHTML = '';

    if (vacataires.length === 0) {
        container.innerHTML = `
            <div class="empty-state">
                <i class="fas fa-user-slash"></i>
                <p>Aucun vacataire disponible</p>
            </div>
        `;
        return;
    }

    vacataires.forEach(vacataire => {
        const vacataireElement = createVacataireElement(vacataire);
        container.appendChild(vacataireElement);
    });
}

// Create vacataire element (clean and simple)
function createVacataireElement(vacataire) {
    const div = document.createElement('div');
    div.className = 'enseignant-card';
    div.dataset.vacataireId = vacataire.id;
    div.onclick = () => selectVacataire(vacataire.id, div);

    div.innerHTML = `
        <div class="d-flex align-items-center">
            <div class="avatar-circle me-3">
                <i class="fas fa-user-clock"></i>
            </div>
            <div class="flex-grow-1">
                <div class="fw-bold">${vacataire.name}</div>
                <small class="text-muted">${vacataire.email}</small>
                <div class="mt-1">
                    <span class="role-badge">Vacataire</span>
                </div>
                ${vacataire.departement ? `<small class="text-muted">${vacataire.departement.nom}</small>` : ''}
            </div>
        </div>
    `;

    return div;
}

// Select vacataire (same as chef's selectEnseignant)
function selectVacataire(vacataireId, element) {
    // Remove previous selection
    document.querySelectorAll('.enseignant-card').forEach(card => {
        card.classList.remove('selected');
    });

    // Select new vacataire
    element.classList.add('selected');
    selectedVacataire = vacataireId;

    // Load compatible UEs
    loadCompatibleUEs(vacataireId);
}

// Load compatible UEs (same as chef)
function loadCompatibleUEs(vacataireId) {
    const container = document.getElementById('availableUEs');
    container.innerHTML = `
        <div class="loading">
            <i class="fas fa-spinner"></i>
            <p>Chargement des UEs...</p>
        </div>
    `;

    fetch(`/coordonnateur/api/compatible-ues/${vacataireId}`)
        .then(response => response.json())
        .then(data => {
            availableUEs = data;
            renderAvailableUEs();
        })
        .catch(error => {
            console.error('Error loading UEs:', error);
            container.innerHTML = `
                <div class="empty-state">
                    <i class="fas fa-exclamation-triangle"></i>
                    <p>Erreur lors du chargement des UEs</p>
                </div>
            `;
        });
}

// Render available UEs (same as chef)
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
        updateCounts();
        return;
    }

    availableUEs.forEach(ue => {
        const ueElement = createUEElement(ue);
        container.appendChild(ueElement);
    });

    updateCounts();
}

// Create UE element (same as chef)
function createUEElement(ue) {
    const div = document.createElement('div');
    div.className = 'ue-item';
    div.dataset.ueId = ue.id;
    div.dataset.selectedTypes = ''; // Track selected types
    div.draggable = false; // Initially not draggable

    // Create type badges
    const typeBadges = ue.vacataire_types.map(type => {
        const badgeClass = type === 'CM' ? 'cm' : (type === 'TD' ? 'td' : 'tp');
        return `<span class="type-badge ${badgeClass}" data-type="${type}" onclick="toggleTypeSelection(this, event)">${type}</span>`;
    }).join('');

    div.innerHTML = `
        <div class="ue-item-header">
            <span class="ue-code">${ue.code}</span>
            <div class="ue-types">${typeBadges}</div>
        </div>
        <div class="ue-name">${ue.nom}</div>
        <div class="ue-details-small">
            <span><i class="fas fa-layer-group me-1"></i>${ue.filiere.nom}</span>
            <span><i class="fas fa-building me-1"></i>${ue.departement.nom}</span>
            ${ue.specialite ? `<span><i class="fas fa-tag me-1"></i>${ue.specialite}</span>` : ''}
        </div>
    `;

    // Add drag event listeners
    div.addEventListener('dragstart', (e) => handleDragStart(e, div));
    div.addEventListener('dragend', handleDragEnd);

    return div;
}

// Toggle type selection (same as chef)
function toggleTypeSelection(badge, event) {
    event.stopPropagation();
    badge.classList.toggle('selected');

    // Update draggable state based on selected types
    const ueElement = badge.closest('.ue-item');
    const selectedTypes = Array.from(ueElement.querySelectorAll('.type-badge.selected')).map(b => b.dataset.type);

    if (selectedTypes.length > 0) {
        ueElement.dataset.selectedTypes = selectedTypes.join(',');
        ueElement.draggable = true;
        ueElement.style.borderColor = '#f59e0b';
        ueElement.style.borderWidth = '3px';
    } else {
        delete ueElement.dataset.selectedTypes;
        ueElement.draggable = false;
        ueElement.style.borderColor = '#e2e8f0';
        ueElement.style.borderWidth = '2px';
    }
}

// Drag and drop handlers (same as chef)
function handleDragStart(e, ueElement) {
    const ueId = ueElement.dataset.ueId;
    const selectedTypes = ueElement.dataset.selectedTypes;

    if (!selectedTypes) {
        e.preventDefault();
        return;
    }

    // Find the original UE data
    const ueData = availableUEs.find(ue => ue.id == ueId);
    if (!ueData) {
        e.preventDefault();
        return;
    }

    // Create enhanced UE data with selected types
    const enhancedUE = {
        ...ueData,
        selectedTypes: selectedTypes.split(','),
        draggedFrom: 'available'
    };

    e.dataTransfer.setData('text/plain', JSON.stringify(enhancedUE));
    e.currentTarget.classList.add('dragging');
}

function handleDragEnd(e) {
    e.currentTarget.classList.remove('dragging');
}

function handleDragOver(e) {
    e.preventDefault();
    e.currentTarget.classList.add('drag-over');
}

function handleDrop(e) {
    e.preventDefault();
    e.currentTarget.classList.remove('drag-over');

    const ueData = JSON.parse(e.dataTransfer.getData('text/plain'));

    // Check if already assigned
    if (assignedUEs.find(ue => ue.id === ueData.id)) {
        alert('Cette UE est déjà assignée');
        return;
    }

    // Move UE from available to assigned
    assignedUEs.push(ueData);
    availableUEs = availableUEs.filter(ue => ue.id !== ueData.id);

    // Re-render
    renderAvailableUEs();
    renderAssignedUEs();

    // Enable save buttons
    updateSaveButtons();
}

// Render assigned UEs (same as chef)
function renderAssignedUEs() {
    const container = document.getElementById('assignedUEs');

    if (assignedUEs.length === 0) {
        container.innerHTML = `
            <div class="drop-zone" ondrop="handleDrop(event)" ondragover="handleDragOver(event)">
                <i class="fas fa-hand-point-right"></i>
                <h5>Glissez-déposez les UEs ici</h5>
                <p>Les UEs compatibles avec les spécialités du vacataire apparaîtront à gauche</p>
            </div>
        `;
    } else {
        // Create a container that maintains drop functionality
        const dropContainer = document.createElement('div');
        dropContainer.style.cssText = 'height: 568px; padding: 0.5rem; border: 2px dashed transparent; border-radius: 10px; transition: all 0.3s ease; overflow-y: auto; display: flex; flex-direction: column;';
        dropContainer.ondrop = handleDrop;
        dropContainer.ondragover = handleDragOver;

        assignedUEs.forEach((ue, index) => {
            const ueElement = document.createElement('div');
            ueElement.className = 'ue-item';
            ueElement.style.cssText = 'border-color: #3b82f6; background: #eff6ff; margin-bottom: 0.5rem;';

            const typeBadges = ue.selectedTypes.map(type => {
                const badgeClass = type === 'CM' ? 'cm' : (type === 'TD' ? 'td' : 'tp');
                return `<span class="type-badge ${badgeClass}">${type}</span>`;
            }).join('');

            ueElement.innerHTML = `
                <div class="ue-item-header">
                    <span class="ue-code">${ue.code}</span>
                    <button class="btn btn-sm btn-outline-danger" onclick="removeUE(${ue.id})">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="ue-name">${ue.nom}</div>
                <div class="ue-types">${typeBadges}</div>
            `;

            dropContainer.appendChild(ueElement);
        });

        container.innerHTML = '';
        container.appendChild(dropContainer);
    }
}

// Remove UE from assigned list and restore to available (same as chef)
function removeUE(ueId) {
    const ue = assignedUEs.find(u => u.id == ueId);
    if (ue) {
        // Remove from assigned
        assignedUEs = assignedUEs.filter(u => u.id != ueId);

        // Add back to available with preserved selections
        availableUEs.push(ue);

        // Re-render both panels
        renderAvailableUEs();
        renderAssignedUEs();
        updateSaveButtons();

        // Restore the type selections after rendering
        setTimeout(() => {
            restoreUETypeSelections(ueId, ue.selectedTypes || []);
        }, 50);
    }
}

// Restore UE type selections (same as chef)
function restoreUETypeSelections(ueId, selectedTypes) {
    const ueElement = document.querySelector(`[data-ue-id="${ueId}"]`);
    if (ueElement && selectedTypes.length > 0) {
        selectedTypes.forEach(type => {
            const badge = ueElement.querySelector(`[data-type="${type}"]`);
            if (badge) {
                badge.classList.add('selected');
            }
        });

        // Update the element's selected types
        ueElement.dataset.selectedTypes = selectedTypes.join(',');
        ueElement.draggable = true;
        ueElement.style.borderColor = '#f59e0b';
        ueElement.style.borderWidth = '3px';
    }
}

// Update counts (same as chef)
function updateCounts() {
    document.getElementById('availableCount').textContent = availableUEs.length;
    document.getElementById('assignedCount').textContent = assignedUEs.length;
    document.getElementById('saveCount').textContent = assignedUEs.length;
}

// Update save buttons (same as chef)
function updateSaveButtons() {
    const hasAssignments = assignedUEs.length > 0 && selectedVacataire;
    document.getElementById('saveBtn').disabled = !hasAssignments;
}

// Save affectations function
function saveAffectations() {
    if (assignedUEs.length === 0) {
        alert('Aucune affectation à sauvegarder');
        return;
    }

    // Implementation for saving affectations
    console.log('Saving affectations:', assignedUEs);
    alert('Fonctionnalité de sauvegarde à implémenter');
}

// Add dynamic CSS styles for elements created by JavaScript
function addDynamicStyles() {
    const style = document.createElement('style');
    style.textContent = `
        .role-badge {
            background: linear-gradient(135deg, #059669, #10b981);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 15px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-block;
        }
        .avatar-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #059669, #10b981);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
        }
        .ue-item-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.5rem;
        }
        .ue-code {
            font-weight: 700;
            color: #f59e0b;
            font-size: 1.1rem;
        }
        .ue-name {
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.5rem;
        }
        .ue-details-small {
            display: flex;
            gap: 1rem;
            font-size: 0.85rem;
            color: #6b7280;
        }
        .ue-types {
            display: flex;
            gap: 0.5rem;
            margin-top: 0.5rem;
        }
        .type-badge {
            padding: 0.25rem 0.5rem;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .type-badge.cm { background: #dcfce7; color: #059669; }
        .type-badge.td { background: #dbeafe; color: #2563eb; }
        .type-badge.tp { background: #fecaca; color: #dc2626; }
        .type-badge:hover { transform: scale(1.1); }
        .type-badge.selected {
            box-shadow: 0 0 0 2px #059669;
            background: #059669 !important;
            color: white !important;
        }
        .empty-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 200px;
            color: #6b7280;
            text-align: center;
        }
        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    `;
    document.head.appendChild(style);
}
</script>
@endpush

@endsection
