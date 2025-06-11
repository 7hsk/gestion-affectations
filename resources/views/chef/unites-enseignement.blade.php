@extends('layouts.chef')

@section('title', 'Unités d\'Enseignement')

@push('styles')
<style>
.filter-card {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border: 1px solid #dee2e6;
    border-radius: 15px;
    margin-bottom: 2rem;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
}

.ue-card {
    border: none;
    border-radius: 12px;
    transition: all 0.3s ease;
    margin-bottom: 1rem;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
}

.ue-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.ue-header {
    background: linear-gradient(135deg, #2c3e50, #34495e);
    color: white;
    border-radius: 12px 12px 0 0;
    padding: 1.25rem;
    position: relative;
    overflow: hidden;
}

.ue-code {
    font-size: 1.2rem;
    font-weight: 700;
    margin-bottom: 0.25rem;
}

.ue-title {
    font-size: 0.95rem;
    opacity: 0.9;
    margin: 0;
}

.ue-body {
    padding: 1.25rem;
}

.ue-info {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
    gap: 1rem;
    margin-bottom: 1rem;
}

.info-item {
    text-align: center;
    padding: 0.75rem;
    background: #f8f9fa;
    border-radius: 8px;
}

.info-label {
    font-size: 0.8rem;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 0.25rem;
}

.info-value {
    font-size: 1.1rem;
    font-weight: 600;
    color: #2c3e50;
}

.status-badge {
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-weight: 500;
    font-size: 0.85rem;
    white-space: nowrap;
    max-width: 120px;
    overflow: hidden;
    text-overflow: ellipsis;
    display: inline-block;
    position: relative;
    z-index: 1;
}

.status-vacant {
    background: rgba(243, 156, 18, 0.1);
    color: #f39c12;
    border: 1px solid rgba(243, 156, 18, 0.3);
}

.status-assigned {
    background: rgba(39, 174, 96, 0.1);
    color: #27ae60;
    border: 1px solid rgba(39, 174, 96, 0.3);
}

/* Modal styling for remaining modals */
.modal {
    z-index: 9999 !important;
}

.modal-backdrop {
    z-index: 9998 !important;
}

.affectation-info {
    background: #e8f4fd;
    border-left: 4px solid #3498db;
    padding: 1rem;
    border-radius: 0 8px 8px 0;
    margin-top: 1rem;
}

.action-buttons {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.btn-action {
    padding: 0.5rem 1rem;
    border-radius: 6px;
    font-size: 0.85rem;
    font-weight: 500;
    transition: all 0.3s ease;
}

.search-box {
    position: relative;
}

.search-box .form-control {
    padding-left: 2.5rem;
    border-radius: 10px;
    border: 2px solid #e9ecef;
    transition: all 0.3s ease;
    background: white;
    color: #495057;
}

.search-box .form-control:focus {
    border-color: #2c3e50;
    box-shadow: 0 0 0 0.2rem rgba(44, 62, 80, 0.15);
    background: white;
}

.search-box .form-control::placeholder {
    color: #adb5bd;
}

.search-box .search-icon {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: #adb5bd;
}

/* Form Select Styling */
.form-select {
    border-radius: 10px;
    border: 2px solid #e9ecef;
    transition: all 0.3s ease;
    background: white;
    color: #495057;
}

.form-select:focus {
    border-color: #2c3e50;
    box-shadow: 0 0 0 0.2rem rgba(44, 62, 80, 0.15);
    background: white;
}

/* Filter Buttons */
.btn-light {
    background: linear-gradient(135deg, #2c3e50, #34495e);
    border: 2px solid #2c3e50;
    color: white;
    border-radius: 10px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-light:hover {
    background: linear-gradient(135deg, #34495e, #2c3e50);
    border-color: #34495e;
    color: white;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(44, 62, 80, 0.3);
}

.btn-outline-light {
    background: white;
    border: 2px solid #dc3545;
    color: #dc3545;
    border-radius: 10px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-outline-light:hover {
    background: #dc3545;
    border-color: #dc3545;
    color: white;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
}

.filter-badge {
    background: linear-gradient(135deg, #007bff, #0056b3);
    color: white;
    padding: 0.4rem 0.8rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    margin: 0.25rem;
    display: inline-block;
    box-shadow: 0 2px 6px rgba(0, 123, 255, 0.3);
}

/* Filter Section Label */
.filter-section-label {
    color: #6c757d;
    font-weight: 600;
    font-size: 0.85rem;
    margin-bottom: 0.5rem;
}

/* Browser-style Notifications */
.notification-container {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 10000;
    max-width: 400px;
}

.notification {
    background: white;
    border-radius: 8px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.15);
    margin-bottom: 10px;
    padding: 16px;
    border-left: 4px solid;
    animation: slideIn 0.3s ease-out;
    position: relative;
    overflow: hidden;
}

.notification.success {
    border-left-color: #28a745;
}

.notification.error {
    border-left-color: #dc3545;
}

.notification.warning {
    border-left-color: #ffc107;
}

.notification.info {
    border-left-color: #17a2b8;
}

.notification-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 8px;
}

.notification-title {
    font-weight: 600;
    color: #2c3e50;
    font-size: 0.9rem;
    margin: 0;
}

.notification-close {
    background: none;
    border: none;
    color: #6c757d;
    cursor: pointer;
    padding: 0;
    font-size: 1.2rem;
    line-height: 1;
}

.notification-message {
    color: #495057;
    font-size: 0.85rem;
    margin: 0;
    line-height: 1.4;
}

.notification-icon {
    margin-right: 8px;
    font-size: 1rem;
}

.notification.success .notification-icon {
    color: #28a745;
}

.notification.error .notification-icon {
    color: #dc3545;
}

.notification.warning .notification-icon {
    color: #ffc107;
}

.notification.info .notification-icon {
    color: #17a2b8;
}

@keyframes slideIn {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}



@keyframes slideOut {
    from {
        transform: translateX(0);
        opacity: 1;
    }
    to {
        transform: translateX(100%);
        opacity: 0;
    }
}

/* Browser-style Notifications */
.toast-notification {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 10000;
    min-width: 350px;
    max-width: 400px;
    background: white;
    border-radius: 12px;
    box-shadow: 0 8px 32px rgba(0,0,0,0.15);
    border-left: 4px solid #28a745;
    padding: 1rem 1.5rem;
    transform: translateX(450px);
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    opacity: 0;
}

.toast-notification.show {
    transform: translateX(0);
    opacity: 1;
}

.toast-notification.success {
    border-left-color: #28a745;
}

.toast-notification.error {
    border-left-color: #dc3545;
}

.toast-notification.warning {
    border-left-color: #ffc107;
}

.toast-notification.info {
    border-left-color: #17a2b8;
}

.toast-header {
    display: flex;
    align-items: center;
    margin-bottom: 0.5rem;
}

.toast-icon {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 0.75rem;
    font-size: 0.8rem;
    color: white;
}

.toast-icon.success { background: #28a745; }
.toast-icon.error { background: #dc3545; }
.toast-icon.warning { background: #ffc107; }
.toast-icon.info { background: #17a2b8; }

.toast-title {
    font-weight: 600;
    color: #2c3e50;
    font-size: 0.9rem;
    flex: 1;
}

.toast-close {
    background: none;
    border: none;
    color: #6c757d;
    font-size: 1.2rem;
    cursor: pointer;
    padding: 0;
    width: 20px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.toast-close:hover {
    color: #495057;
}

.toast-message {
    color: #495057;
    font-size: 0.85rem;
    line-height: 1.4;
}

/* UE Details Modal */
.modal-header {
    background: linear-gradient(135deg, #2c3e50, #34495e);
    color: white;
    border-radius: 12px 12px 0 0;
}

.modal-content {
    border-radius: 12px;
    border: none;
    box-shadow: 0 10px 40px rgba(0,0,0,0.2);
}

.ue-detail-section {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 1rem;
}

.ue-detail-title {
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 0.75rem;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.ue-detail-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
    gap: 1rem;
}

.ue-detail-item {
    text-align: center;
}

.ue-detail-label {
    font-size: 0.75rem;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 0.25rem;
}

.ue-detail-value {
    font-weight: 600;
    color: #2c3e50;
    font-size: 1rem;
}

.empty-state {
    text-align: center;
    padding: 3rem;
    color: #6c757d;
}

.empty-state i {
    font-size: 4rem;
    margin-bottom: 1rem;
    opacity: 0.5;
}

/* View Toggle Buttons */
.btn-toggle {
    padding: 0.75rem 1.5rem;
    font-weight: 600;
    border-radius: 10px;
    transition: all 0.3s ease;
    border: 2px solid;
    position: relative;
    overflow: hidden;
}

.btn-toggle:first-child {
    border-top-right-radius: 0;
    border-bottom-right-radius: 0;
    border-right: 1px solid;
}

.btn-toggle:last-child {
    border-top-left-radius: 0;
    border-bottom-left-radius: 0;
    border-left: 1px solid;
}

/* Warning Button (UEs Vacantes) */
.btn-outline-warning.btn-toggle {
    background: white;
    border-color: #ffc107;
    color: #ffc107;
}

.btn-outline-warning.btn-toggle:hover {
    background: #ffc107;
    border-color: #ffc107;
    color: #212529;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(255, 193, 7, 0.4);
}

.btn-warning.btn-toggle.active {
    background: linear-gradient(135deg, #ffc107, #ffb300);
    border-color: #ffc107;
    color: #212529;
    box-shadow: 0 4px 15px rgba(255, 193, 7, 0.5);
    transform: translateY(-2px);
}

/* Success Button (UEs Affectées) */
.btn-outline-success.btn-toggle {
    background: white;
    border-color: #28a745;
    color: #28a745;
}

.btn-outline-success.btn-toggle:hover {
    background: #28a745;
    border-color: #28a745;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(40, 167, 69, 0.4);
}

.btn-success.btn-toggle.active {
    background: linear-gradient(135deg, #28a745, #20c997);
    border-color: #28a745;
    color: white;
    box-shadow: 0 4px 15px rgba(40, 167, 69, 0.5);
    transform: translateY(-2px);
}

/* Button Group Styling */
.btn-group .btn-toggle {
    border-radius: 0;
    margin: 0;
    border-left: 1px solid;
    border-right: 1px solid;
}

.btn-group .btn-toggle:first-child {
    border-top-left-radius: 10px;
    border-bottom-left-radius: 10px;
    border-left: 2px solid;
}

.btn-group .btn-toggle:last-child {
    border-top-right-radius: 10px;
    border-bottom-right-radius: 10px;
    border-right: 2px solid;
}

/* Smooth transitions for all states */
.btn-toggle, .btn-toggle:hover, .btn-toggle:focus, .btn-toggle:active {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Focus states */
.btn-toggle:focus {
    outline: none;
    box-shadow: 0 0 0 3px rgba(255, 193, 7, 0.25);
}

.btn-success.btn-toggle:focus {
    box-shadow: 0 0 0 3px rgba(40, 167, 69, 0.25);
}

/* Active state animation */
.btn-toggle.active::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    transition: left 0.5s;
}

.btn-toggle.active:hover::before {
    left: 100%;
}


</style>
@endpush

@section('content')
<!-- Notification Container -->
<div id="notificationContainer" class="notification-container"></div>

<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1">Unités d'Enseignement</h2>
                    <p class="text-muted mb-0">Gestion des UEs du département {{ Auth::user()->departement->nom }} - Année {{ $currentYear }}</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('chef.unites-enseignement.next-year') }}" class="btn btn-outline-warning">
                        <i class="fas fa-calendar-plus me-2"></i>UEs Année Prochaine
                    </a>
                    <button class="btn btn-warning" onclick="showDemandesTab()">
                        <i class="fas fa-clock me-2"></i>Demandes Enseignants
                        @if($demandesCount > 0)
                            <span class="badge bg-danger ms-1">{{ $demandesCount }}</span>
                        @endif
                    </button>
                    <button class="btn btn-info" onclick="showHistoriqueTab()">
                        <i class="fas fa-history me-2"></i>Historique Demandes
                        @if(isset($historiquedemandes) && $historiquedemandes->count() > 0)
                            <span class="badge bg-light text-dark ms-1">{{ $historiquedemandes->count() }}</span>
                        @endif
                    </button>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#affectationModal">
                        <i class="fas fa-plus me-2"></i>Nouvelle Affectation
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card filter-card">
        <div class="card-body">


            <!-- Toggle Buttons for View Mode -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="d-flex justify-content-center">
                        <div class="btn-group shadow-sm" role="group">
                            <button type="button" class="btn btn-toggle {{ $viewMode === 'vacant' ? 'btn-warning active' : 'btn-outline-warning' }}" data-view="vacant" onclick="switchView('vacant')">
                                <i class="fas fa-exclamation-triangle me-2"></i>UEs Vacantes
                            </button>
                            <button type="button" class="btn btn-toggle {{ $viewMode === 'affected' ? 'btn-success active' : 'btn-outline-success' }}" data-view="affected" onclick="switchView('affected')">
                                <i class="fas fa-check-circle me-2"></i>UEs Affectées
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <form method="GET" action="{{ route('chef.unites-enseignement') }}" id="filter-form">
                <input type="hidden" name="view_mode" id="view_mode" value="{{ request('view_mode', 'vacant') }}">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="search-box">
                            <i class="fas fa-search search-icon"></i>
                            <input type="text" class="form-control" name="search"
                                   placeholder="Rechercher par code ou nom..."
                                   value="{{ request('search') }}">
                        </div>
                    </div>

                    <div class="col-md-2">
                        <select class="form-select" name="filiere_id">
                            <option value="">Toutes les filières</option>
                            @foreach($filieres as $filiere)
                                <option value="{{ $filiere->id }}" {{ request('filiere_id') == $filiere->id ? 'selected' : '' }}>
                                    {{ $filiere->nom }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <select class="form-select" name="semestre">
                            <option value="">Tous les semestres</option>
                            @foreach($semestres as $semestre)
                                <option value="{{ $semestre }}" {{ request('semestre') == $semestre ? 'selected' : '' }}>
                                    {{ $semestre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-light flex-fill">
                                <i class="fas fa-filter"></i>
                            </button>
                            <a href="{{ route('chef.unites-enseignement') }}" class="btn btn-outline-light">
                                <i class="fas fa-times"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Active Filters -->
                @if(request()->hasAny(['search', 'filiere_id', 'semestre']))
                    <div class="mt-4 pt-3 border-top">
                        <div class="filter-section-label">Filtres actifs:</div>
                        <div class="d-flex flex-wrap">
                            @if(request('search'))
                                <span class="filter-badge">
                                    <i class="fas fa-search me-1"></i>{{ request('search') }}
                                </span>
                            @endif
                            @if(request('filiere_id'))
                                <span class="filter-badge">
                                    <i class="fas fa-layer-group me-1"></i>{{ $filieres->find(request('filiere_id'))->nom ?? '' }}
                                </span>
                            @endif
                            @if(request('semestre'))
                                <span class="filter-badge">
                                    <i class="fas fa-calendar me-1"></i>{{ request('semestre') }}
                                </span>
                            @endif
                        </div>
                    </div>
                @endif
            </form>
        </div>
    </div>

    <!-- Demandes Management Section (Hidden by default) -->
    <div class="card" id="demandes-section" style="display: none;">
        <div class="card-header bg-warning text-dark">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-clock me-2"></i>Demandes d'Enseignement pour l'Année Prochaine ({{ date('Y') + 1 }}-{{ date('Y') + 2 }})
                </h5>
                <button class="btn btn-sm btn-outline-dark" onclick="hideDemandesTab()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            @if(isset($demandes) && $demandes->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Enseignant</th>
                                <th>UE Demandée</th>
                                <th>Type Séance</th>
                                <th>Date Demande</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($demandes as $demande)
                                <tr id="demande-{{ $demande->id }}">
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                                {{ strtoupper(substr($demande->user->name, 0, 2)) }}
                                            </div>
                                            <div>
                                                <div class="fw-bold">{{ $demande->user->name }}</div>
                                                <small class="text-muted">{{ $demande->user->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <div class="fw-bold">{{ $demande->uniteEnseignement->code }}</div>
                                            <small class="text-muted">{{ $demande->uniteEnseignement->nom }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $demande->type_seance }}</span>
                                    </td>
                                    <td>
                                        <small>{{ $demande->created_at->format('d/m/Y H:i') }}</small>
                                    </td>
                                    <td>
                                        @if($demande->validee === 'en_attente')
                                            <span class="badge bg-warning">En attente</span>
                                        @elseif($demande->validee === 'valide')
                                            <span class="badge bg-success">Approuvée</span>
                                        @else
                                            <span class="badge bg-danger">Rejetée</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($demande->validee === 'en_attente')
                                            <div class="btn-group btn-group-sm">
                                                <button class="btn btn-success" onclick="approuverDemande({{ $demande->id }})">
                                                    <i class="fas fa-check"></i> Approuver
                                                </button>
                                                <button class="btn btn-danger" onclick="rejeterDemande({{ $demande->id }})">
                                                    <i class="fas fa-times"></i> Rejeter
                                                </button>
                                            </div>
                                        @else
                                            <span class="text-muted">Traitée</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <h5>Aucune demande en attente</h5>
                    <p class="text-muted">Toutes les demandes d'enseignement ont été traitées.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Historique Demandes Section (Hidden by default) -->
    <div class="card" id="historique-section" style="display: none;">
        <div class="card-header bg-info text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-history me-2"></i>Historique des Demandes ({{ date('Y') + 1 }}-{{ date('Y') + 2 }})
                </h5>
                <button class="btn btn-sm btn-outline-light" onclick="hideHistoriqueTab()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            @if(isset($historiquedemandes) && $historiquedemandes->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Enseignant</th>
                                <th>UE Demandée</th>
                                <th>Type Séance</th>
                                <th>Date Demande</th>
                                <th>Date Traitement</th>
                                <th>Statut</th>
                                <th>Commentaire</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($historiquedemandes as $demande)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                                {{ strtoupper(substr($demande->user->name, 0, 2)) }}
                                            </div>
                                            <div>
                                                <div class="fw-bold">{{ $demande->user->name }}</div>
                                                <small class="text-muted">{{ $demande->user->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <div class="fw-bold">{{ $demande->uniteEnseignement->code }}</div>
                                            <small class="text-muted">{{ $demande->uniteEnseignement->nom }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $demande->type_seance }}</span>
                                    </td>
                                    <td>
                                        <small>{{ $demande->created_at->format('d/m/Y H:i') }}</small>
                                    </td>
                                    <td>
                                        <small>{{ $demande->date_validation ? $demande->date_validation->format('d/m/Y H:i') : '-' }}</small>
                                    </td>
                                    <td>
                                        @if($demande->validee === 'valide')
                                            <span class="badge bg-success">
                                                <i class="fas fa-check me-1"></i>Approuvée
                                            </span>
                                        @elseif($demande->validee === 'rejete')
                                            <span class="badge bg-danger">
                                                <i class="fas fa-times me-1"></i>Rejetée
                                            </span>
                                        @elseif($demande->validee === 'annule')
                                            <span class="badge bg-warning text-dark">
                                                <i class="fas fa-ban me-1"></i>Annulée par l'enseignant
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">{{ $demande->validee }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $demande->commentaire ?? '-' }}</small>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-archive fa-3x text-muted mb-3"></i>
                    <h5>Aucun historique</h5>
                    <p class="text-muted">Aucune demande n'a encore été traitée.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Results Summary -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted">
                        {{ $unites->total() }} UE(s) trouvée(s)
                        @if($unites->hasPages())
                            - Page {{ $unites->currentPage() }} sur {{ $unites->lastPage() }}
                        @endif
                    </span>
                </div>

            </div>
        </div>
    </div>

    <!-- UEs Grid -->
    @if($unites->count() > 0)
        <div class="row" id="ues-container">
            @foreach($unites as $ue)
                <div class="col-lg-6 col-xl-4">
                    <div class="ue-card">
                        <div class="ue-header">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1 me-3">
                                    <div class="ue-code">{{ $ue->code }}</div>
                                    <div class="ue-title">{{ Str::limit($ue->nom, 40) }}</div>
                                </div>
                                <div class="flex-shrink-0">
                                    @if($ue->est_vacant)
                                        <span class="status-badge status-vacant" title="Vacant">
                                            <i class="fas fa-exclamation-triangle me-1"></i>Vacant
                                        </span>
                                    @else
                                        <span class="status-badge status-assigned" title="Affecté">
                                            <i class="fas fa-check-circle me-1"></i>Affecté
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="ue-body">
                            <!-- UE Info -->
                            <div class="ue-info">
                                <div class="info-item">
                                    <div class="info-label">Filière</div>
                                    <div class="info-value">{{ $ue->filiere->nom ?? 'N/A' }}</div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">Semestre</div>
                                    <div class="info-value">{{ $ue->semestre }}</div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">CM</div>
                                    <div class="info-value">{{ $ue->heures_cm }}h</div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">TD</div>
                                    <div class="info-value">{{ $ue->heures_td }}h</div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">TP</div>
                                    <div class="info-value">{{ $ue->heures_tp }}h</div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">Total</div>
                                    <div class="info-value">{{ $ue->total_hours }}h</div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="action-buttons mt-3">
                                <!-- For Affected UEs View -->
                                <div class="affected-view-actions" style="display: {{ request('view_mode', 'affected') == 'affected' ? 'block' : 'none' }};">
                                    <a href="{{ route('chef.ue.details', $ue->id) }}" class="btn btn-info btn-action">
                                        <i class="fas fa-eye me-1"></i>Détails
                                    </a>
                                    <a href="{{ route('chef.unites-enseignement.edit', $ue->id) }}" class="btn btn-warning btn-action">
                                        <i class="fas fa-edit me-1"></i>Modifier
                                    </a>
                                </div>

                                <!-- For Vacant UEs View -->
                                <div class="vacant-view-actions" style="display: {{ request('view_mode', 'affected') == 'vacant' ? 'block' : 'none' }};">
                                    <a href="{{ route('chef.ue.affecter', $ue->id) }}" class="btn btn-primary btn-action">
                                        <i class="fas fa-user-plus me-1"></i>Affecter
                                    </a>

                                    <a href="{{ route('chef.ue.details', $ue->id) }}" class="btn btn-info btn-action">
                                        <i class="fas fa-eye me-1"></i>Détails
                                    </a>

                                    <a href="{{ route('chef.unites-enseignement.edit', $ue->id) }}" class="btn btn-warning btn-action">
                                        <i class="fas fa-edit me-1"></i>Modifier
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="row mt-4">
            <div class="col-12">
                {{ $unites->appends(request()->query())->links() }}
            </div>
        </div>
    @else
        <div class="empty-state">
            <i class="fas fa-search"></i>
            <h4>Aucune UE trouvée</h4>
            <p>Aucune unité d'enseignement ne correspond à vos critères de recherche.</p>
            <a href="{{ route('chef.unites-enseignement') }}" class="btn btn-primary">
                <i class="fas fa-refresh me-2"></i>Réinitialiser les filtres
            </a>
        </div>
    @endif
</div>










<!-- Toast Notification Container -->
<div id="toastContainer"></div>
@endsection

@push('scripts')
<script>
// All modal functionality removed - using dedicated pages now

// Demands Management Functions
function showDemandesTab() {
    document.getElementById('demandes-section').style.display = 'block';
    document.getElementById('demandes-section').scrollIntoView({ behavior: 'smooth' });
}

function hideDemandesTab() {
    document.getElementById('demandes-section').style.display = 'none';
}

// Historique Management Functions
function showHistoriqueTab() {
    document.getElementById('historique-section').style.display = 'block';
    document.getElementById('historique-section').scrollIntoView({ behavior: 'smooth' });
}

function hideHistoriqueTab() {
    document.getElementById('historique-section').style.display = 'none';
}

function approuverDemande(demandeId) {
    if (confirm('Êtes-vous sûr de vouloir approuver cette demande?')) {
        fetch(`/chef/demandes/${demandeId}/approve`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Remove the row from active demands (it goes to historique)
                const row = document.getElementById(`demande-${demandeId}`);
                row.remove();

                // Show success message
                showAlert('success', 'Demande approuvée avec succès! Elle apparaît maintenant dans l\'historique.');

                // Update badge count
                updateDemandesCount();

                // Update historique badge if visible
                updateHistoriqueBadge();
            } else {
                showAlert('error', data.message || 'Erreur lors de l\'approbation');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('error', 'Erreur lors de l\'approbation');
        });
    }
}

function rejeterDemande(demandeId) {
    if (confirm('Êtes-vous sûr de vouloir rejeter cette demande?')) {
        fetch(`/chef/demandes/${demandeId}/reject`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Remove the row from active demands (it goes to historique)
                const row = document.getElementById(`demande-${demandeId}`);
                row.remove();

                // Show success message
                showAlert('success', 'Demande rejetée avec succès! Elle apparaît maintenant dans l\'historique.');

                // Update badge count
                updateDemandesCount();

                // Update historique badge if visible
                updateHistoriqueBadge();
            } else {
                showAlert('error', data.message || 'Erreur lors du rejet');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('error', 'Erreur lors du rejet');
        });
    }
}

function updateDemandesCount() {
    // Count remaining pending demands in the table
    const pendingRows = document.querySelectorAll('#demandes-section tbody tr').length;
    const badge = document.querySelector('.btn-warning .badge');

    if (pendingRows > 0) {
        if (badge) {
            badge.textContent = pendingRows;
        }
    } else {
        if (badge) {
            badge.remove();
        }
    }
}

function updateHistoriqueBadge() {
    // Increment historique badge count
    const historiqueBadge = document.querySelector('.btn-info .badge');
    if (historiqueBadge) {
        const currentCount = parseInt(historiqueBadge.textContent) || 0;
        historiqueBadge.textContent = currentCount + 1;
    }
}

// Browser-style notification system
function showNotification(type, title, message) {
    const container = document.getElementById('notificationContainer');

    const icons = {
        success: 'fas fa-check-circle',
        error: 'fas fa-exclamation-circle',
        warning: 'fas fa-exclamation-triangle',
        info: 'fas fa-info-circle'
    };

    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.innerHTML = `
        <div class="notification-header">
            <div class="d-flex align-items-center">
                <i class="${icons[type]} notification-icon"></i>
                <h6 class="notification-title">${title}</h6>
            </div>
            <button class="notification-close" onclick="closeNotification(this)">×</button>
        </div>
        <p class="notification-message">${message}</p>
    `;

    container.appendChild(notification);

    // Auto-remove after 5 seconds
    setTimeout(() => {
        if (notification.parentNode) {
            closeNotification(notification.querySelector('.notification-close'));
        }
    }, 5000);
}

function closeNotification(button) {
    const notification = button.closest('.notification');
    notification.style.animation = 'slideOut 0.3s ease-out';
    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, 300);
}

// Legacy function for compatibility
function showAlert(type, message) {
    const titles = {
        success: 'Succès',
        error: 'Erreur',
        warning: 'Attention',
        info: 'Information'
    };
    showNotification(type, titles[type] || 'Notification', message);
}



// Switch between affected and vacant views
function switchView(viewMode) {
    // Update toggle buttons
    document.querySelectorAll('.btn-toggle').forEach(btn => {
        // Remove all button classes
        btn.classList.remove('btn-warning', 'btn-success', 'btn-outline-warning', 'btn-outline-success', 'active');

        if (btn.dataset.view === viewMode) {
            // Add active classes
            btn.classList.add('active');
            if (viewMode === 'vacant') {
                btn.classList.add('btn-warning');
            } else if (viewMode === 'affected') {
                btn.classList.add('btn-success');
            }
        } else {
            // Add outline classes for inactive buttons
            if (btn.dataset.view === 'vacant') {
                btn.classList.add('btn-outline-warning');
            } else if (btn.dataset.view === 'affected') {
                btn.classList.add('btn-outline-success');
            }
        }
    });

    // Update hidden input
    document.getElementById('view_mode').value = viewMode;

    // Show/hide appropriate action buttons
    document.querySelectorAll('.affected-view-actions').forEach(el => {
        el.style.display = viewMode === 'affected' ? 'block' : 'none';
    });

    document.querySelectorAll('.vacant-view-actions').forEach(el => {
        el.style.display = viewMode === 'vacant' ? 'block' : 'none';
    });

    // Submit form to reload with new view mode
    document.getElementById('filter-form').submit();
}

// Page initialization
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const currentView = urlParams.get('view_mode') || 'vacant';

    // Update hidden input
    document.getElementById('view_mode').value = currentView;

    // Set correct toggle button state
    document.querySelectorAll('.btn-toggle').forEach(btn => {
        // Remove all button classes
        btn.classList.remove('btn-warning', 'btn-success', 'btn-outline-warning', 'btn-outline-success', 'active');

        if (btn.dataset.view === currentView) {
            // Add active classes
            btn.classList.add('active');
            if (currentView === 'vacant') {
                btn.classList.add('btn-warning');
            } else if (currentView === 'affected') {
                btn.classList.add('btn-success');
            }
        } else {
            // Add outline classes for inactive buttons
            if (btn.dataset.view === 'vacant') {
                btn.classList.add('btn-outline-warning');
            } else if (btn.dataset.view === 'affected') {
                btn.classList.add('btn-outline-success');
            }
        }
    });

    // Show/hide appropriate action buttons
    document.querySelectorAll('.affected-view-actions').forEach(el => {
        el.style.display = currentView === 'affected' ? 'block' : 'none';
    });

    document.querySelectorAll('.vacant-view-actions').forEach(el => {
        el.style.display = currentView === 'vacant' ? 'block' : 'none';
    });
});
</script>
@endpush
