@extends('layouts.chef')

@section('title', 'Affectation Interactive des UEs')

@push('styles')
<style>
/* Main Container */
.affectation-container {
    background: #f8f9fa;
    min-height: 100vh;
    padding: 2rem 0;
}

/* Header */
.affectation-header {
    background: white;
    border-radius: 15px;
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.affectation-title {
    color: #2c3e50;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.affectation-subtitle {
    color: #6c757d;
    margin-bottom: 0;
}

/* Drag Drop Interface */
.drag-drop-interface {
    display: grid;
    grid-template-columns: 300px 1fr 1fr;
    gap: 2rem;
    min-height: 600px;
}

/* Enseignant Panel */
.enseignant-panel {
    background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
    border-radius: 15px;
    padding: 1.5rem;
    color: white;
    box-shadow: 0 8px 25px rgba(44, 62, 80, 0.3);
    display: flex;
    flex-direction: column;
    height: 100%;
}

.panel-title {
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
}

.panel-title i {
    margin-right: 0.5rem;
}

/* Enseignant List Container */
#enseignantsList {
    height: 600px;
    overflow-y: auto;
    padding-right: 0.5rem;
}

/* Custom Scrollbar */
#enseignantsList::-webkit-scrollbar,
.ue-panel-content::-webkit-scrollbar {
    width: 6px;
}

#enseignantsList::-webkit-scrollbar-track,
.ue-panel-content::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 3px;
}

#enseignantsList::-webkit-scrollbar-thumb,
.ue-panel-content::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.3);
    border-radius: 3px;
}

#enseignantsList::-webkit-scrollbar-thumb:hover,
.ue-panel-content::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.5);
}

/* UE Panels */
.ue-panel {
    background: white;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    overflow: hidden;
    display: flex;
    flex-direction: column;
    height: 100%;
}

.ue-panel-header {
    padding: 1.5rem;
    font-weight: 600;
    text-align: center;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
}

.available-panel .ue-panel-header {
    background: linear-gradient(135deg, #fd7e14, #e55a00);
}

.assigned-panel .ue-panel-header {
    background: linear-gradient(135deg, #2c3e50, #34495e);
}

.ue-panel-content {
    padding: 0.5rem;
    height: 600px;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
}

/* Enseignant Cards */
.enseignant-card {
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 8px;
    padding: 0.75rem;
    margin-bottom: 0.5rem;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
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

.enseignant-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #007bff, #0056b3);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    margin-right: 0.75rem;
    font-size: 0.9rem;
}

.enseignant-info h6 {
    margin: 0 0 0.25rem 0;
    font-weight: 600;
}

.enseignant-info small {
    opacity: 0.8;
    font-size: 0.75rem;
}

/* UE Items */
.ue-item {
    background: white;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    padding: 0.75rem;
    margin-bottom: 0.5rem;
    cursor: grab;
    transition: all 0.3s ease;
    box-shadow: 0 2px 6px rgba(0,0,0,0.08);
    width: 100%;
    box-sizing: border-box;
    flex-shrink: 0;
}

.ue-item:hover {
    transform: translateY(-1px);
    box-shadow: 0 3px 12px rgba(0,0,0,0.12);
    border-color: #007bff;
}

.ue-item:active {
    cursor: grabbing;
}

.ue-item.dragging {
    opacity: 0.6;
    transform: rotate(2deg) scale(0.95);
}

.ue-item[draggable="false"] {
    cursor: default;
    opacity: 0.7;
}

.ue-item[draggable="false"]:hover {
    transform: none;
    box-shadow: 0 2px 6px rgba(0,0,0,0.08);
    border-color: #e9ecef;
}

.ue-item-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.5rem;
}

.ue-code {
    font-weight: 700;
    color: #2c3e50;
    font-size: 0.9rem;
}

.ue-type {
    background: #007bff;
    color: white;
    padding: 0.2rem 0.6rem;
    border-radius: 15px;
    font-size: 0.7rem;
    font-weight: 600;
}

/* UE Type Selection */
.ue-type-selector {
    display: flex;
    gap: 0.25rem;
    margin-top: 0.5rem;
    flex-wrap: wrap;
}

.type-option {
    background: #f8f9fa;
    border: 2px solid #dee2e6;
    color: #6c757d;
    padding: 0.2rem 0.5rem;
    border-radius: 12px;
    font-size: 0.65rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    user-select: none;
}

.type-option:hover {
    background: #e9ecef;
    border-color: #adb5bd;
}

.type-option.selected {
    background: #fd7e14;
    border-color: #fd7e14;
    color: white;
    transform: scale(1.05);
}

.type-option.cm.selected {
    background: #28a745;
    border-color: #28a745;
}

.type-option.td.selected {
    background: #007bff;
    border-color: #007bff;
}

.type-option.tp.selected {
    background: #dc3545;
    border-color: #dc3545;
}

.ue-name {
    font-size: 0.8rem;
    color: #495057;
    margin-bottom: 0.5rem;
    font-weight: 500;
    line-height: 1.3;
}

.ue-details {
    display: flex;
    justify-content: space-between;
    font-size: 0.7rem;
    color: #6c757d;
}

.ue-details span {
    display: flex;
    align-items: center;
}

.ue-details i {
    margin-right: 0.2rem;
    font-size: 0.65rem;
}

/* Drop Zone */
.drop-zone {
    border: 2px dashed #dee2e6;
    border-radius: 10px;
    padding: 2rem 1rem;
    text-align: center;
    color: #6c757d;
    background: #f8f9fa;
    transition: all 0.3s ease;
    height: 568px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
}

.drop-zone.drag-over {
    border-color: #28a745;
    background: rgba(40, 167, 69, 0.1);
    color: #28a745;
    transform: scale(1.01);
}

.drop-zone i {
    font-size: 2.5rem;
    margin-bottom: 1rem;
    opacity: 0.5;
}

.drop-zone h5 {
    font-size: 1.1rem;
    margin-bottom: 0.5rem;
}

.drop-zone p {
    font-size: 0.9rem;
    margin: 0;
}

/* Empty States */
.empty-state {
    text-align: center;
    padding: 2rem 1rem;
    color: #6c757d;
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
}

.empty-state i {
    font-size: 2.5rem;
    margin-bottom: 1rem;
    opacity: 0.3;
}



/* Action Buttons */
.action-buttons {
    background: white;
    border-radius: 15px;
    padding: 1.5rem;
    margin-top: 2rem;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.btn-reset {
    background: #6c757d;
    border: none;
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-reset:hover {
    background: #5a6268;
    transform: translateY(-2px);
}

.btn-save {
    background: linear-gradient(135deg, #28a745, #20c997);
    border: none;
    color: white;
    padding: 0.75rem 2rem;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-save:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
}

.btn-save:disabled {
    background: #dee2e6;
    color: #6c757d;
    cursor: not-allowed;
    transform: none;
    box-shadow: none;
}

/* Count Badges */
.count-badge {
    background: rgba(255, 255, 255, 0.2);
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    margin-left: 0.5rem;
}

/* Remove Button */
.btn-remove {
    background: #dc3545;
    border: none;
    color: white;
    padding: 0.25rem 0.5rem;
    border-radius: 5px;
    font-size: 0.75rem;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-remove:hover {
    background: #c82333;
    transform: scale(1.1);
}

/* UE Return Animation */
.ue-returning {
    animation: returnToAvailable 0.5s ease-out;
}

@keyframes returnToAvailable {
    0% {
        transform: translateX(0) scale(1);
        opacity: 1;
    }
    50% {
        transform: translateX(-20px) scale(0.95);
        opacity: 0.7;
    }
    100% {
        transform: translateX(0) scale(1);
        opacity: 1;
    }
}

/* Loading */
.loading {
    text-align: center;
    padding: 2rem;
    color: #6c757d;
}

.loading i {
    font-size: 2rem;
    margin-bottom: 1rem;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

@keyframes pulse {
    0% {
        transform: scale(1);
        box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
    }
    50% {
        transform: scale(1.05);
        box-shadow: 0 8px 25px rgba(40, 167, 69, 0.6);
    }
    100% {
        transform: scale(1);
        box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
    }
}

/* Responsive */
@media (max-width: 1200px) {
    .drag-drop-interface {
        grid-template-columns: 280px 1fr 1fr;
        gap: 1.5rem;
    }
}

@media (max-width: 992px) {
    .drag-drop-interface {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }

    .enseignant-panel {
        order: 1;
    }

    .available-panel {
        order: 2;
    }

    .assigned-panel {
        order: 3;
    }
}
</style>
@endpush

@section('content')
<div class="affectation-container">
    <div class="container-fluid">
        <!-- Header -->
        <div class="affectation-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="affectation-title">
                        <i class="fas fa-magic me-2"></i>Affectation Interactive des UEs
                    </h1>
                    <p class="affectation-subtitle" id="dynamicSubtitle">Glissez-déposez les UEs pour affecter les enseignants de manière interactive</p>
                </div>
                <div>
                    <button class="btn btn-reset me-2" onclick="resetAll()">
                        <i class="fas fa-undo me-2"></i>Réinitialiser
                    </button>
                    <button class="btn btn-save" id="saveBtn" onclick="saveAssignments()" disabled>
                        <i class="fas fa-save me-2"></i>Sauvegarder
                    </button>
                </div>
            </div>
        </div>

        <!-- Main Drag Drop Interface -->
        <div class="drag-drop-interface">
            <!-- Enseignant Panel -->
            <div class="enseignant-panel">
                <div class="panel-title">
                    <i class="fas fa-users"></i>Sélectionner un Enseignant
                </div>

                <div id="enseignantsList">
                    <div class="loading">
                        <i class="fas fa-spinner"></i>
                        <p>Chargement des enseignants...</p>
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
                        <p>Sélectionnez un enseignant pour voir les UEs compatibles</p>
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
                        <p>Les UEs compatibles avec les spécialités de l'enseignant apparaîtront à gauche</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="action-buttons">
            <div>
                <span class="text-muted">
                    <i class="fas fa-info-circle me-2"></i>
                    Glissez les UEs de gauche à droite pour les affecter à l'enseignant sélectionné
                </span>
            </div>
            <div>
                <span class="text-muted me-3">
                    <strong>Affectations en cours:</strong> <span id="sessionCount">0</span>
                </span>
                <button class="btn btn-reset me-2" onclick="resetAll()">
                    <i class="fas fa-undo me-2"></i>Réinitialiser
                </button>
                <button class="btn btn-save" id="saveBtn2" onclick="saveAssignments()" disabled>
                    <i class="fas fa-save me-2"></i>Sauvegarder les Affectations
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Global Variables
let selectedEnseignant = null;
let availableUEs = [];
let assignedUEs = [];
let enseignantsData = @json($enseignants); // Get enseignants from server

// Initialize when page loads
document.addEventListener('DOMContentLoaded', function() {
    loadEnseignants();

    // Check for pre-selected enseignant from URL parameter
    const urlParams = new URLSearchParams(window.location.search);
    const preSelectedEnseignantId = urlParams.get('enseignant_id');

    if (preSelectedEnseignantId) {
        // Wait a bit for enseignants to load, then auto-select
        setTimeout(() => {
            autoSelectEnseignant(preSelectedEnseignantId);
        }, 500);
    }
});

// Load enseignants list
function loadEnseignants() {
    console.log('🔄 Loading enseignants from server data...');
    console.log('📊 Enseignants data:', enseignantsData);

    const container = document.getElementById('enseignantsList');
    container.innerHTML = '';

    if (!enseignantsData || enseignantsData.length === 0) {
        container.innerHTML = `
            <div class="empty-state">
                <i class="fas fa-user-times"></i>
                <p>Aucun enseignant trouvé dans votre département</p>
            </div>
        `;
        return;
    }

    enseignantsData.forEach(enseignant => {
        console.log('👨‍🏫 Creating card for:', enseignant.name);

        const card = document.createElement('div');
        card.className = 'enseignant-card';
        card.onclick = () => selectEnseignant(enseignant);
        card.innerHTML = `
            <div class="enseignant-avatar">
                ${enseignant.name.substring(0, 2).toUpperCase()}
            </div>
            <div class="enseignant-info">
                <h6>${enseignant.name}</h6>
                <small>${enseignant.email}</small>
                ${enseignant.specialite ? `<div class="mt-1"><small class="text-muted">${enseignant.specialite}</small></div>` : ''}
            </div>
        `;
        container.appendChild(card);
    });

    console.log(`✅ Created ${enseignantsData.length} enseignant cards from server data`);
}

// Auto-select enseignant by ID (for pre-selection from URL parameter)
function autoSelectEnseignant(enseignantId) {
    console.log('🎯 Auto-selecting enseignant with ID:', enseignantId);

    // Find the enseignant in the data
    const enseignant = enseignantsData.find(e => e.id == enseignantId);

    if (!enseignant) {
        console.warn('⚠️ Enseignant not found with ID:', enseignantId);
        return;
    }

    console.log('✅ Found enseignant for auto-selection:', enseignant.name);

    // Find the corresponding card element
    const cards = document.querySelectorAll('.enseignant-card');
    let targetCard = null;

    cards.forEach(card => {
        const cardName = card.querySelector('h6').textContent;
        if (cardName === enseignant.name) {
            targetCard = card;
        }
    });

    if (targetCard) {
        // Simulate click on the card
        console.log('🖱️ Simulating click on enseignant card');

        // Set the selected enseignant
        selectedEnseignant = enseignant;

        // Update UI - remove selection from all cards
        document.querySelectorAll('.enseignant-card').forEach(card => {
            card.classList.remove('selected');
        });

        // Add selection to target card with special pre-selected styling
        targetCard.classList.add('selected');
        targetCard.style.animation = 'pulse 2s ease-in-out 3';

        // Load compatible UEs
        loadCompatibleUEs(enseignant.id);

        // Scroll the enseignant into view
        targetCard.scrollIntoView({
            behavior: 'smooth',
            block: 'center'
        });

        console.log('🎉 Auto-selection completed for:', enseignant.name);

        // Update header subtitle
        const subtitle = document.getElementById('dynamicSubtitle');
        if (subtitle) {
            subtitle.innerHTML = `<i class="fas fa-user-check me-2"></i>Enseignant <strong>${enseignant.name}</strong> pré-sélectionné - Choisissez les UEs à affecter`;
            subtitle.style.color = '#28a745';
            subtitle.style.fontWeight = '600';
        }

        // Show notification that enseignant was pre-selected
        setTimeout(() => {
            showAlert(`Enseignant "${enseignant.name}" pré-sélectionné. Vous pouvez maintenant choisir les UEs à affecter.`, 'info');
        }, 1000);

    } else {
        console.warn('⚠️ Could not find card element for enseignant:', enseignant.name);
    }
}

// Select enseignant
function selectEnseignant(enseignant) {
    selectedEnseignant = enseignant;

    // Update UI
    document.querySelectorAll('.enseignant-card').forEach(card => {
        card.classList.remove('selected');
    });
    event.currentTarget.classList.add('selected');



    // Load compatible UEs
    loadCompatibleUEs(enseignant.id);
}

// Load compatible UEs
function loadCompatibleUEs(enseignantId) {
    const container = document.getElementById('availableUEs');
    container.innerHTML = `
        <div class="loading">
            <i class="fas fa-spinner"></i>
            <p>Chargement des UEs...</p>
        </div>
    `;

    fetch(`/chef/compatible-ues/${enseignantId}`)
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
        updateCounts();
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
    div.className = 'ue-item';
    div.dataset.ueId = ue.id;
    div.dataset.selectedTypes = ''; // Track selected types

    div.innerHTML = `
        <div class="ue-item-header">
            <span class="ue-code">${ue.code}</span>
            <span class="ue-type">${ue.type || 'Disponible'}</span>
        </div>
        <div class="ue-name">${ue.nom}</div>
        <div class="ue-details">
            <span><i class="fas fa-layer-group"></i>${ue.filiere_nom || 'N/A'}</span>
            <span><i class="fas fa-clock"></i>${ue.total_hours || 0}h</span>
        </div>
        <div class="ue-type-selector">
            <div class="type-option cm" onclick="toggleUEType(${ue.id}, 'CM', this)">CM</div>
            <div class="type-option td" onclick="toggleUEType(${ue.id}, 'TD', this)">TD</div>
            <div class="type-option tp" onclick="toggleUEType(${ue.id}, 'TP', this)">TP</div>
        </div>
    `;

    return div;
}

// Toggle UE type selection
function toggleUEType(ueId, type, element) {
    const ueElement = document.querySelector(`[data-ue-id="${ueId}"]`);
    if (!ueElement) return;

    const isSelected = element.classList.contains('selected');

    if (isSelected) {
        // Deselect this type
        element.classList.remove('selected');
    } else {
        // Select this type
        element.classList.add('selected');
    }

    // Update selected types
    const selectedTypes = [];
    ueElement.querySelectorAll('.type-option.selected').forEach(option => {
        selectedTypes.push(option.textContent);
    });

    ueElement.dataset.selectedTypes = selectedTypes.join(',');

    // Update the main type badge
    const typeBadge = ueElement.querySelector('.ue-type');
    if (selectedTypes.length === 0) {
        typeBadge.textContent = 'Disponible';
        typeBadge.style.background = '#6c757d';
        ueElement.draggable = false;
    } else if (selectedTypes.length === 1) {
        typeBadge.textContent = selectedTypes[0];
        typeBadge.style.background = getTypeColor(selectedTypes[0]);
        ueElement.draggable = true;
    } else {
        typeBadge.textContent = `${selectedTypes.length} Types`;
        typeBadge.style.background = '#fd7e14';
        ueElement.draggable = true;
    }

    // Update drag handlers
    if (selectedTypes.length > 0) {
        ueElement.ondragstart = (e) => handleDragStart(e, ueElement);
        ueElement.ondragend = handleDragEnd;
    } else {
        ueElement.ondragstart = null;
        ueElement.ondragend = null;
    }
}

// Get color for UE type
function getTypeColor(type) {
    switch(type) {
        case 'CM': return '#28a745';
        case 'TD': return '#007bff';
        case 'TP': return '#dc3545';
        default: return '#6c757d';
    }
}

// Create assigned UE element
function createAssignedUEElement(ue) {
    const div = document.createElement('div');
    div.className = 'ue-item';
    div.dataset.ueId = ue.id;

    // Display selected types
    const typesDisplay = ue.selectedTypes && ue.selectedTypes.length > 0
        ? ue.selectedTypes.map(type => `<span class="ue-type" style="background: ${getTypeColor(type)}; margin-right: 0.25rem;">${type}</span>`).join('')
        : '<span class="ue-type">CM</span>';

    div.innerHTML = `
        <div class="ue-item-header">
            <span class="ue-code">${ue.code}</span>
            <button class="btn-remove" onclick="removeUE(${ue.id})">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="ue-name">${ue.nom}</div>
        <div class="ue-details">
            <span><i class="fas fa-layer-group"></i>${ue.filiere_nom || 'N/A'}</span>
            <span><i class="fas fa-clock"></i>${ue.total_hours || 0}h</span>
        </div>
        <div style="margin-top: 0.5rem;">
            ${typesDisplay}
        </div>
    `;

    return div;
}

// Drag and drop handlers
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
    if (e.currentTarget.classList.contains('drop-zone')) {
        e.currentTarget.classList.add('drag-over');
    } else {
        e.currentTarget.style.borderColor = '#28a745';
        e.currentTarget.style.backgroundColor = 'rgba(40, 167, 69, 0.1)';
    }
}

function handleDrop(e) {
    e.preventDefault();
    e.currentTarget.classList.remove('drag-over');

    // Reset styles for container
    if (!e.currentTarget.classList.contains('drop-zone')) {
        e.currentTarget.style.borderColor = 'transparent';
        e.currentTarget.style.backgroundColor = 'transparent';
    }

    const ueData = JSON.parse(e.dataTransfer.getData('text/plain'));

    // Check if already assigned
    if (assignedUEs.find(ue => ue.id === ueData.id)) {
        showAlert('Cette UE est déjà assignée', 'warning');
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

// Render assigned UEs
function renderAssignedUEs() {
    const container = document.getElementById('assignedUEs');
    container.innerHTML = '';

    if (assignedUEs.length === 0) {
        container.innerHTML = `
            <div class="drop-zone" ondrop="handleDrop(event)" ondragover="handleDragOver(event)">
                <i class="fas fa-hand-point-right"></i>
                <h5>Glissez-déposez les UEs ici</h5>
                <p>Les UEs compatibles avec les spécialités de l'enseignant apparaîtront à gauche</p>
            </div>
        `;
    } else {
        // Create a container that maintains drop functionality
        const dropContainer = document.createElement('div');
        dropContainer.style.cssText = 'height: 568px; padding: 0.5rem; border: 2px dashed transparent; border-radius: 10px; transition: all 0.3s ease; overflow-y: auto; display: flex; flex-direction: column;';
        dropContainer.ondrop = handleDrop;
        dropContainer.ondragover = handleDragOver;
        dropContainer.ondragleave = (e) => {
            e.currentTarget.style.borderColor = 'transparent';
            e.currentTarget.style.backgroundColor = 'transparent';
        };

        assignedUEs.forEach(ue => {
            const ueElement = createAssignedUEElement(ue);
            dropContainer.appendChild(ueElement);
        });

        container.appendChild(dropContainer);
    }

    updateCounts();
}

// Remove UE from assigned list and restore to available with selections
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

// Restore UE type selections when UE goes back to available
function restoreUETypeSelections(ueId, selectedTypes) {
    const ueElement = document.querySelector(`[data-ue-id="${ueId}"]`);
    if (!ueElement || !selectedTypes || selectedTypes.length === 0) return;

    // Add return animation
    ueElement.classList.add('ue-returning');

    // Restore selected types
    selectedTypes.forEach(type => {
        const typeButton = ueElement.querySelector(`.type-option.${type.toLowerCase()}`);
        if (typeButton) {
            typeButton.classList.add('selected');
        }
    });

    // Update the UE element state
    ueElement.dataset.selectedTypes = selectedTypes.join(',');

    // Update the main type badge
    const typeBadge = ueElement.querySelector('.ue-type');
    if (selectedTypes.length === 1) {
        typeBadge.textContent = selectedTypes[0];
        typeBadge.style.background = getTypeColor(selectedTypes[0]);
    } else if (selectedTypes.length > 1) {
        typeBadge.textContent = `${selectedTypes.length} Types`;
        typeBadge.style.background = '#fd7e14';
    }

    // Make it draggable again
    ueElement.draggable = true;
    ueElement.ondragstart = (e) => handleDragStart(e, ueElement);
    ueElement.ondragend = handleDragEnd;

    // Remove animation class after animation completes
    setTimeout(() => {
        ueElement.classList.remove('ue-returning');
    }, 500);
}

// Update counts
function updateCounts() {
    document.getElementById('availableCount').textContent = availableUEs.length;
    document.getElementById('assignedCount').textContent = assignedUEs.length;
    document.getElementById('sessionCount').textContent = assignedUEs.length;
}

// Update save buttons
function updateSaveButtons() {
    const hasAssignments = assignedUEs.length > 0 && selectedEnseignant;
    document.getElementById('saveBtn').disabled = !hasAssignments;
    document.getElementById('saveBtn2').disabled = !hasAssignments;
}

// Reset all
function resetAll() {
    selectedEnseignant = null;
    availableUEs = [];
    assignedUEs = [];

    // Reset UI
    document.querySelectorAll('.enseignant-card').forEach(card => {
        card.classList.remove('selected');
    });
    document.getElementById('availableUEs').innerHTML = `
        <div class="empty-state">
            <i class="fas fa-graduation-cap"></i>
            <p>Sélectionnez un enseignant pour voir les UEs compatibles</p>
        </div>
    `;

    // Reset header subtitle
    const subtitle = document.getElementById('dynamicSubtitle');
    if (subtitle) {
        subtitle.innerHTML = 'Glissez-déposez les UEs pour affecter les enseignants de manière interactive';
        subtitle.style.color = '';
        subtitle.style.fontWeight = '';
    }

    renderAssignedUEs();
    updateSaveButtons();
    updateCounts();
}

// Save assignments
function saveAssignments() {
    if (!selectedEnseignant || assignedUEs.length === 0) {
        showAlert('Veuillez sélectionner un enseignant et au moins une UE', 'warning');
        return;
    }

    const saveBtn = document.getElementById('saveBtn');
    const saveBtn2 = document.getElementById('saveBtn2');
    const originalText = saveBtn.innerHTML;

    // Show loading
    saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Sauvegarde...';
    saveBtn2.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Sauvegarde...';
    saveBtn.disabled = true;
    saveBtn2.disabled = true;

    const data = {
        enseignant_id: selectedEnseignant.id,
        ues: assignedUEs.flatMap(ue => {
            if (ue.selectedTypes && ue.selectedTypes.length > 0) {
                // Create separate entries for each selected type
                return ue.selectedTypes.map(type => ({
                    ue_id: ue.id,
                    type_seance: type
                }));
            } else {
                // Default to CM if no types selected
                return [{
                    ue_id: ue.id,
                    type_seance: 'CM'
                }];
            }
        })
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
            resetAll();
        } else {
            showAlert('Erreur: ' + (data.message || 'Erreur inconnue'), 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Erreur lors de la sauvegarde', 'danger');
    })
    .finally(() => {
        saveBtn.innerHTML = originalText;
        saveBtn2.innerHTML = originalText;
        updateSaveButtons();
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
