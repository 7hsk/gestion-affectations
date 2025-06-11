@extends('layouts.coordonnateur')

@section('title', 'Gestion des Emplois du Temps')

@push('styles')
<style>
.schedule-grid {
    background: rgba(255, 255, 255, 0.95);
    border-radius: 15px;
    padding: 1.5rem;
    margin-bottom: 2rem;
    box-shadow: 0 4px 12px rgba(88, 28, 135, 0.1);
    overflow-x: auto;
}

.schedule-table {
    min-width: 800px;
    border-collapse: separate;
    border-spacing: 2px;
}

.schedule-table th {
    background: linear-gradient(135deg, #7c3aed, #8b5cf6);
    color: white;
    padding: 1rem;
    text-align: center;
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.9rem;
}

.schedule-table td {
    background: rgba(255, 255, 255, 0.8);
    padding: 0.5rem;
    border-radius: 6px;
    vertical-align: top;
    min-height: 80px;
    position: relative;
}

.schedule-slot {
    background: linear-gradient(135deg, rgba(124, 58, 237, 0.1), rgba(139, 92, 246, 0.1));
    border: 2px solid rgba(124, 58, 237, 0.2);
    border-radius: 8px;
    padding: 0.75rem;
    margin-bottom: 0.5rem;
    cursor: pointer;
}

.schedule-slot:hover {
    background: linear-gradient(135deg, rgba(124, 58, 237, 0.2), rgba(139, 92, 246, 0.2));
    border-color: #7c3aed;
}

.schedule-slot.cm {
    background: linear-gradient(135deg, rgba(220, 38, 38, 0.1), rgba(239, 68, 68, 0.1));
    border-color: rgba(220, 38, 38, 0.3);
}

.schedule-slot.td {
    background: linear-gradient(135deg, rgba(5, 150, 105, 0.1), rgba(16, 185, 129, 0.1));
    border-color: rgba(5, 150, 105, 0.3);
}

.schedule-slot.tp {
    background: linear-gradient(135deg, rgba(8, 145, 178, 0.1), rgba(6, 182, 212, 0.1));
    border-color: rgba(8, 145, 178, 0.3);
}

.slot-code {
    font-weight: 800;
    font-size: 0.9rem;
    color: #374151;
}

.slot-title {
    font-size: 0.8rem;
    color: #6b7280;
    margin: 0.25rem 0;
}

.slot-teacher {
    font-size: 0.75rem;
    color: #7c3aed;
    font-weight: 600;
}

.slot-type {
    position: absolute;
    top: 0.25rem;
    right: 0.25rem;
    background: #7c3aed;
    color: white;
    padding: 0.125rem 0.375rem;
    border-radius: 4px;
    font-size: 0.7rem;
    font-weight: 600;
}

.time-slot {
    background: linear-gradient(135deg, #374151, #4b5563);
    color: white;
    font-weight: 600;
    text-align: center;
    font-size: 0.85rem;
}

.upload-section {
    background: linear-gradient(135deg, #059669 0%, #10b981 100%);
    color: white;
    border-radius: 15px;
    padding: 2rem;
    margin-bottom: 2rem;
    text-align: center;
}

.upload-area {
    border: 2px dashed rgba(255, 255, 255, 0.5);
    border-radius: 10px;
    padding: 2rem;
    margin-top: 1rem;
    transition: all 0.3s ease;
}

.upload-area:hover {
    border-color: white;
    background: rgba(255, 255, 255, 0.1);
}

.filiere-tabs {
    background: rgba(255, 255, 255, 0.95);
    border-radius: 15px;
    padding: 1rem;
    margin-bottom: 2rem;
    box-shadow: 0 4px 12px rgba(88, 28, 135, 0.1);
}

.filiere-tab {
    background: transparent;
    border: 2px solid rgba(124, 58, 237, 0.2);
    color: #7c3aed;
    border-radius: 10px;
    padding: 0.75rem 1.5rem;
    margin: 0.25rem;
    transition: all 0.3s ease;
    font-weight: 600;
}

.filiere-tab.active {
    background: linear-gradient(135deg, #7c3aed, #8b5cf6);
    color: white;
    border-color: #7c3aed;
    box-shadow: 0 4px 15px rgba(124, 58, 237, 0.3);
}

.filiere-tab:hover {
    background: rgba(124, 58, 237, 0.1);
    border-color: #7c3aed;
}

.legend {
    display: flex;
    justify-content: center;
    gap: 2rem;
    margin-bottom: 1rem;
}

.legend-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.legend-color {
    width: 20px;
    height: 20px;
    border-radius: 4px;
    border: 2px solid rgba(0, 0, 0, 0.1);
}

.legend-color.cm {
    background: linear-gradient(135deg, rgba(220, 38, 38, 0.3), rgba(239, 68, 68, 0.3));
}

.legend-color.td {
    background: linear-gradient(135deg, rgba(5, 150, 105, 0.3), rgba(16, 185, 129, 0.3));
}

.legend-color.tp {
    background: linear-gradient(135deg, rgba(8, 145, 178, 0.3), rgba(6, 182, 212, 0.3));
}

.stats-bar {
    background: rgba(255, 255, 255, 0.95);
    border-radius: 15px;
    padding: 1rem;
    margin-bottom: 2rem;
    box-shadow: 0 4px 12px rgba(88, 28, 135, 0.1);
}

.stat-item {
    text-align: center;
    padding: 0.5rem;
}

.stat-number {
    font-size: 1.5rem;
    font-weight: 800;
    background: linear-gradient(135deg, #7c3aed, #8b5cf6);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.stat-label {
    font-size: 0.8rem;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-weight: 600;
}

/* UE List Styles */
.ue-list-container {
    background: rgba(255, 255, 255, 0.95);
    border-radius: 20px;
    box-shadow: 0 4px 12px rgba(6, 78, 59, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.ue-list {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    max-height: 200px;
    overflow-y: auto;
    padding: 10px;
}

.ue-item {
    background: linear-gradient(135deg, #059669 0%, #10b981 100%);
    color: white;
    padding: 12px 16px;
    border-radius: 12px;
    cursor: grab;
    box-shadow: 0 4px 15px rgba(5, 150, 105, 0.3);
    min-width: 200px;
    user-select: none;
}

.ue-item:hover {
    box-shadow: 0 6px 20px rgba(5, 150, 105, 0.4);
}

.ue-item:active {
    cursor: grabbing;
    opacity: 0.8;
}

.ue-item .ue-name {
    display: block;
    font-size: 0.9em;
    opacity: 0.9;
    margin-top: 4px;
}

.ue-item .ue-details {
    margin-top: 8px;
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.ue-item .badge {
    font-size: 0.75em;
    padding: 2px 6px;
}

/* Drop Zone Styles */
.drop-zone {
    position: relative;
    transition: all 0.3s ease;
}

.drop-zone.drag-over {
    background-color: rgba(16, 185, 129, 0.1) !important;
    border: 2px dashed #10b981 !important;
}

.drop-placeholder {
    opacity: 0.6;
    transition: all 0.3s ease;
}

.drop-zone:hover .drop-placeholder {
    opacity: 1;
    background-color: rgba(16, 185, 129, 0.05);
}

/* Schedule Slot Styles */
.schedule-slot {
    position: relative;
    padding: 8px;
    border-radius: 8px;
    margin: 2px;
    min-height: 80px;
    transition: all 0.3s ease;
}

.schedule-slot .remove-schedule {
    position: absolute;
    top: 2px;
    right: 2px;
    width: 20px;
    height: 20px;
    padding: 0;
    border-radius: 50%;
    display: none;
    font-size: 10px;
    line-height: 1;
}

.schedule-slot:hover .remove-schedule {
    display: flex;
    align-items: center;
    justify-content: center;
}

.schedule-slot.cm {
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    color: white;
}

.schedule-slot.td {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
}

.schedule-slot.tp {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    color: white;
}

.slot-type {
    font-weight: bold;
    font-size: 0.8em;
    opacity: 0.9;
}

.slot-code {
    font-weight: bold;
    font-size: 0.9em;
    margin: 2px 0;
}

.slot-title {
    font-size: 0.8em;
    opacity: 0.9;
    line-height: 1.2;
}

.slot-teacher {
    font-size: 0.75em;
    opacity: 0.8;
    margin-top: 4px;
}

/* Sidebar Toggle Button - Only for Emploi du Temps */
.sidebar-minimize-btn {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 1001;
    background: linear-gradient(135deg, #7c3aed, #8b5cf6);
    color: white;
    border: none;
    border-radius: 12px;
    padding: 12px 20px;
    display: flex;
    align-items: center;
    gap: 8px;
    box-shadow: 0 4px 15px rgba(124, 58, 237, 0.3);
    cursor: pointer;
    font-size: 0.9rem;
    font-weight: 600;
    transition: all 0.3s ease;
}

.sidebar-minimize-btn:hover {
    background: linear-gradient(135deg, #6d28d9, #7c3aed);
    box-shadow: 0 6px 20px rgba(124, 58, 237, 0.4);
    transform: translateY(-2px);
}

.sidebar-minimize-btn i {
    font-size: 1rem;
}

.sidebar-minimize-btn.minimized {
    background: linear-gradient(135deg, #059669, #10b981);
}

/* New UE Card Styles */
.ue-card-container {
    background: white;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    padding: 1rem;
    margin-bottom: 0.75rem;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.ue-header {
    margin-bottom: 0.75rem;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid #e2e8f0;
}

.ue-header strong {
    color: #059669;
    font-size: 1rem;
    display: block;
}

.ue-name {
    color: #374151;
    font-size: 0.85rem;
    margin-top: 0.25rem;
    display: block;
}

.ue-type-options {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.ue-type-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem 0.75rem;
    border-radius: 8px;
    cursor: grab;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.ue-type-item:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.ue-type-item:active {
    cursor: grabbing;
    transform: scale(0.98);
}

/* Type Colors - Red CM, Green TD, Blue TP */
.ue-type-item.cm {
    background: linear-gradient(135deg, #fee2e2, #fecaca);
    border-color: #ef4444;
    color: #dc2626;
}

.ue-type-item.cm:hover {
    background: linear-gradient(135deg, #fecaca, #fca5a5);
    border-color: #dc2626;
}

.ue-type-item.td {
    background: linear-gradient(135deg, #dcfce7, #bbf7d0);
    border-color: #22c55e;
    color: #16a34a;
}

.ue-type-item.td:hover {
    background: linear-gradient(135deg, #bbf7d0, #86efac);
    border-color: #16a34a;
}

.ue-type-item.tp {
    background: linear-gradient(135deg, #dbeafe, #bfdbfe);
    border-color: #3b82f6;
    color: #2563eb;
}

.ue-type-item.tp:hover {
    background: linear-gradient(135deg, #bfdbfe, #93c5fd);
    border-color: #2563eb;
}

.type-label {
    font-weight: 600;
    font-size: 0.9rem;
}

.type-hours {
    font-size: 0.8rem;
    opacity: 0.8;
}

/* UE Container - Card Carousel with Navigation */
.ue-list-container {
    height: 480px; /* Fixed height */
    border-radius: 15px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    background: white;
    position: relative;
    overflow: hidden;
}

.ue-carousel-header {
    background: linear-gradient(135deg, #059669, #10b981);
    color: white;
    padding: 1rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-radius: 15px 15px 0 0;
}

.ue-carousel-title {
    font-weight: 600;
    font-size: 1rem;
    margin: 0;
}

.ue-carousel-counter {
    background: rgba(255, 255, 255, 0.2);
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 500;
}

.ue-carousel-content {
    height: calc(100% - 120px); /* Minus header and navigation */
    padding: 1rem;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.ue-card-container {
    height: 100%; /* Fill available space */
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    padding: 1.5rem;
    background: white;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
    display: none; /* Hidden by default */
}

.ue-card-container.active {
    display: block; /* Show active card */
    animation: fadeInSlide 0.3s ease-in-out;
}

@keyframes fadeInSlide {
    from {
        opacity: 0;
        transform: translateX(20px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

.ue-carousel-navigation {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: #f8fafc;
    padding: 0.75rem 1rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-radius: 0 0 15px 15px;
    border-top: 1px solid #e2e8f0;
}

.carousel-btn {
    background: linear-gradient(135deg, #059669, #10b981);
    color: white;
    border: none;
    border-radius: 8px;
    padding: 0.5rem 1rem;
    font-size: 0.85rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.carousel-btn:hover {
    background: linear-gradient(135deg, #047857, #059669);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(5, 150, 105, 0.3);
}

.carousel-btn:disabled {
    background: #9ca3af;
    cursor: not-allowed;
    transform: none;
    box-shadow: none;
}

.carousel-indicators {
    display: flex;
    gap: 0.5rem;
    align-items: center;
}

.carousel-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #d1d5db;
    transition: all 0.3s ease;
    cursor: pointer;
}

.carousel-dot.active {
    background: #059669;
    transform: scale(1.2);
}

/* Flexible Table - Always Complete Visibility */
.schedule-grid {
    min-height: 500px; /* Minimum height for table */
    height: auto; /* Flexible height */
    overflow: visible; /* Allow content to be fully visible */
    display: block; /* Change from flex to block */
}

.schedule-table {
    width: 100%;
    min-width: 100%; /* Always full width */
    table-layout: fixed; /* Fixed layout for consistent columns */
    font-size: clamp(0.7rem, 1.5vw, 0.9rem); /* Responsive font size */
    border-collapse: collapse;
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.schedule-table th,
.schedule-table td {
    padding: clamp(0.25rem, 1vw, 0.75rem); /* Responsive padding */
    vertical-align: top;
    border: 1px solid #e2e8f0;
    min-height: 100px; /* Minimum height for content */
    height: auto; /* Flexible height */
    overflow: visible; /* Show all content */
    position: relative;
    word-wrap: break-word;
}

.schedule-table th:first-child,
.schedule-table td:first-child {
    width: 12%; /* Responsive width for time column */
    min-width: 80px; /* Minimum width */
}

.time-slot {
    background: linear-gradient(135deg, #374151, #4b5563);
    color: #000000 !important; /* Black text for hours */
    font-weight: 600;
    text-align: center;
    font-size: 0.85rem;
    writing-mode: horizontal-tb;
}

/* Schedule Slot Colors - Match UE Type Colors */
.schedule-slot {
    padding: 0.5rem;
    border-radius: 8px;
    margin: 2px;
    position: relative;
    cursor: pointer;
    transition: all 0.3s ease;
    height: calc(100% - 4px);
    overflow: hidden;
}

.schedule-slot.cm {
    background: linear-gradient(135deg, #fee2e2, #fecaca);
    border: 2px solid #ef4444;
    color: #dc2626;
}

.schedule-slot.td {
    background: linear-gradient(135deg, #dcfce7, #bbf7d0);
    border: 2px solid #22c55e;
    color: #16a34a;
}

.schedule-slot.tp {
    background: linear-gradient(135deg, #dbeafe, #bfdbfe);
    border: 2px solid #3b82f6;
    color: #2563eb;
}

.schedule-slot:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

/* Group Selection Modal */
.group-selection-modal {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: white;
    border-radius: 15px;
    padding: 2rem;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    z-index: 2000;
    min-width: 300px;
}

.group-selection-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 1999;
}

.group-option {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem;
    margin: 0.5rem 0;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.group-option:hover {
    border-color: #059669;
    background: #f0fdf4;
}

.group-option.selected {
    border-color: #059669;
    background: #f0fdf4;
    color: #059669;
}

/* Responsive Design for All Zoom Levels */
@media (min-width: 1400px) {
    .schedule-table {
        font-size: 0.95rem;
    }
    .schedule-table th,
    .schedule-table td {
        padding: 0.75rem;
        min-height: 120px;
    }
    .ue-list-container {
        max-height: 75vh;
    }
}

@media (max-width: 1200px) {
    .schedule-table {
        font-size: 0.8rem;
    }
    .schedule-table th,
    .schedule-table td {
        padding: 0.5rem;
        min-height: 90px;
    }
    .ue-list-container {
        max-height: 65vh;
    }
}

@media (max-width: 992px) {
    .schedule-table {
        font-size: 0.75rem;
    }
    .schedule-table th,
    .schedule-table td {
        padding: 0.4rem;
        min-height: 80px;
    }
    .ue-list-container {
        max-height: 60vh;
        min-height: 350px;
    }
    .schedule-table th:first-child,
    .schedule-table td:first-child {
        width: 15%;
        min-width: 70px;
    }
}

@media (max-width: 768px) {
    .schedule-table {
        font-size: 0.7rem;
    }
    .schedule-table th,
    .schedule-table td {
        padding: 0.3rem;
        min-height: 70px;
    }
    .ue-list-container {
        max-height: 50vh;
        min-height: 300px;
    }
    .schedule-table th:first-child,
    .schedule-table td:first-child {
        width: 18%;
        min-width: 60px;
    }
}

/* Zoom Level Adjustments */
@media (min-resolution: 150dpi) {
    .schedule-table {
        font-size: clamp(0.8rem, 2vw, 1rem);
    }
    .schedule-table th,
    .schedule-table td {
        padding: clamp(0.4rem, 1.2vw, 0.8rem);
    }
}

/* Ensure content is always visible */
.schedule-slot {
    max-width: 100%;
    overflow: visible;
    word-wrap: break-word;
    hyphens: auto;
}

/* Compact Schedule Slot Styles */
.slot-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.25rem;
}

.slot-type {
    font-weight: 600;
    font-size: 0.75rem;
    padding: 0.1rem 0.3rem;
    border-radius: 4px;
    background: rgba(255, 255, 255, 0.2);
}

.slot-code {
    font-weight: 600;
    font-size: 0.8rem;
    margin: 0.1rem 0;
    text-align: center;
}

.slot-abbreviation {
    font-size: 0.7rem;
    text-align: center;
    opacity: 0.9;
    font-weight: 500;
}

.remove-schedule {
    position: absolute;
    top: 2px;
    right: 2px;
    width: 18px;
    height: 18px;
    padding: 0;
    border-radius: 50%;
    font-size: 0.6rem;
    line-height: 1;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* UE Cards Responsive */
@media (max-width: 768px) {
    .ue-card-container {
        min-height: 80px;
        padding: 0.75rem;
    }
    .ue-header strong {
        font-size: 0.9rem;
    }
    .ue-name {
        font-size: 0.8rem;
    }
    .type-label {
        font-size: 0.8rem;
    }
    .type-hours {
        font-size: 0.7rem;
    }
}
</style>
@endpush

@section('content')

<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Gestion des Emplois du Temps</h2>
            <p class="text-muted">Charger et visualiser les emplois du temps par filière</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-primary" onclick="exportSchedule()">
                <i class="fas fa-download me-2"></i>Exporter
            </button>
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#uploadModal">
                <i class="fas fa-upload me-2"></i>Charger EDT
            </button>
        </div>
    </div>

    <!-- Upload Section -->
    <div class="upload-section">
        <h4><i class="fas fa-cloud-upload-alt me-2"></i>Charger un Emploi du Temps</h4>
        <p class="mb-0">Importez les emplois du temps par semestre et affectez-les automatiquement aux enseignants</p>
        <div class="upload-area" onclick="document.getElementById('scheduleFile').click()">
            <i class="fas fa-file-excel fa-3x mb-3"></i>
            <p class="mb-0">Cliquez pour sélectionner un fichier Excel</p>
            <small>Formats supportés: .xlsx, .xls</small>
            <input type="file" id="scheduleFile" accept=".xlsx,.xls" style="display: none;" onchange="handleFileUpload(this)">
        </div>
    </div>

    <!-- Year Selection Buttons -->
    <div class="year-selection mb-4">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5 class="card-title mb-3">
                            <i class="fas fa-calendar-alt me-2"></i>Sélectionner l'Année de la Filière GI
                        </h5>
                        <div class="btn-group" role="group" id="filiereButtons">
                            @foreach($filieres as $filiere)
                                <button type="button"
                                        class="btn filiere-btn {{ isset($selectedFiliere) && $selectedFiliere->id == $filiere->id ? 'btn-primary' : 'btn-outline-primary' }}"
                                        data-filiere="{{ $filiere->nom }}"
                                        data-filiere-id="{{ $filiere->id }}"
                                        onclick="selectFiliere('{{ $filiere->nom }}', {{ $filiere->id }})">
                                    <i class="fas fa-calendar me-2"></i>{{ $filiere->nom }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h5 class="card-title mb-3">
                            <i class="fas fa-clock me-2"></i>Sélectionner le Semestre
                        </h5>
                        <div class="btn-group" role="group" id="semesterButtons">
                            @if(isset($selectedFiliere))
                                @php
                                    $filiereNumber = substr($selectedFiliere->nom, -1);
                                    $semesters = [];
                                    if ($filiereNumber == '1') {
                                        $semesters = ['S1', 'S2'];
                                    } elseif ($filiereNumber == '2') {
                                        $semesters = ['S3', 'S4'];
                                    } elseif ($filiereNumber == '3') {
                                        $semesters = ['S5'];
                                    }
                                @endphp
                                @foreach($semesters as $semester)
                                    <button type="button"
                                            class="btn btn-outline-success semester-btn"
                                            data-semester="{{ $semester }}"
                                            onclick="selectSemester('{{ $semester }}')">
                                        {{ $semester }}
                                    </button>
                                @endforeach
                            @else
                                <p class="text-muted mb-0">Sélectionnez d'abord une année</p>
                            @endif
                        </div>
                    </div>
                </div>
                @if(isset($selectedFiliere))
                    <div class="mt-3">
                        <span class="badge bg-success">
                            <i class="fas fa-check me-1"></i>Actuellement: {{ $selectedFiliere->nom }}
                        </span>
                        <span class="badge bg-info ms-2">
                            <i class="fas fa-clock me-1"></i>{{ $schedules->count() }} Créneaux
                        </span>
                        <span class="badge bg-warning ms-2" id="selectedSemesterBadge" style="display: none;">
                            <i class="fas fa-calendar-week me-1"></i>Semestre: <span id="selectedSemesterText">-</span>
                        </span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Statistics -->
    <div class="stats-bar">
        <div class="row">
            <div class="col-md-3">
                <div class="stat-item">
                    <div class="stat-number">{{ $schedules->count() }}</div>
                    <div class="stat-label">Total Créneaux</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-item">
                    <div class="stat-number">{{ $schedules->where('type_seance', 'CM')->count() }}</div>
                    <div class="stat-label">Cours CM</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-item">
                    <div class="stat-number">{{ $schedules->where('type_seance', 'TD')->count() }}</div>
                    <div class="stat-label">Séances TD</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-item">
                    <div class="stat-number">{{ $schedules->where('type_seance', 'TP')->count() }}</div>
                    <div class="stat-label">Séances TP</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Legend -->
    <div class="legend">
        <div class="legend-item">
            <div class="legend-color cm"></div>
            <span>Cours Magistral (CM)</span>
        </div>
        <div class="legend-item">
            <div class="legend-color td"></div>
            <span>Travaux Dirigés (TD)</span>
        </div>
        <div class="legend-item">
            <div class="legend-color tp"></div>
            <span>Travaux Pratiques (TP)</span>
        </div>
    </div>

    <!-- Main Layout: UE List + Schedule Grid -->
    <div class="row">
        <!-- UE List Panel (Left Side) - Carousel Format -->
        <div class="col-md-4">
            <div class="ue-list-container mb-4">
                <!-- Carousel Header -->
                <div class="ue-carousel-header">
                    <h5 class="ue-carousel-title">
                        <i class="fas fa-graduation-cap me-2"></i>Unités d'Enseignement
                    </h5>
                    <div class="ue-carousel-counter" id="ue-counter">
                        @if(isset($selectedFiliere) && isset($availableUEs) && $availableUEs->count() > 0)
                            <span class="text-success">
                                <i class="fas fa-check-circle me-1"></i>
                                1 / {{ $availableUEs->count() }}
                            </span>
                        @elseif(isset($selectedFiliere))
                            <span class="text-warning">
                                <i class="fas fa-exclamation-triangle me-1"></i>
                                Aucune UE
                            </span>
                        @else
                            <span class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Sélectionnez une filière
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Carousel Content -->
                <div class="ue-carousel-content">
                    @if(isset($selectedFiliere) && isset($availableUEs) && $availableUEs->count() > 0)
                        @foreach($availableUEs as $index => $ue)
                            <!-- UE Card with Type Options -->
                            <div class="ue-card-container {{ $index === 0 ? 'active' : '' }}" data-ue-index="{{ $index }}">
                                <div class="ue-header">
                                    <strong class="ue-code">{{ $ue->code }}</strong>
                                    <span class="ue-name">{{ $ue->nom }}</span>
                                    <span class="badge bg-info ms-2">{{ $ue->semestre }}</span>
                                </div>

                                <!-- Type Options based on available hours -->
                                <div class="ue-type-options">
                                    @if($ue->heures_cm > 0)
                                        <div class="ue-type-item cm"
                                             draggable="true"
                                             data-ue-id="{{ $ue->id }}"
                                             data-ue-code="{{ $ue->code }}"
                                             data-ue-name="{{ $ue->nom }}"
                                             data-type="CM"
                                             ondragstart="handleUEDragStart(event)">
                                            <span class="type-label">CM</span>
                                            <span class="type-hours">{{ $ue->heures_cm }}h</span>
                                        </div>
                                    @endif

                                    @if($ue->heures_td > 0)
                                        <div class="ue-type-item td"
                                             draggable="true"
                                             data-ue-id="{{ $ue->id }}"
                                             data-ue-code="{{ $ue->code }}"
                                             data-ue-name="{{ $ue->nom }}"
                                             data-type="TD"
                                             ondragstart="handleUEDragStart(event)">
                                            <span class="type-label">TD</span>
                                            <span class="type-hours">{{ $ue->heures_td }}h</span>
                                        </div>
                                    @endif

                                    @if($ue->heures_tp > 0)
                                        <div class="ue-type-item tp"
                                             draggable="true"
                                             data-ue-id="{{ $ue->id }}"
                                             data-ue-code="{{ $ue->code }}"
                                             data-ue-name="{{ $ue->nom }}"
                                             data-type="TP"
                                             ondragstart="handleUEDragStart(event)">
                                            <span class="type-label">TP</span>
                                            <span class="type-hours">{{ $ue->heures_tp }}h</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="ue-card-container active">
                            <div class="text-center py-4">
                                @if(!isset($selectedFiliere))
                                    <i class="fas fa-graduation-cap fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">Sélectionnez une filière</h5>
                                    <p class="text-muted">Choisissez GI1, GI2 ou GI3 pour voir les UEs disponibles</p>
                                @elseif(isset($selectedSemester))
                                    <i class="fas fa-calendar-times fa-3x text-warning mb-3"></i>
                                    <h5 class="text-warning">Aucune UE pour {{ $selectedSemester }}</h5>
                                    <p class="text-muted">Aucune unité d'enseignement trouvée pour {{ $selectedFiliere->nom }} - {{ $selectedSemester }}</p>
                                    <button class="btn btn-outline-success btn-sm" onclick="clearSemesterFilter()">
                                        <i class="fas fa-list me-1"></i>Voir tous les semestres
                                    </button>
                                @else
                                    <i class="fas fa-book fa-3x text-info mb-3"></i>
                                    <h5 class="text-info">Aucune UE disponible</h5>
                                    <p class="text-muted">Aucune unité d'enseignement trouvée pour {{ $selectedFiliere->nom }}</p>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Carousel Navigation -->
                @if(isset($selectedFiliere) && isset($availableUEs) && $availableUEs->count() > 1)
                    <div class="ue-carousel-navigation">
                        <button class="carousel-btn" id="prevBtn" onclick="previousUE()">
                            <i class="fas fa-chevron-left"></i>
                            Précédent
                        </button>

                        <div class="carousel-indicators" id="carousel-indicators">
                            @for($i = 0; $i < $availableUEs->count(); $i++)
                                <div class="carousel-dot {{ $i === 0 ? 'active' : '' }}"
                                     onclick="goToUE({{ $i }})"
                                     data-index="{{ $i }}"></div>
                            @endfor
                        </div>

                        <button class="carousel-btn" id="nextBtn" onclick="nextUE()">
                            Suivant
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                @endif
            </div>
        </div>

        <!-- Schedule Grid (Right Side) -->
        <div class="col-md-8">
            <div class="schedule-grid">
                <table class="schedule-table w-100">
            <thead>
                <tr>
                    <th style="width: 100px;">Horaires</th>
                    <th>Lundi</th>
                    <th>Mardi</th>
                    <th>Mercredi</th>
                    <th>Jeudi</th>
                    <th>Vendredi</th>
                    <th>Samedi</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $timeSlots = [
                        '08:30-10:30',
                        '10:30-12:30',
                        '14:30-16:30',
                        '16:30-18:30'
                    ];
                    $days = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];
                @endphp

                @foreach($timeSlots as $timeSlot)
                    <tr>
                        <td class="time-slot">{{ $timeSlot }}</td>
                        @foreach($days as $day)
                            <td class="schedule-cell drop-zone"
                                data-day="{{ $day }}"
                                data-time-slot="{{ $timeSlot }}"
                                ondrop="drop(event)"
                                ondragover="allowDrop(event)">
                                @php
                                    $daySchedules = $schedules->filter(function($schedule) use ($day, $timeSlot) {
                                        $debut = \Carbon\Carbon::parse($schedule->heure_debut)->format('H:i');
                                        $fin = \Carbon\Carbon::parse($schedule->heure_fin)->format('H:i');
                                        $scheduleTimeSlot = $debut . '-' . $fin;
                                        return $schedule->jour_semaine == $day && $scheduleTimeSlot == $timeSlot;
                                    });
                                @endphp

                                @foreach($daySchedules as $schedule)
                                    <div class="schedule-slot {{ strtolower($schedule->type_seance) }}"
                                         data-schedule-id="{{ $schedule->id }}"
                                         onclick="viewScheduleDetails({{ $schedule->id }})">
                                        <div class="slot-type">{{ $schedule->type_seance }}</div>
                                        <div class="slot-code">{{ $schedule->uniteEnseignement->code }}</div>
                                        <div class="slot-title">{{ Str::limit($schedule->uniteEnseignement->nom, 20) }}</div>
                                        <div class="slot-teacher">{{ $schedule->user->name ?? 'Non assigné' }}</div>
                                        <button class="btn btn-sm btn-danger remove-schedule" onclick="removeSchedule({{ $schedule->id }})">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                @endforeach

                                @if($daySchedules->isEmpty())
                                    <div class="text-center py-2 drop-placeholder">
                                        <small class="text-muted">Glissez une UE ici</small>
                                    </div>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
            </div>

            <!-- Save Schedule Button -->
            <div class="text-center mt-4">
                <button class="btn btn-success btn-lg" onclick="saveSchedule()">
                    <i class="fas fa-save me-2"></i>Sauvegarder l'Emploi du Temps
                </button>
                <button class="btn btn-outline-primary btn-lg ms-2" data-bs-toggle="modal" data-bs-target="#uploadModal">
                    <i class="fas fa-upload me-2"></i>Charger un EDT
                </button>
            </div>

            @if($schedules->isEmpty() && !isset($selectedFiliere))
                <div class="text-center py-5">
                    <i class="fas fa-calendar-times fa-4x text-muted mb-3"></i>
                    <h4>Aucun emploi du temps</h4>
                    <p class="text-muted">Sélectionnez une année pour commencer</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Upload Modal -->
<div class="modal fade" id="uploadModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Charger un Emploi du Temps</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('coordonnateur.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="type" value="schedules">
                <div class="modal-body">
                    <div class="form-floating mb-3">
                        <select class="form-select" id="filiere_id" name="filiere_id" required>
                            <option value="">Choisir une filière...</option>
                            @foreach($filieres as $filiere)
                                <option value="{{ $filiere->id }}">{{ $filiere->nom }}</option>
                            @endforeach
                        </select>
                        <label for="filiere_id">Filière</label>
                    </div>

                    <div class="form-floating mb-3">
                        <select class="form-select" id="semestre" name="semestre" required>
                            <option value="">Choisir un semestre...</option>
                            <option value="S1">Semestre 1</option>
                            <option value="S2">Semestre 2</option>
                            <option value="S3">Semestre 3</option>
                            <option value="S4">Semestre 4</option>
                            <option value="S5">Semestre 5</option>
                            <option value="S6">Semestre 6</option>
                        </select>
                        <label for="semestre">Semestre</label>
                    </div>

                    <div class="mb-3">
                        <label for="file" class="form-label">Fichier Excel</label>
                        <input type="file" class="form-control" id="file" name="file" accept=".xlsx,.xls" required>
                        <div class="form-text">
                            Formats supportés: .xlsx, .xls (max 10MB)
                        </div>
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Format attendu:</strong> Le fichier doit contenir les colonnes suivantes:
                        <ul class="mb-0 mt-2">
                            <li>Code UE</li>
                            <li>Jour (Lundi, Mardi, etc.)</li>
                            <li>Heure début (HH:MM)</li>
                            <li>Heure fin (HH:MM)</li>
                            <li>Type séance (CM/TD/TP)</li>
                            <li>Enseignant (email)</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-success">Charger l'EDT</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Schedule Details Modal -->
<div class="modal fade" id="scheduleDetailsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Détails du Créneau</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="scheduleDetailsContent">
                <!-- Content will be loaded dynamically -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                <button type="button" class="btn btn-primary" onclick="editSchedule()">Modifier</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Drag and Drop Variables
let draggedUE = null;
let scheduleChanges = [];

// Initialize drag and drop
document.addEventListener('DOMContentLoaded', function() {
    initializeDragAndDrop();
});

function initializeDragAndDrop() {
    // Add drag event listeners to UE type items
    const ueTypeItems = document.querySelectorAll('.ue-type-item');
    ueTypeItems.forEach(item => {
        item.addEventListener('dragstart', function(e) {
            draggedUE = {
                id: this.dataset.ueId,
                code: this.dataset.ueCode,
                name: this.dataset.ueName,
                type: this.dataset.type
            };
            e.dataTransfer.effectAllowed = 'move';
            this.style.opacity = '0.5';
        });

        item.addEventListener('dragend', function(e) {
            this.style.opacity = '1';
        });
    });
}

// Handle UE drag start (called from HTML)
function handleUEDragStart(event) {
    const item = event.target;
    draggedUE = {
        id: item.dataset.ueId,
        code: item.dataset.ueCode,
        name: item.dataset.ueName,
        type: item.dataset.type
    };
    event.dataTransfer.effectAllowed = 'move';
    item.style.opacity = '0.5';

    // Reset opacity after drag
    setTimeout(() => {
        item.style.opacity = '1';
    }, 100);
}

function allowDrop(ev) {
    ev.preventDefault();
    ev.currentTarget.classList.add('drag-over');
}

async function drop(ev) {
    ev.preventDefault();
    ev.currentTarget.classList.remove('drag-over');

    if (!draggedUE) return;

    const cell = ev.currentTarget;
    const day = cell.dataset.day;
    const timeSlot = cell.dataset.timeSlot;

    // Check if cell is already occupied
    const existingSlot = cell.querySelector('.schedule-slot');
    if (existingSlot) {
        showNotification('Ce créneau est déjà occupé. Supprimez d\'abord le cours existant.', 'error');
        return;
    }

    // Check for conflicts before allowing drop
    const conflictCheck = await checkScheduleConflict(draggedUE.id, day, timeSlot, draggedUE.type);

    if (conflictCheck.conflict) {
        showNotification(conflictCheck.message, 'error');
        draggedUE = null;
        return;
    }

    // Check if type requires group selection (TD/TP)
    if (draggedUE.type === 'TD' || draggedUE.type === 'TP') {
        showGroupSelectionModal(cell, draggedUE, day, timeSlot);
    } else {
        // For CM, create directly
        createScheduleSlot(cell, draggedUE, day, timeSlot, null);
    }

    // Reset dragged UE
    draggedUE = null;
}

function createScheduleSlot(cell, ue, day, timeSlot, groupNumber) {
    // Remove placeholder
    const placeholder = cell.querySelector('.drop-placeholder');
    if (placeholder) {
        placeholder.remove();
    }

    // Get the correct type and class
    const type = ue.type || 'CM';
    const typeClass = type.toLowerCase();

    // Create abbreviation from UE name (first letters of each word)
    const abbreviation = ue.name.split(' ')
        .map(word => word.charAt(0).toUpperCase())
        .join('')
        .substring(0, 4); // Max 4 letters

    // Create group text for TD/TP (short format)
    const groupText = groupNumber ? `-G${groupNumber}` : '';

    // Create schedule slot HTML with short text
    const slotHtml = `
        <div class="schedule-slot ${typeClass}" data-ue-id="${ue.id}" data-day="${day}" data-time-slot="${timeSlot}" data-type="${type}" data-group="${groupNumber || ''}">
            <div class="slot-header">
                <span class="slot-type">${type}${groupText}</span>
                <button class="btn btn-sm btn-danger remove-schedule" onclick="removeScheduleSlot(this)">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="slot-code">${ue.code}</div>
            <div class="slot-abbreviation">${abbreviation}</div>
        </div>
    `;

    cell.innerHTML = slotHtml;

    // Save to database
    saveScheduleSlot(ue, day, timeSlot, type, groupNumber);

    // Add to changes array
    scheduleChanges.push({
        action: 'add',
        ue_id: ue.id,
        day: day,
        time_slot: timeSlot,
        type_seance: type,
        group_number: groupNumber,
        ue_code: ue.code,
        ue_name: ue.name
    });

    console.log('Schedule changes:', scheduleChanges);
}

function removeScheduleSlot(button) {
    const slot = button.closest('.schedule-slot');
    const cell = button.closest('.schedule-cell');
    const ueId = slot.dataset.ueId;
    const day = slot.dataset.day;
    const timeSlot = slot.dataset.timeSlot;

    // Add to changes array
    scheduleChanges.push({
        action: 'remove',
        ue_id: ueId,
        day: day,
        time_slot: timeSlot
    });

    // Remove slot and add placeholder
    slot.remove();
    cell.innerHTML = '<div class="text-center py-2 drop-placeholder"><small class="text-muted">Glissez une UE ici</small></div>';
}

function removeSchedule(scheduleId) {
    if (confirm('Êtes-vous sûr de vouloir supprimer ce créneau?')) {
        // Add to changes array
        scheduleChanges.push({
            action: 'delete',
            schedule_id: scheduleId
        });

        // Remove from DOM
        const slot = document.querySelector(`[data-schedule-id="${scheduleId}"]`);
        if (slot) {
            const cell = slot.closest('.schedule-cell');
            slot.remove();
            if (!cell.querySelector('.schedule-slot')) {
                cell.innerHTML = '<div class="text-center py-2 drop-placeholder"><small class="text-muted">Glissez une UE ici</small></div>';
            }
        }
    }
}

function saveSchedule() {
    if (scheduleChanges.length === 0) {
        alert('Aucune modification à sauvegarder.');
        return;
    }

    if (!selectedFiliere) {
        alert('Veuillez sélectionner une filière.');
        return;
    }

    if (!selectedSemester) {
        alert('Veuillez sélectionner un semestre.');
        return;
    }

    // Show loading
    const saveBtn = document.querySelector('button[onclick="saveSchedule()"]');
    const originalText = saveBtn.innerHTML;
    saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Sauvegarde...';
    saveBtn.disabled = true;

    // Prepare data for saving
    const saveData = {
        changes: scheduleChanges,
        filiere_id: selectedFiliere.id,
        filiere_name: selectedFiliere.name,
        semester: selectedSemester,
        current_year: new Date().getFullYear(),
        update_enseignant_schedules: true // Flag to update enseignant emploi du temps
    };

    // Send AJAX request to save coordonnateur changes and update enseignant schedules
    fetch('{{ route("coordonnateur.save-emploi-du-temps") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(saveData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message with details
            const successMessage = `
                <div class="alert alert-success alert-dismissible fade show position-fixed"
                     style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
                    <h6><i class="fas fa-check-circle me-2"></i>Sauvegarde réussie!</h6>
                    <ul class="mb-0">
                        <li>Emploi du temps coordonnateur mis à jour</li>
                        <li>Emplois du temps enseignants synchronisés</li>
                        <li>${data.affected_teachers || 0} enseignant(s) affecté(s)</li>
                        <li>Semestre: ${selectedSemester}</li>
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            document.body.insertAdjacentHTML('beforeend', successMessage);

            // Clear changes and save state
            scheduleChanges = [];
            saveScheduleState();

            // Auto-remove alert after 5 seconds
            setTimeout(() => {
                const alert = document.querySelector('.alert-success');
                if (alert) alert.remove();
            }, 5000);

        } else {
            alert('Erreur lors de la sauvegarde: ' + (data.message || 'Erreur inconnue'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Erreur lors de la sauvegarde: ' + error.message);
    })
    .finally(() => {
        saveBtn.innerHTML = originalText;
        saveBtn.disabled = false;
    });
}

// Save current schedule state to localStorage for persistence
function saveScheduleState() {
    const scheduleState = {
        filiere: selectedFiliere,
        semester: selectedSemester,
        scheduleData: getScheduleData(),
        lastSaved: new Date().toISOString()
    };

    localStorage.setItem('coordonnateur_schedule_state', JSON.stringify(scheduleState));
    console.log('Schedule state saved:', scheduleState);
}

// Get current schedule data from DOM
function getScheduleData() {
    const scheduleData = {};
    const scheduleCells = document.querySelectorAll('.schedule-cell');

    scheduleCells.forEach(cell => {
        const day = cell.dataset.day;
        const timeSlot = cell.dataset.timeSlot;
        const scheduleSlot = cell.querySelector('.schedule-slot');

        if (scheduleSlot) {
            const key = `${day}-${timeSlot}`;
            scheduleData[key] = {
                ue_id: scheduleSlot.dataset.ueId,
                ue_code: scheduleSlot.querySelector('.slot-code')?.textContent,
                ue_name: scheduleSlot.querySelector('.slot-title')?.textContent,
                type_seance: scheduleSlot.querySelector('.slot-type')?.textContent,
                teacher: scheduleSlot.querySelector('.slot-teacher')?.textContent
            };
        }
    });

    return scheduleData;
}

// Load schedule state from localStorage
function loadScheduleState() {
    const savedState = localStorage.getItem('coordonnateur_schedule_state');
    if (savedState) {
        try {
            const state = JSON.parse(savedState);
            console.log('Loading saved schedule state:', state);

            // Restore filière and semester selection
            if (state.filiere && state.semester) {
                selectedFiliere = state.filiere;
                selectedSemester = state.semester;

                // Update UI to reflect saved state
                updateUIFromSavedState(state);
            }
        } catch (error) {
            console.error('Error loading schedule state:', error);
        }
    }
}

// Update UI from saved state
function updateUIFromSavedState(state) {
    // Update filière button
    const filiereBtn = document.querySelector(`[data-filiere="${state.filiere.name}"]`);
    if (filiereBtn) {
        filiereBtn.classList.remove('btn-outline-primary');
        filiereBtn.classList.add('btn-primary');
    }

    // Update semester buttons
    updateSemesterButtons(state.filiere.name);

    // Update semester button
    setTimeout(() => {
        const semesterBtn = document.querySelector(`[data-semester="${state.semester}"]`);
        if (semesterBtn) {
            semesterBtn.classList.remove('btn-outline-success');
            semesterBtn.classList.add('btn-success');
        }

        // Update badge
        document.getElementById('selectedSemesterText').textContent = state.semester;
        document.getElementById('selectedSemesterBadge').style.display = 'inline-block';
    }, 100);
}

function changeFiliere(filiereId) {
    // Implementation for filtering by filiere
    console.log('Change filiere:', filiereId);

    // Update active tab
    document.querySelectorAll('.filiere-tab').forEach(tab => {
        tab.classList.remove('active');
    });
    event.target.classList.add('active');

    // Filter schedule slots
    const slots = document.querySelectorAll('.schedule-slot');
    slots.forEach(slot => {
        if (filiereId === 'all') {
            slot.style.display = 'block';
        } else {
            // Implementation for filtering by filiere
            slot.style.display = 'block';
        }
    });
}

function viewScheduleDetails(scheduleId) {
    const modal = new bootstrap.Modal(document.getElementById('scheduleDetailsModal'));
    document.getElementById('scheduleDetailsContent').innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Chargement...</span>
            </div>
        </div>
    `;
    modal.show();

    // Load details via AJAX
    setTimeout(() => {
        document.getElementById('scheduleDetailsContent').innerHTML = `
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                Détails du créneau #${scheduleId}
            </div>
            <p>Fonctionnalité en cours de développement...</p>
        `;
    }, 1000);
}

function editSchedule() {
    // Implementation for editing schedule
    console.log('Edit schedule');
}

function exportSchedule() {
    // Implementation for exporting schedule
    window.location.href = '{{ route("coordonnateur.export") }}?type=schedules';
}

function handleFileUpload(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const fileName = file.name;

        // Show upload modal with pre-filled file
        const modal = new bootstrap.Modal(document.getElementById('uploadModal'));
        modal.show();

        // Update file input in modal
        document.getElementById('file').files = input.files;
    }
}
</script>
@endpush

@push('scripts')
<script>
// Global Variables
let draggedUE = null;
let scheduleChanges = [];
let currentUEIndex = 0;
let totalUEs = 0;

// Initialize everything when page loads
document.addEventListener('DOMContentLoaded', function() {
    initializePage();
});

function initializePage() {
    initializeDragAndDrop();
    initializeCarousel();
    console.log('Emploi du temps initialized successfully');
}

// Filiere and Semester Selection Functions
function selectFiliere(filiereName, filiereId) {
    console.log('Selecting filiere:', filiereName, filiereId);
    showNotification('Chargement de la filière ' + filiereName + '...', 'info');

    // Navigate to the page with filiere parameter
    window.location.href = `{{ route('coordonnateur.emplois-du-temps') }}?filiere_id=${filiereId}`;
}

function selectSemester(semester) {
    console.log('Selecting semester:', semester);
    showNotification('Chargement du semestre ' + semester + '...', 'info');

    // Get current URL parameters
    const urlParams = new URLSearchParams(window.location.search);
    const currentFiliereId = urlParams.get('filiere_id');

    // Build new URL with semester parameter
    let newUrl = `{{ route('coordonnateur.emplois-du-temps') }}`;
    if (currentFiliereId) {
        newUrl += `?filiere_id=${currentFiliereId}&semester=${semester}`;
    } else {
        newUrl += `?semester=${semester}`;
    }

    // Navigate to new URL
    window.location.href = newUrl;
}

function clearSemesterFilter() {
    console.log('Clearing semester filter');
    showNotification('Affichage de tous les semestres...', 'info');

    // Get current URL parameters
    const urlParams = new URLSearchParams(window.location.search);
    const currentFiliereId = urlParams.get('filiere_id');

    // Build new URL without semester parameter
    let newUrl = `{{ route('coordonnateur.emplois-du-temps') }}`;
    if (currentFiliereId) {
        newUrl += `?filiere_id=${currentFiliereId}`;
    }

    // Navigate to new URL
    window.location.href = newUrl;
}

// Initialize drag and drop functionality
function initializeDragAndDrop() {
    const ueTypeItems = document.querySelectorAll('.ue-type-item');
    ueTypeItems.forEach(item => {
        item.addEventListener('dragstart', function(e) {
            draggedUE = {
                id: this.dataset.ueId,
                code: this.dataset.ueCode,
                name: this.dataset.ueName,
                type: this.dataset.type
            };
            e.dataTransfer.effectAllowed = 'move';
            this.style.opacity = '0.5';
            console.log('Dragging UE:', draggedUE);
        });

        item.addEventListener('dragend', function(e) {
            this.style.opacity = '1';
        });
    });
}

// Drag and Drop Functions
function allowDrop(ev) {
    ev.preventDefault();
    ev.currentTarget.classList.add('drag-over');
}

function drop(ev) {
    ev.preventDefault();
    ev.currentTarget.classList.remove('drag-over');

    if (!draggedUE) {
        console.log('No dragged UE');
        return;
    }

    const cell = ev.currentTarget;
    const day = cell.dataset.day;
    const timeSlot = cell.dataset.timeSlot;

    console.log('Dropping UE:', draggedUE, 'at', day, timeSlot);

    // Check if cell is already occupied
    const existingSlot = cell.querySelector('.schedule-slot');
    if (existingSlot) {
        showNotification('Ce créneau est déjà occupé. Supprimez d\'abord le cours existant.', 'error');
        return;
    }

    // For TD/TP, show group selection modal
    if (draggedUE.type === 'TD' || draggedUE.type === 'TP') {
        showGroupSelectionModal(cell, draggedUE, day, timeSlot);
    } else {
        // For CM, create directly
        createScheduleSlot(cell, draggedUE, day, timeSlot, null);
    }

    // Reset dragged UE
    draggedUE = null;
}

// Handle UE drag start (called from HTML)
function handleUEDragStart(event) {
    const item = event.target;
    draggedUE = {
        id: item.dataset.ueId,
        code: item.dataset.ueCode,
        name: item.dataset.ueName,
        type: item.dataset.type
    };
    event.dataTransfer.effectAllowed = 'move';
    item.style.opacity = '0.5';
    console.log('Dragging UE:', draggedUE);

    // Reset opacity after drag
    setTimeout(() => {
        item.style.opacity = '1';
    }, 100);
}
