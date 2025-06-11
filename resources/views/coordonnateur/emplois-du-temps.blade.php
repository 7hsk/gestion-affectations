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

/* Button container for multiple buttons */
.slot-buttons {
    position: absolute;
    top: 2px;
    right: 2px;
    display: none;
    gap: 2px;
    flex-direction: row;
}

.schedule-slot:hover .slot-buttons {
    display: flex;
}

/* Empty slot button styling */
.empty-slot-btn {
    background-color: #f59e0b !important;
    color: white !important;
    border: 2px solid #d97706 !important;
    font-weight: bold !important;
    width: 20px;
    height: 20px;
    padding: 0;
    border-radius: 50%;
    font-size: 10px;
    line-height: 1;
    display: flex;
    align-items: center;
    justify-content: center;
}

.empty-slot-btn:hover {
    background-color: #d97706 !important;
    border-color: #b45309 !important;
    transform: scale(1.1) !important;
}

/* Remove button styling */
.remove-schedule {
    background-color: #f44336 !important;
    color: white !important;
    border: 2px solid #d32f2f !important;
    font-weight: bold !important;
    width: 20px;
    height: 20px;
    padding: 0;
    border-radius: 50%;
    font-size: 10px;
    line-height: 1;
    display: flex;
    align-items: center;
    justify-content: center;
}

.remove-schedule:hover {
    background-color: #d32f2f !important;
    border-color: #b71c1c !important;
    transform: scale(1.1) !important;
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

.remove-schedule,
.delete-existing-schedule {
    position: absolute;
    top: 2px;
    right: 2px;
    width: 24px !important;
    height: 24px !important;
    padding: 0;
    border-radius: 50%;
    font-size: 0.8rem !important;
    line-height: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    border: none;
    cursor: pointer;
    transition: all 0.2s ease;
    z-index: 10 !important;
}

/* X button for ALL UEs (existing and new - SAME STYLE) */
.schedule-slot .remove-schedule {
    background-color: #f44336 !important;
    color: white !important;
    border: 2px solid #d32f2f !important;
    font-weight: bold !important;
    box-shadow: 0 2px 4px rgba(0,0,0,0.3) !important;
}

.schedule-slot .remove-schedule:hover {
    background-color: #d32f2f !important;
    border-color: #b71c1c !important;
    transform: scale(1.2) !important;
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
            <button class="btn btn-outline-primary" onclick="openExportModal()">
                <i class="fas fa-file-pdf me-2"></i>Exporter PDF
            </button>
        </div>
    </div>

    <!-- Upload Section Removed - Import not in allowed list -->

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
                            @if(isset($selectedFiliere) && isset($availableSemesters) && count($availableSemesters) > 0)
                                <button type="button"
                                        class="btn semester-btn {{ !isset($selectedSemester) ? 'btn-success' : 'btn-outline-success' }}"
                                        onclick="clearSemesterFilter()">
                                    <i class="fas fa-list me-1"></i>Tous
                                </button>
                                @foreach($availableSemesters as $semester)
                                    <button type="button"
                                            class="btn semester-btn {{ isset($selectedSemester) && $selectedSemester == $semester ? 'btn-success' : 'btn-outline-success' }}"
                                            data-semester="{{ $semester }}"
                                            onclick="selectSemester('{{ $semester }}')">
                                        {{ $semester }}
                                    </button>
                                @endforeach
                            @else
                                <p class="text-muted mb-0">Sélectionnez d'abord une filière</p>
                            @endif
                        </div>
                    </div>
                </div>
                @if(isset($selectedFiliere))
                    <div class="mt-3">
                        <span class="badge bg-success">
                            <i class="fas fa-check me-1"></i>Filière: {{ $selectedFiliere->nom }}
                        </span>
                        @if(isset($selectedSemester))
                            <span class="badge bg-warning ms-2">
                                <i class="fas fa-calendar-week me-1"></i>Semestre: {{ $selectedSemester }}
                            </span>
                        @else
                            <span class="badge bg-secondary ms-2">
                                <i class="fas fa-list me-1"></i>Tous les semestres
                            </span>
                        @endif
                        <span class="badge bg-info ms-2">
                            <i class="fas fa-clock me-1"></i>{{ $schedules->count() }} Créneaux
                        </span>
                        @if(isset($availableUEs))
                            <span class="badge bg-primary ms-2">
                                <i class="fas fa-graduation-cap me-1"></i>{{ $availableUEs->count() }} UEs disponibles
                            </span>
                        @endif
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
                        @if(isset($availableUEs) && $availableUEs->count() > 0)
                            <span class="text-success">
                                <i class="fas fa-check-circle me-1"></i>
                                1 / {{ $availableUEs->count() }}
                            </span>
                        @else
                            <span class="text-warning">
                                <i class="fas fa-exclamation-triangle me-1"></i>
                                Aucune UE
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Carousel Content -->
                <div class="ue-carousel-content">
                    @if(isset($availableUEs) && $availableUEs->count() > 0)
                        @foreach($availableUEs as $index => $ue)
                            <!-- UE Card with Type Options -->
                            <div class="ue-card-container {{ $index === 0 ? 'active' : '' }}" data-ue-index="{{ $index }}">
                                <div class="ue-header">
                                    <strong>{{ $ue->code }}</strong>
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
                                    <p class="text-muted">Choisissez une filière pour voir les UEs disponibles</p>
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
                @if(isset($availableUEs) && $availableUEs->count() > 1)
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
                                    @php
                                        // Create abbreviation from UE name (first letters of each word)
                                        $abbreviation = collect(explode(' ', $schedule->uniteEnseignement->nom))
                                            ->map(fn($word) => strtoupper(substr($word, 0, 1)))
                                            ->take(4)
                                            ->join('');

                                        // Create group text for TD/TP
                                        $groupText = $schedule->group_number ? '-G' . $schedule->group_number : '';
                                    @endphp

                                    <!-- EXISTING UEs - ONLY ORANGE "VIDER CRÉNEAU" BUTTON -->
                                    <div class="schedule-slot {{ strtolower($schedule->type_seance) }}"
                                         data-ue-id="{{ $schedule->ue_id }}"
                                         data-day="{{ $day }}"
                                         data-time-slot="{{ $timeSlot }}"
                                         data-type="{{ $schedule->type_seance }}"
                                         data-group="{{ $schedule->group_number ?? '' }}"
                                         data-ue-code="{{ $schedule->uniteEnseignement->code }}"
                                         data-ue-name="{{ $schedule->uniteEnseignement->nom }}"
                                         data-schedule-id="{{ $schedule->id }}"
                                         data-existing="true">
                                        <div class="slot-type">{{ $schedule->type_seance }}{{ $groupText }}</div>
                                        <div class="slot-code">{{ $schedule->uniteEnseignement->code }}</div>
                                        <div class="slot-abbreviation">{{ $abbreviation }}</div>
                                        <div class="slot-teacher">{{ $schedule->user->name ?? 'Non assigné' }}</div>

                                        <!-- ONLY ORANGE BUTTON FOR EXISTING UEs -->
                                        <div class="slot-buttons">
                                            <button class="btn btn-sm btn-warning empty-slot-btn" title="Vider le créneau" onclick="emptySlot(this)">
                                                <i class="fas fa-eraser"></i>
                                            </button>
                                        </div>
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

<!-- Export Modal -->
<div class="modal fade" id="exportModal" tabindex="-1" style="z-index: 99999; position: fixed;">
    <div class="modal-dialog" style="z-index: 100000; position: relative;">
        <div class="modal-content" style="z-index: 100001; pointer-events: auto;">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-file-pdf me-2"></i>Exporter Emploi du Temps
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Sélectionnez la filière et le semestre pour exporter l'emploi du temps correspondant depuis la base de données.
                </div>

                <div class="form-floating mb-3">
                    <select class="form-select" id="export_filiere_id" required>
                        <option value="">Choisir une filière...</option>
                        @foreach($filieres as $filiere)
                            <option value="{{ $filiere->id }}">{{ $filiere->nom }}</option>
                        @endforeach
                    </select>
                    <label for="export_filiere_id">Filière</label>
                </div>

                <div class="form-floating mb-3">
                    <select class="form-select" id="export_semester" required>
                        <option value="">Choisir un semestre...</option>
                        <option value="S1">Semestre 1</option>
                        <option value="S2">Semestre 2</option>
                        <option value="S3">Semestre 3</option>
                        <option value="S4">Semestre 4</option>
                        <option value="S5">Semestre 5</option>
                        <option value="S6">Semestre 6</option>
                    </select>
                    <label for="export_semester">Semestre</label>
                </div>

                <div class="form-floating mb-3">
                    <select class="form-select" id="export_year">
                        <option value="{{ date('Y') }}">{{ date('Y') }}-{{ date('Y') + 1 }}</option>
                        <option value="{{ date('Y') - 1 }}">{{ date('Y') - 1 }}-{{ date('Y') }}</option>
                        <option value="{{ date('Y') + 1 }}">{{ date('Y') + 1 }}-{{ date('Y') + 2 }}</option>
                    </select>
                    <label for="export_year">Année Universitaire</label>
                </div>

                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Note:</strong> L'export générera un PDF avec l'état actuel sauvegardé dans la base de données pour la filière et le semestre sélectionnés.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary" onclick="confirmExport()">
                    <i class="fas fa-download me-2"></i>Exporter PDF
                </button>
            </div>
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

@push('styles')
<style>
/* Ensure export modal appears above all overlays */
#exportModal {
    z-index: 99999 !important;
    position: fixed !important;
}

#exportModal .modal-backdrop {
    z-index: 99998 !important;
}

.modal-backdrop.show {
    z-index: 99998 !important;
}

/* Ensure modal content is above backdrop */
#exportModal .modal-dialog {
    z-index: 100000 !important;
    position: relative;
}

/* Override any conflicting z-index */
.modal.show {
    z-index: 99999 !important;
}

/* Ensure modal is clickable */
#exportModal .modal-content {
    z-index: 100001 !important;
    position: relative;
    pointer-events: auto !important;
}

/* Make sure backdrop doesn't block interaction */
.modal-backdrop {
    pointer-events: none !important;
}

#exportModal .modal-backdrop {
    pointer-events: auto !important;
}
</style>
@endpush

@push('scripts')
<script>
// Drag and Drop Variables
let draggedUE = null;
let scheduleChanges = [];

// Track placed UEs to prevent duplicates
let placedUEs = {
    // Structure: { ue_id: { CM: true, TD: [1, 2], TP: [1] } }
};

// Store original UE positions for returning them
let originalUEPositions = new Map();

// Initialize drag and drop
document.addEventListener('DOMContentLoaded', function() {
    initializeDragAndDrop();
});

function initializeDragAndDrop() {
    console.log('Initializing drag and drop...');

    // Remove existing event listeners to avoid duplicates
    const existingItems = document.querySelectorAll('.ue-type-item');
    existingItems.forEach(item => {
        // Clone and replace to remove all event listeners
        const newItem = item.cloneNode(true);
        item.parentNode.replaceChild(newItem, item);
    });

    // Add drag event listeners to UE type items (fresh)
    const ueTypeItems = document.querySelectorAll('.ue-type-item');
    console.log('Found UE type items:', ueTypeItems.length);

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
            console.log('Drag started for:', draggedUE);
        });

        item.addEventListener('dragend', function(e) {
            this.style.opacity = '1';
            console.log('Drag ended');
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

    console.log('Drop function called with draggedUE:', draggedUE);

    if (!draggedUE) {
        console.log('No draggedUE - returning');
        return;
    }

    const cell = ev.currentTarget;
    const day = cell.dataset.day;
    const timeSlot = cell.dataset.timeSlot;

    console.log('Drop target:', { day, timeSlot });

    // Check if cell is already occupied
    const existingSlot = cell.querySelector('.schedule-slot');
    if (existingSlot) {
        showNotification('Ce créneau est déjà occupé. Supprimez d\'abord le cours existant.', 'error');
        return;
    }

    // Check for duplicate placement prevention (check DOM instead of tracking)
    if (!canPlaceUEInDOM(draggedUE.id, draggedUE.type)) {
        const message = draggedUE.type === 'CM'
            ? `Le cours magistral de ${draggedUE.code} est déjà placé dans l'emploi du temps.`
            : `Tous les groupes ${draggedUE.type} de ${draggedUE.code} sont déjà placés.`;
        showNotification(message, 'error');
        draggedUE = null;
        return;
    }

    // Store original position for potential return
    storeOriginalUEPosition(draggedUE);

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
        // For CM, create directly (NEW UE - red X button)
        createScheduleSlot(cell, draggedUE, day, timeSlot, null, false);
    }

    // Reset dragged UE
    draggedUE = null;
}

// Check if UE can be placed by checking DOM (for fresh drop simulation)
function canPlaceUEInDOM(ueId, type, groupNumber = null) {
    // Check existing schedule slots in DOM
    const existingSlots = document.querySelectorAll(`.schedule-slot[data-ue-id="${ueId}"][data-type="${type}"]`);

    if (type === 'CM') {
        // CM can only be placed once
        return existingSlots.length === 0;
    } else if (type === 'TD' || type === 'TP') {
        // For TD/TP, check if this specific group is already placed
        if (groupNumber) {
            const groupSlots = Array.from(existingSlots).filter(slot =>
                slot.dataset.group === groupNumber.toString()
            );
            return groupSlots.length === 0;
        }
        // If no group specified yet, check if all 3 groups are used
        return existingSlots.length < 3;
    }

    return true;
}

// Legacy function for backward compatibility
function canPlaceUE(ueId, type, groupNumber = null) {
    return canPlaceUEInDOM(ueId, type, groupNumber);
}

// Store original UE position for returning (still needed for drag/drop)
function storeOriginalUEPosition(ue) {
    const ueElement = document.querySelector(`[data-ue-id="${ue.id}"][data-type="${ue.type}"]`);
    if (ueElement) {
        const container = ueElement.closest('.ue-card-container');
        if (container) {
            const containerIndex = Array.from(container.parentElement.children).indexOf(container);

            originalUEPositions.set(`${ue.id}-${ue.type}`, {
                ue: ue,
                containerIndex: containerIndex,
                element: ueElement.cloneNode(true)
            });
        }
    }
}

// Mark UE as placed (simplified for DOM-based approach)
function markUEAsPlaced(ueId, type, groupNumber = null) {
    // This is now mainly for tracking, DOM is the source of truth
    if (!placedUEs[ueId]) {
        placedUEs[ueId] = {};
    }

    if (type === 'CM') {
        placedUEs[ueId].CM = true;
    } else if (type === 'TD' || type === 'TP') {
        if (!placedUEs[ueId][type]) {
            placedUEs[ueId][type] = [];
        }
        if (groupNumber && !placedUEs[ueId][type].includes(groupNumber)) {
            placedUEs[ueId][type].push(groupNumber);
        }
    }
}

// Mark UE as removed (simplified for DOM-based approach)
function markUEAsRemoved(ueId, type, groupNumber = null) {
    if (!placedUEs[ueId]) return;

    if (type === 'CM') {
        placedUEs[ueId].CM = false;
    } else if (type === 'TD' || type === 'TP') {
        if (placedUEs[ueId][type] && groupNumber) {
            const index = placedUEs[ueId][type].indexOf(groupNumber);
            if (index > -1) {
                placedUEs[ueId][type].splice(index, 1);
            }
        }
    }
}

function createScheduleSlot(cell, ue, day, timeSlot, groupNumber, isExisting = false) {
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

    // DIFFERENT BUTTONS FOR EXISTING vs NEW UEs
    let buttonsHtml = '';
    if (isExisting) {
        // EXISTING UEs: Only orange "Vider créneau" button
        buttonsHtml = `
            <div class="slot-buttons">
                <button class="btn btn-sm btn-warning empty-slot-btn" title="Vider le créneau">
                    <i class="fas fa-eraser"></i>
                </button>
            </div>
        `;
    } else {
        // NEW UEs: Only red "X" button (return to carousel)
        buttonsHtml = `
            <div class="slot-buttons">
                <button class="btn btn-sm btn-danger remove-schedule" title="Retourner au carousel">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
    }

    // Create schedule slot HTML with appropriate buttons
    const slotHtml = `
        <div class="schedule-slot ${typeClass}"
             data-ue-id="${ue.id}"
             data-day="${day}"
             data-time-slot="${timeSlot}"
             data-type="${type}"
             data-group="${groupNumber || ''}"
             data-ue-code="${ue.code}"
             data-ue-name="${ue.name}"
             data-existing="${isExisting}">
            <div class="slot-type">${type}${groupText}</div>
            <div class="slot-code">${ue.code}</div>
            <div class="slot-abbreviation">${abbreviation}</div>
            <div class="slot-teacher">Non assigné</div>
            ${buttonsHtml}
        </div>
    `;

    cell.innerHTML = slotHtml;

    if (isExisting) {
        // EXISTING UEs: Only empty button (orange)
        const emptyButton = cell.querySelector('.empty-slot-btn');
        if (emptyButton) {
            emptyButton.addEventListener('click', function(e) {
                e.stopPropagation();
                emptySlot(this);
            });
        }
        console.log(`✅ EXISTING UE ${ue.code} (${type}) placed with EMPTY button only (🗑️)`);
    } else {
        // NEW UEs: Only remove button (red X)
        const removeButton = cell.querySelector('.remove-schedule');
        if (removeButton) {
            removeButton.addEventListener('click', function(e) {
                e.stopPropagation();
                removeScheduleAndReturnUE(this);
            });
        }
        console.log(`✅ NEW UE ${ue.code} (${type}) placed with REMOVE button only (❌)`);
    }

    // Mark UE as placed (for tracking)
    markUEAsPlaced(ue.id, type, groupNumber);

    // Remove UE from carousel
    removeUEFromCarousel(ue.id, type);

    console.log(`✅ UE ${ue.code} (${type}) placed successfully with dual buttons: Empty (🗑️) and Remove (❌)`);
}

// Remove UE from carousel when placed (DOM-based logic)
function removeUEFromCarousel(ueId, type) {
    const ueElement = document.querySelector(`[data-ue-id="${ueId}"][data-type="${type}"]`);
    if (!ueElement) return;

    if (type === 'CM') {
        // CM disappears immediately after placement
        ueElement.style.display = 'none';
        ueElement.classList.add('placed');
    } else if (type === 'TD' || type === 'TP') {
        // TD/TP only disappear when all groups are used (check DOM)
        if (areAllGroupsUsedInDOM(ueId, type)) {
            ueElement.style.display = 'none';
            ueElement.classList.add('placed');
        }
    }
}

// Check if all groups are used for TD/TP (DOM-based)
function areAllGroupsUsedInDOM(ueId, type) {
    if (type !== 'TD' && type !== 'TP') return false;

    const maxGroups = getMaxGroupsForUE(ueId, type);
    const existingSlots = document.querySelectorAll(`.schedule-slot[data-ue-id="${ueId}"][data-type="${type}"]`);

    return existingSlots.length >= maxGroups;
}

// Get maximum number of groups for a UE type
function getMaxGroupsForUE(ueId, type) {
    // Default to 3 groups for TD/TP
    return 3;
}

// Return UE to carousel when removed (DOM-based logic)
function returnUEToCarousel(ueId, type) {
    const ueElement = document.querySelector(`[data-ue-id="${ueId}"][data-type="${type}"]`);
    if (!ueElement) return;

    if (type === 'CM') {
        // CM always returns when removed
        ueElement.style.display = 'block';
        ueElement.classList.remove('placed');
    } else if (type === 'TD' || type === 'TP') {
        // TD/TP return to visible if not all groups are used (check DOM)
        if (!areAllGroupsUsedInDOM(ueId, type)) {
            ueElement.style.display = 'block';
            ueElement.classList.remove('placed');
        }
    }
}

// NEW FUNCTION: Empty slot without returning UE to carousel
function emptySlot(button) {
    const slot = button.closest('.schedule-slot');
    const cell = button.closest('.schedule-cell');

    const ueCode = slot.dataset.ueCode;
    const type = slot.dataset.type;
    const day = slot.dataset.day;
    const timeSlot = slot.dataset.timeSlot;
    const groupNumber = slot.dataset.group || null;
    const ueId = slot.dataset.ueId;

    console.log(`🗑️ Emptying slot: ${ueCode} (${type}) from ${day} ${timeSlot} - UE will NOT return to carousel`);

    // Remove slot and add placeholder (make it empty)
    slot.remove();
    cell.innerHTML = '<div class="text-center py-2 drop-placeholder"><small class="text-muted">Glissez une UE ici</small></div>';

    // Mark UE as removed from tracking but DON'T return to carousel
    markUEAsRemoved(ueId, type, groupNumber);

    console.log(`✅ Slot emptied successfully: ${ueCode} (${type}) - UE stays hidden`);
}

// Remove schedule and return UE to carousel (NO TABLE REFRESH)
function removeScheduleAndReturnUE(button, scheduleId = null) {
    const slot = button.closest('.schedule-slot');
    const cell = button.closest('.schedule-cell');

    const ueId = slot.dataset.ueId;
    const ueCode = slot.dataset.ueCode;
    const ueName = slot.dataset.ueName;
    const type = slot.dataset.type;
    const groupNumber = slot.dataset.group || null;
    const day = slot.dataset.day;
    const timeSlot = slot.dataset.timeSlot;

    console.log(`Removing ${ueCode} (${type}) from ${day} ${timeSlot} - making available for drag again`);

    // Remove slot and add placeholder
    slot.remove();
    cell.innerHTML = '<div class="text-center py-2 drop-placeholder"><small class="text-muted">Glissez une UE ici</small></div>';

    // Mark UE as removed from tracking
    markUEAsRemoved(ueId, type, groupNumber);

    // Return UE to carousel directly (NO REFRESH)
    returnUEToCarouselDirectly(ueId, ueCode, ueName, type);

    console.log('UE returned to carousel without refresh:', { ueId, ueCode, type, groupNumber });
}

// Return UE directly to carousel without refreshing table
function returnUEToCarouselDirectly(ueId, ueCode, ueName, type) {
    console.log(`Returning ${ueCode} (${type}) directly to carousel`);

    // Check if UE already exists in carousel
    const existingUE = document.querySelector(`[data-ue-id="${ueId}"][data-type="${type}"]`);
    if (existingUE) {
        // Just make it visible again
        existingUE.style.display = 'block';
        existingUE.classList.remove('placed');
        console.log(`${ueCode} (${type}) made visible in carousel`);
        return;
    }

    // If not in carousel, add it back
    addUEToCarousel(ueId, ueCode, ueName, type);
    console.log(`${ueCode} (${type}) added back to carousel`);
}

// Add UE back to carousel
function addUEToCarousel(ueId, ueCode, ueName, type) {
    const ueCarousel = document.querySelector('.ue-carousel');
    if (!ueCarousel) return;

    // Find the current UE container or create new one
    let ueContainer = ueCarousel.querySelector(`[data-ue-id="${ueId}"]`);

    if (!ueContainer) {
        // Create new UE container
        ueContainer = document.createElement('div');
        ueContainer.className = 'ue-card-container';
        ueContainer.dataset.ueId = ueId;

        const ueCard = document.createElement('div');
        ueCard.className = 'ue-card';
        ueCard.innerHTML = `
            <div class="ue-header">
                <h6 class="ue-code">${ueCode}</h6>
                <p class="ue-name">${ueName}</p>
            </div>
        `;

        ueContainer.appendChild(ueCard);
        ueCarousel.appendChild(ueContainer);
    }

    // Add the specific type back
    const typeColors = { 'CM': 'cm', 'TD': 'td', 'TP': 'tp' };
    const typeClass = typeColors[type] || 'cm';

    const typeItem = document.createElement('div');
    typeItem.className = `ue-type-item ${typeClass}`;
    typeItem.draggable = true;
    typeItem.dataset.ueId = ueId;
    typeItem.dataset.ueCode = ueCode;
    typeItem.dataset.ueName = ueName;
    typeItem.dataset.type = type;

    typeItem.innerHTML = `
        <span class="type-label">${type}</span>
        <span class="type-hours">-h</span>
    `;

    // Add drag event listeners
    typeItem.addEventListener('dragstart', function(e) {
        draggedUE = {
            id: this.dataset.ueId,
            code: this.dataset.ueCode,
            name: this.dataset.ueName,
            type: this.dataset.type
        };
        e.dataTransfer.effectAllowed = 'move';
        this.style.opacity = '0.5';
    });

    typeItem.addEventListener('dragend', function(e) {
        this.style.opacity = '1';
    });

    ueContainer.appendChild(typeItem);
}

// REMOVED: createExistingScheduleSlot - now using same function as fresh drops

// REMOVED: hideExistingSchedule - now using same function as fresh drops

function removeScheduleSlot(button) {
    // This function is kept for backward compatibility
    removeScheduleAndReturnUE(button);
}

// REMOVED: reloadDataToMakeUEAvailable() - no longer needed since we return directly

// REMOVED: No confirmation dialogs - use removeScheduleAndReturnUE instead

// Get current schedule items from DOM (what's actually visible in the table)
function getCurrentScheduleItems() {
    const scheduleItems = [];
    const scheduleCells = document.querySelectorAll('.schedule-cell');

    scheduleCells.forEach(cell => {
        const day = cell.dataset.day;
        const timeSlot = cell.dataset.timeSlot;
        const scheduleSlot = cell.querySelector('.schedule-slot');

        if (scheduleSlot) {
            const ueId = scheduleSlot.dataset.ueId;
            const type = scheduleSlot.dataset.type;
            const group = scheduleSlot.dataset.group;
            const ueCode = scheduleSlot.dataset.ueCode;
            const ueName = scheduleSlot.dataset.ueName;

            // Parse time slot
            const [heureDebut, heureFin] = timeSlot.split('-');

            scheduleItems.push({
                ue_id: ueId,
                ue_code: ueCode,
                ue_name: ueName,
                jour_semaine: day,
                heure_debut: heureDebut,
                heure_fin: heureFin,
                type_seance: type,
                groupe: group || null,
                time_slot: timeSlot
            });
        }
    });

    console.log('Current schedule items from DOM:', scheduleItems);
    return scheduleItems;
}

function saveSchedule() {
    if (!selectedFiliere) {
        alert('Veuillez sélectionner une filière.');
        return;
    }

    if (!selectedSemester) {
        alert('Veuillez sélectionner un semestre.');
        return;
    }

    // Get current visible schedule items from DOM (ALL current state)
    const currentScheduleItems = getCurrentScheduleItems();

    console.log('Current schedule items to save:', currentScheduleItems);

    // Allow saving even with no schedules (to clear existing data)
    if (currentScheduleItems.length === 0) {
        console.log('No schedules to save - will clear all existing data for this filiere+semester');
    }

    // Show loading
    const saveBtn = document.querySelector('button[onclick="saveSchedule()"]');
    const originalText = saveBtn.innerHTML;
    saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Sauvegarde...';
    saveBtn.disabled = true;

    // Prepare data for saving - send current state, replace all existing data
    const saveData = {
        schedule_items: currentScheduleItems, // Current visible items
        filiere_id: selectedFiliere.id,
        filiere_name: selectedFiliere.name,
        semester: selectedSemester,
        current_year: new Date().getFullYear(),
        replace_all: true, // Flag to replace all existing data
        update_enseignant_schedules: true
    };

    console.log('Saving current schedule state (replace all):', saveData);

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
                        <li>${data.processed_schedules || 0} créneaux sauvegardés</li>
                        <li>${data.deleted_schedules || 0} anciens créneaux supprimés</li>
                        <li>Semestre: ${selectedSemester}</li>
                        <li><i class="fas fa-sync-alt fa-spin me-1"></i>Actualisation en cours...</li>
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            document.body.insertAdjacentHTML('beforeend', successMessage);

            // Clear changes and tracking after successful save
            scheduleChanges = [];
            placedUEs = {}; // Reset tracking
            originalUEPositions.clear(); // Clear stored positions

            // QUICK REFRESH - reload current semester data to show updated schedules
            console.log('Save successful - refreshing current semester data...');

            // Reload the current filière and semester data
            if (selectedFiliere && selectedSemester) {
                setTimeout(() => {
                    loadEmploiDuTempsData(selectedFiliere.id, selectedSemester);
                    console.log('🔄 Data refreshed after save!');
                }, 500); // Small delay to ensure save is complete
            }

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



function editSchedule() {
    // Implementation for editing schedule
    console.log('Edit schedule');
}

// Open export modal with proper z-index handling
function openExportModal() {
    console.log('🔥 Opening export modal...');

    // Remove any existing modal backdrops
    const existingBackdrops = document.querySelectorAll('.modal-backdrop');
    existingBackdrops.forEach(backdrop => backdrop.remove());

    const modalElement = document.getElementById('exportModal');

    // Ensure modal appears above everything with very high z-index
    modalElement.style.zIndex = '99999';
    modalElement.style.position = 'fixed';
    modalElement.style.pointerEvents = 'auto';

    // Pre-fill with current selections
    const currentFiliere = document.getElementById('filiere_select')?.value;
    const currentSemester = document.getElementById('semester_select')?.value;
    const currentYear = document.getElementById('year_select')?.value || new Date().getFullYear();

    if (currentFiliere) {
        document.getElementById('export_filiere_id').value = currentFiliere;
    }

    if (currentSemester) {
        document.getElementById('export_semester').value = currentSemester;
    }

    document.getElementById('export_year').value = currentYear;

    console.log('📋 Pre-filled modal with:', {
        filiere: currentFiliere,
        semester: currentSemester,
        year: currentYear
    });

    // Create and show modal
    const modal = new bootstrap.Modal(modalElement, {
        backdrop: 'static',
        keyboard: true,
        focus: true
    });

    modal.show();

    // Ensure proper z-index after modal is shown
    setTimeout(() => {
        modalElement.style.zIndex = '99999';

        const backdrop = document.querySelector('.modal-backdrop');
        if (backdrop) {
            backdrop.style.zIndex = '99998';
            backdrop.style.pointerEvents = 'auto';
        }

        const modalContent = modalElement.querySelector('.modal-content');
        if (modalContent) {
            modalContent.style.zIndex = '100000';
            modalContent.style.pointerEvents = 'auto';
        }

        console.log('✅ Modal z-index applied successfully');
    }, 150);
}

// Alternative approach - create modal dynamically if accessibility issues persist
function createDynamicExportModal() {
    // Remove existing modal if any
    const existingModal = document.getElementById('dynamicExportModal');
    if (existingModal) {
        existingModal.remove();
    }

    // Create modal HTML
    const modalHTML = `
        <div class="modal fade show" id="dynamicExportModal" style="display: block; z-index: 999999; position: fixed; background: rgba(0,0,0,0.5);">
            <div class="modal-dialog" style="z-index: 999999; margin-top: 50px;">
                <div class="modal-content" style="z-index: 999999;">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-file-pdf me-2"></i>Exporter Emploi du Temps
                        </h5>
                        <button type="button" class="btn-close" onclick="closeDynamicModal()"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Sélectionnez la filière et le semestre pour exporter l'emploi du temps correspondant.
                        </div>

                        <div class="form-floating mb-3">
                            <select class="form-select" id="dynamic_export_filiere_id" required>
                                <option value="">Choisir une filière...</option>
                                @foreach($filieres as $filiere)
                                    <option value="{{ $filiere->id }}">{{ $filiere->nom }}</option>
                                @endforeach
                            </select>
                            <label>Filière</label>
                        </div>

                        <div class="form-floating mb-3">
                            <select class="form-select" id="dynamic_export_semester" required>
                                <option value="">Choisir un semestre...</option>
                                <option value="S1">Semestre 1</option>
                                <option value="S2">Semestre 2</option>
                                <option value="S3">Semestre 3</option>
                                <option value="S4">Semestre 4</option>
                                <option value="S5">Semestre 5</option>
                                <option value="S6">Semestre 6</option>
                            </select>
                            <label>Semestre</label>
                        </div>

                        <div class="form-floating mb-3">
                            <select class="form-select" id="dynamic_export_year">
                                <option value="{{ date('Y') }}">{{ date('Y') }}-{{ date('Y') + 1 }}</option>
                                <option value="{{ date('Y') - 1 }}">{{ date('Y') - 1 }}-{{ date('Y') }}</option>
                                <option value="{{ date('Y') + 1 }}">{{ date('Y') + 1 }}-{{ date('Y') + 2 }}</option>
                            </select>
                            <label>Année Universitaire</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" onclick="closeDynamicModal()">Annuler</button>
                        <button type="button" class="btn btn-primary" onclick="confirmDynamicExport()">
                            <i class="fas fa-download me-2"></i>Exporter PDF
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;

    // Append to body
    document.body.insertAdjacentHTML('beforeend', modalHTML);

    // Pre-fill with current selections
    const currentFiliere = document.getElementById('filiere_select')?.value;
    const currentSemester = document.getElementById('semester_select')?.value;
    const currentYear = document.getElementById('year_select')?.value || new Date().getFullYear();

    if (currentFiliere) {
        document.getElementById('dynamic_export_filiere_id').value = currentFiliere;
    }

    if (currentSemester) {
        document.getElementById('dynamic_export_semester').value = currentSemester;
    }

    document.getElementById('dynamic_export_year').value = currentYear;

    console.log('🚀 Dynamic modal created and shown');
}

function closeDynamicModal() {
    const modal = document.getElementById('dynamicExportModal');
    if (modal) {
        modal.remove();
    }
}

function confirmDynamicExport() {
    const selectedFiliere = document.getElementById('dynamic_export_filiere_id').value;
    const selectedSemester = document.getElementById('dynamic_export_semester').value;
    const selectedYear = document.getElementById('dynamic_export_year').value;

    if (!selectedFiliere) {
        alert('Veuillez sélectionner une filière');
        return;
    }

    if (!selectedSemester) {
        alert('Veuillez sélectionner un semestre');
        return;
    }

    closeDynamicModal();

    // Export PDF
    window.location.href = `/coordonnateur/emploi-du-temps/export?filiere_id=${selectedFiliere}&semester=${selectedSemester}&year=${selectedYear}`;
}

// Export function - try normal modal first, fallback to dynamic
function exportSchedule() {
    // Try the normal modal first
    try {
        openExportModal();
    } catch (error) {
        console.log('Normal modal failed, using dynamic modal:', error);
        createDynamicExportModal();
    }
}

// Confirm export from modal
function confirmExport() {
    const selectedFiliere = document.getElementById('export_filiere_id').value;
    const selectedSemester = document.getElementById('export_semester').value;
    const selectedYear = document.getElementById('export_year').value;

    if (!selectedFiliere) {
        showNotification('Veuillez sélectionner une filière', 'error');
        return;
    }

    if (!selectedSemester) {
        showNotification('Veuillez sélectionner un semestre', 'error');
        return;
    }

    console.log('🔥 Exporting selected emploi du temps:', {
        filiere: selectedFiliere,
        semester: selectedSemester,
        year: selectedYear
    });

    // Show loading notification
    showNotification('Génération du PDF en cours...', 'info');

    // Close modal
    const modal = bootstrap.Modal.getInstance(document.getElementById('exportModal'));
    modal.hide();

    // Export PDF with selected parameters
    window.location.href = `/coordonnateur/emploi-du-temps/export?filiere_id=${selectedFiliere}&semester=${selectedSemester}&year=${selectedYear}`;
}

// Pre-fill export modal with current interface selections
document.addEventListener('DOMContentLoaded', function() {
    const exportModal = document.getElementById('exportModal');
    if (exportModal) {
        exportModal.addEventListener('show.bs.modal', function(event) {
            // Ensure modal appears above everything
            this.style.zIndex = '9999';

            // Try to pre-fill with current interface selections
            const currentFiliere = document.getElementById('filiere_select')?.value;
            const currentSemester = document.getElementById('semester_select')?.value;
            const currentYear = document.getElementById('year_select')?.value || new Date().getFullYear();

            if (currentFiliere) {
                document.getElementById('export_filiere_id').value = currentFiliere;
            }

            if (currentSemester) {
                document.getElementById('export_semester').value = currentSemester;
            }

            document.getElementById('export_year').value = currentYear;

            console.log('📋 Export modal pre-filled with:', {
                filiere: currentFiliere,
                semester: currentSemester,
                year: currentYear
            });
        });

        exportModal.addEventListener('shown.bs.modal', function() {
            // Ensure backdrop is properly positioned
            const backdrop = document.querySelector('.modal-backdrop');
            if (backdrop) {
                backdrop.style.zIndex = '9998';
            }

            console.log('✅ Export modal fully shown with proper z-index');
        });
    }
});

// handleFileUpload function removed - import not in allowed list
</script>
@endpush

@push('scripts')
<script>
// Filière and Semester Selection
let selectedFiliere = null;
let selectedSemester = null;

function selectFiliere(filiereName, filiereId) {
    selectedFiliere = { name: filiereName, id: filiereId };

    // Update active button
    document.querySelectorAll('.filiere-btn').forEach(btn => {
        btn.classList.remove('btn-primary');
        btn.classList.add('btn-outline-primary');
    });
    event.target.classList.remove('btn-outline-primary');
    event.target.classList.add('btn-primary');

    // Update semester buttons based on filière
    updateSemesterButtons(filiereName);

    // Filter UEs and schedule based on filière
    filterByFiliere(filiereId);

    console.log('Selected filière:', selectedFiliere);
}

function updateSemesterButtons(filiereName) {
    const semesterContainer = document.getElementById('semesterButtons');
    const filiereNumber = filiereName.slice(-1); // Get last character (1, 2, or 3)

    let semesters = [];
    if (filiereNumber === '1') {
        semesters = ['S1', 'S2'];
    } else if (filiereNumber === '2') {
        semesters = ['S3', 'S4'];
    } else if (filiereNumber === '3') {
        semesters = ['S5'];
    }

    // Clear existing buttons
    semesterContainer.innerHTML = '';

    // Add new semester buttons
    semesters.forEach(semester => {
        const button = document.createElement('button');
        button.type = 'button';
        button.className = 'btn btn-outline-success semester-btn';
        button.dataset.semester = semester;
        button.onclick = () => selectSemester(semester);
        button.innerHTML = semester;
        semesterContainer.appendChild(button);
    });

    // Reset selected semester
    selectedSemester = null;
    document.getElementById('selectedSemesterBadge').style.display = 'none';
}

function selectSemester(semester) {
    selectedSemester = semester;

    // Update active button
    document.querySelectorAll('.semester-btn').forEach(btn => {
        btn.classList.remove('btn-success');
        btn.classList.add('btn-outline-success');
    });
    event.target.classList.remove('btn-outline-success');
    event.target.classList.add('btn-success');

    // Update badge
    document.getElementById('selectedSemesterText').textContent = semester;
    document.getElementById('selectedSemesterBadge').style.display = 'inline-block';

    // Filter UEs and schedule based on semester
    filterBySemester(semester);

    console.log('Selected semester:', selectedSemester);
}

function filterByFiliere(filiereId) {
    // Filter UE items
    const ueItems = document.querySelectorAll('.ue-item');
    ueItems.forEach(item => {
        // Show all UEs for now - in real implementation, filter by filière
        item.style.display = 'block';
    });

    // Filter schedule slots
    const scheduleSlots = document.querySelectorAll('.schedule-slot');
    scheduleSlots.forEach(slot => {
        // Show all slots for now - in real implementation, filter by filière
        slot.style.display = 'block';
    });
}

function filterBySemester(semester) {
    // Filter UE items by semester
    const ueItems = document.querySelectorAll('.ue-item');
    ueItems.forEach(item => {
        const ueSemester = item.querySelector('.badge.bg-info')?.textContent;
        if (ueSemester === semester) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });

    // Filter schedule slots by semester (if they have semester data)
    const scheduleSlots = document.querySelectorAll('.schedule-slot');
    scheduleSlots.forEach(slot => {
        // For now, show all slots - in real implementation, filter by semester
        slot.style.display = 'block';
    });
}

// Sidebar Minimize Functionality - Only for Emploi du Temps
function toggleSidebarMinimize() {
    const sidebar = document.querySelector('.sidebar');
    const mainContent = document.getElementById('mainContent');
    const minimizeBtn = document.getElementById('sidebarMinimizeBtn');
    const minimizeIcon = minimizeBtn.querySelector('i');
    const minimizeText = minimizeBtn.querySelector('span');

    // Toggle sidebar minimized state
    sidebar.classList.toggle('minimized');
    mainContent.classList.toggle('sidebar-minimized');
    minimizeBtn.classList.toggle('minimized');

    // Update button text and icon
    if (sidebar.classList.contains('minimized')) {
        minimizeIcon.className = 'fas fa-expand-alt';
        minimizeText.textContent = 'Agrandir';
    } else {
        minimizeIcon.className = 'fas fa-compress-alt';
        minimizeText.textContent = 'Minimiser';
    }

    // Save state for this session only (emploi du temps specific)
    sessionStorage.setItem('emploiDuTempsSidebarMinimized', sidebar.classList.contains('minimized'));
}

// Initialize page on load
document.addEventListener('DOMContentLoaded', function() {
    // Restore sidebar state for emploi du temps only
    const isMinimized = sessionStorage.getItem('emploiDuTempsSidebarMinimized') === 'true';
    if (isMinimized) {
        toggleSidebarMinimize();
    }

    // Load saved schedule state
    loadScheduleState();

    // Initialize drag and drop
    initializeDragAndDrop();

    // Auto-select first filiere if none selected and load default semester
    autoSelectFirstFiliere();

    console.log('🎉 SEMESTER-AWARE SCHEDULE SYSTEM INITIALIZED:');
    console.log('📅 Semester selection loads saved UEs for that specific semester');
    console.log('🗑️ Orange buttons (Existing UEs): Vider le créneau - UE disappears');
    console.log('❌ Red buttons (New UEs): Return to carousel - UE can be reused');
});

// Enhanced Drag and Drop functionality
let draggedElement = null;

document.addEventListener('DOMContentLoaded', function() {
    // Make UE items draggable
    const ueItems = document.querySelectorAll('.ue-item');
    ueItems.forEach(item => {
        item.addEventListener('dragstart', function(e) {
            draggedElement = this;
            this.style.opacity = '0.5';
            e.dataTransfer.effectAllowed = 'move';
            e.dataTransfer.setData('text/html', this.outerHTML);
        });

        item.addEventListener('dragend', function(e) {
            this.style.opacity = '1';
            draggedElement = null;
        });
    });

    // Make schedule slots droppable
    const scheduleSlots = document.querySelectorAll('.schedule-slot');
    scheduleSlots.forEach(slot => {
        slot.addEventListener('dragover', function(e) {
            e.preventDefault();
            e.dataTransfer.dropEffect = 'move';
            this.style.background = 'linear-gradient(135deg, rgba(124, 58, 237, 0.3), rgba(139, 92, 246, 0.3))';
        });

        slot.addEventListener('dragleave', function(e) {
            this.style.background = '';
        });

        slot.addEventListener('drop', function(e) {
            e.preventDefault();
            this.style.background = '';

            if (draggedElement) {
                // Add UE to schedule slot
                const ueData = {
                    id: draggedElement.dataset.ueId,
                    name: draggedElement.querySelector('.ue-name').textContent,
                    type: draggedElement.dataset.type,
                    teacher: draggedElement.dataset.teacher || 'Non assigné'
                };

                // Create schedule item
                const scheduleItem = document.createElement('div');
                scheduleItem.className = `schedule-item ${ueData.type.toLowerCase()}`;
                scheduleItem.innerHTML = `
                    <div class="item-header">
                        <span class="item-name">${ueData.name}</span>
                        <button class="remove-btn" onclick="removeScheduleItem(this)">×</button>
                    </div>
                    <div class="item-type">${ueData.type}</div>
                    <div class="item-teacher">${ueData.teacher}</div>
                `;

                // Add to slot
                this.appendChild(scheduleItem);

                // Remove from UE list
                draggedElement.remove();

                console.log('UE ajoutée à l\'emploi du temps:', ueData);
            }
        });
    });
});

// Remove schedule item
function removeScheduleItem(button) {
    const scheduleItem = button.closest('.schedule-item');
    const slot = scheduleItem.parentElement;

    // Get UE data
    const ueName = scheduleItem.querySelector('.item-name').textContent;
    const ueType = scheduleItem.querySelector('.item-type').textContent;

    // Remove from schedule
    scheduleItem.remove();

    // Add back to UE list (simplified - in real app, you'd restore from database)
    console.log('UE retirée de l\'emploi du temps:', ueName, ueType);
}

// Save emploi du temps
function saveEmploiDuTemps() {
    const scheduleData = {};
    const scheduleSlots = document.querySelectorAll('.schedule-slot');

    scheduleSlots.forEach(slot => {
        const day = slot.dataset.day;
        const time = slot.dataset.time;
        const items = slot.querySelectorAll('.schedule-item');

        if (items.length > 0) {
            scheduleData[`${day}-${time}`] = Array.from(items).map(item => ({
                name: item.querySelector('.item-name').textContent,
                type: item.querySelector('.item-type').textContent,
                teacher: item.querySelector('.item-teacher').textContent
            }));
        }
    });

    console.log('Emploi du temps sauvegardé:', scheduleData);

    // Show success message
    const alert = document.createElement('div');
    alert.className = 'alert alert-success position-fixed';
    alert.style.cssText = 'top: 20px; right: 20px; z-index: 9999;';
    alert.innerHTML = '<i class="fas fa-check-circle me-2"></i>Emploi du temps sauvegardé avec succès!';
    document.body.appendChild(alert);

    setTimeout(() => {
        alert.remove();
    }, 3000);
}

// Group Selection Modal Functions
function showGroupSelectionModal(cell, ue, day, timeSlot) {
    // Get available groups for this UE
    const availableGroups = getAvailableGroups(ue.id, ue.type);

    if (availableGroups.length === 0) {
        showNotification(`Tous les groupes ${ue.type} de ${ue.code} sont déjà placés.`, 'error');
        return;
    }

    // Create overlay
    const overlay = document.createElement('div');
    overlay.className = 'group-selection-overlay';
    overlay.onclick = () => closeGroupSelectionModal();

    // Generate group options HTML
    let groupOptionsHtml = '';
    const badgeColors = ['bg-primary', 'bg-success', 'bg-warning', 'bg-info', 'bg-secondary'];

    availableGroups.forEach((groupNum, index) => {
        const badgeColor = badgeColors[index % badgeColors.length];
        groupOptionsHtml += `
            <div class="group-option" onclick="selectGroup(${groupNum})">
                <span><i class="fas fa-users me-2"></i>Groupe ${groupNum}</span>
                <span class="badge ${badgeColor}">G${groupNum}</span>
            </div>
        `;
    });

    // Create modal
    const modal = document.createElement('div');
    modal.className = 'group-selection-modal';
    modal.innerHTML = `
        <h4 style="color: #059669; margin-bottom: 1.5rem;">
            <i class="fas fa-users me-2"></i>Sélectionner le Groupe
        </h4>
        <p style="color: #6b7280; margin-bottom: 1.5rem;">
            <strong>${ue.code}</strong> - ${ue.name}<br>
            <span style="font-size: 0.9rem;">Type: ${ue.type}</span>
        </p>

        <div class="group-options">
            ${groupOptionsHtml}
        </div>

        <div style="margin-top: 1.5rem; text-align: center;">
            <button class="btn btn-secondary me-2" onclick="closeGroupSelectionModal()">
                <i class="fas fa-times me-1"></i>Annuler
            </button>
            <button class="btn btn-success" onclick="confirmGroupSelection()">
                <i class="fas fa-check me-1"></i>Confirmer
            </button>
        </div>
    `;

    // Store modal data
    window.groupModalData = { cell, ue, day, timeSlot };
    window.selectedGroupNumber = null;

    // Add to page
    document.body.appendChild(overlay);
    document.body.appendChild(modal);
}

// Get available groups for a UE type (DOM-based)
function getAvailableGroups(ueId, type) {
    const maxGroups = getMaxGroupsForUE(ueId, type);

    // Check DOM for existing groups
    const existingSlots = document.querySelectorAll(`.schedule-slot[data-ue-id="${ueId}"][data-type="${type}"]`);
    const usedGroups = Array.from(existingSlots).map(slot => parseInt(slot.dataset.group)).filter(g => !isNaN(g));

    const availableGroups = [];
    for (let i = 1; i <= maxGroups; i++) {
        if (!usedGroups.includes(i)) {
            availableGroups.push(i);
        }
    }

    return availableGroups;
}

function selectGroup(groupNumber) {
    // Remove previous selection
    document.querySelectorAll('.group-option').forEach(option => {
        option.classList.remove('selected');
    });

    // Add selection to clicked option
    event.target.closest('.group-option').classList.add('selected');
    window.selectedGroupNumber = groupNumber;
}

function confirmGroupSelection() {
    if (!window.selectedGroupNumber) {
        alert('Veuillez sélectionner un groupe.');
        return;
    }

    const { cell, ue, day, timeSlot } = window.groupModalData;

    // Double-check if this specific group can be placed
    if (!canPlaceUE(ue.id, ue.type, window.selectedGroupNumber)) {
        showNotification(`Le groupe ${window.selectedGroupNumber} de ${ue.code} (${ue.type}) est déjà placé dans l'emploi du temps.`, 'error');
        closeGroupSelectionModal();
        return;
    }

    // Create schedule slot with group (NEW UE - red X button)
    createScheduleSlot(cell, ue, day, timeSlot, window.selectedGroupNumber, false);

    // Close modal
    closeGroupSelectionModal();
}

function closeGroupSelectionModal() {
    const overlay = document.querySelector('.group-selection-overlay');
    const modal = document.querySelector('.group-selection-modal');

    if (overlay) overlay.remove();
    if (modal) modal.remove();

    // Clean up
    window.groupModalData = null;
    window.selectedGroupNumber = null;
}

// UE Carousel Navigation Functions
let currentUEIndex = 0;
let totalUEs = 0;

// Initialize carousel and load existing schedules
document.addEventListener('DOMContentLoaded', function() {
    initializeUECarousel();
    loadExistingSchedules();
});

function initializeUECarousel() {
    const ueCards = document.querySelectorAll('.ue-card-container');
    totalUEs = ueCards.length;
    currentUEIndex = 0;

    updateCarouselDisplay();
    updateNavigationButtons();
}

function nextUE() {
    if (currentUEIndex < totalUEs - 1) {
        currentUEIndex++;
        updateCarouselDisplay();
        updateNavigationButtons();
    }
}

function previousUE() {
    if (currentUEIndex > 0) {
        currentUEIndex--;
        updateCarouselDisplay();
        updateNavigationButtons();
    }
}

function goToUE(index) {
    if (index >= 0 && index < totalUEs) {
        currentUEIndex = index;
        updateCarouselDisplay();
        updateNavigationButtons();
    }
}

function updateCarouselDisplay() {
    // Hide all cards
    const ueCards = document.querySelectorAll('.ue-card-container');
    ueCards.forEach((card, index) => {
        card.classList.remove('active');
        if (index === currentUEIndex) {
            card.classList.add('active');
        }
    });

    // Update counter
    const counter = document.getElementById('ue-counter');
    if (counter && totalUEs > 0) {
        counter.textContent = `${currentUEIndex + 1} / ${totalUEs}`;
    }

    // Update indicators
    const indicators = document.querySelectorAll('.carousel-dot');
    indicators.forEach((dot, index) => {
        dot.classList.remove('active');
        if (index === currentUEIndex) {
            dot.classList.add('active');
        }
    });
}

function updateNavigationButtons() {
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');

    if (prevBtn) {
        prevBtn.disabled = currentUEIndex === 0;
    }

    if (nextBtn) {
        nextBtn.disabled = currentUEIndex === totalUEs - 1;
    }
}

// Load existing schedules from database and initialize tracking
function loadExistingSchedules() {
    console.log('Loading existing schedules...');

    // The schedules are already loaded in the PHP template
    // Check if there are any schedule slots already displayed
    const existingSlots = document.querySelectorAll('.schedule-slot');
    console.log('Found existing schedule slots:', existingSlots.length);

    if (existingSlots.length > 0) {
        console.log('Schedules already loaded in template');

        // Initialize tracking from existing schedules
        initializeTrackingFromExistingSchedules();
        return;
    }

    console.log('No schedules found in template');
}

// Initialize tracking from existing schedules on page load
function initializeTrackingFromExistingSchedules() {
    const existingSlots = document.querySelectorAll('.schedule-slot');

    existingSlots.forEach(slot => {
        const ueId = slot.dataset.ueId;
        const type = slot.dataset.type;
        const group = slot.dataset.group;

        if (ueId && type) {
            // Mark as placed in tracking
            markUEAsPlaced(ueId, type, group || null);

            // Note: Don't hide from carousel here - backend already filters them out
            // The backend getEmploiDuTempsData method now filters UEs that are already scheduled
        }
    });

    console.log('Initialized tracking from existing schedules:', placedUEs);
}

// Load emploi du temps data via AJAX
async function loadEmploiDuTempsData(filiereId, semester = null) {
    console.log('Loading emploi du temps data:', { filiereId, semester });

    // Show loading state
    showLoadingState();

    try {
        const url = new URL('/coordonnateur/api/emploi-du-temps-data', window.location.origin);
        url.searchParams.set('filiere_id', filiereId);
        if (semester) {
            url.searchParams.set('semester', semester);
        }

        const response = await fetch(url, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();
        console.log('🔄 API Response received:', data);
        console.log('📊 Schedules in response:', data.schedules?.length || 0);
        console.log('📚 UEs in response:', data.ues?.length || 0);

        if (data.success) {
            // Update UI with loaded data
            updateUIWithData(data);
            showNotification(`Données chargées: ${data.schedules?.length || 0} horaires, ${data.ues?.length || 0} UEs`, 'success');
        } else {
            throw new Error(data.message || 'Failed to load data');
        }

    } catch (error) {
        console.error('Error loading emploi du temps data:', error);
        showNotification('Erreur lors du chargement des données: ' + error.message, 'error');
    } finally {
        hideLoadingState();
    }
}

// Keyboard navigation
document.addEventListener('keydown', function(event) {
    if (event.target.tagName.toLowerCase() === 'input' || event.target.tagName.toLowerCase() === 'textarea') {
        return; // Don't interfere with form inputs
    }

    if (event.key === 'ArrowLeft') {
        event.preventDefault();
        previousUE();
    } else if (event.key === 'ArrowRight') {
        event.preventDefault();
        nextUE();
    }
});

// Schedule Management Functions
async function checkScheduleConflict(ueId, day, timeSlot, type) {
    try {
        console.log('Checking conflict for:', { ueId, day, timeSlot, type });

        const response = await fetch('/api/schedule/check-conflict', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                ue_id: ueId,
                jour_semaine: day,
                time_slot: timeSlot,
                type_seance: type,
                annee_universitaire: '2024-2025'
            })
        });

        const data = await response.json();
        console.log('Conflict check result:', data);
        return data;
    } catch (error) {
        console.error('Error checking conflict:', error);
        return { conflict: false };
    }
}

// REMOVED: No automatic database saving - only save when "Save" button is clicked

// REMOVED: No automatic database removal - only save changes when "Save" button is clicked

// REMOVED: This function is replaced by removeScheduleAndReturnUE

// Filiere and Semester Selection Functions
function selectFiliere(filiereName, filiereId) {
    console.log('Selecting filiere:', filiereName, filiereId);

    // Update button states
    document.querySelectorAll('.filiere-btn').forEach(btn => {
        btn.classList.remove('btn-primary');
        btn.classList.add('btn-outline-primary');
    });

    // Activate selected button
    const selectedBtn = document.querySelector(`[data-filiere-id="${filiereId}"]`);
    if (selectedBtn) {
        selectedBtn.classList.remove('btn-outline-primary');
        selectedBtn.classList.add('btn-primary');
    }

    // Update semester buttons based on filiere
    updateSemesterButtons(filiereName);

    // Store selected filiere globally
    selectedFiliere = { name: filiereName, id: filiereId };
    selectedSemester = null; // Clear semester when changing filiere

    // Load data via AJAX instead of page reload
    loadEmploiDuTempsData(filiereId, null);
}

function selectSemester(semester) {
    console.log('Selecting semester:', semester);

    // Get current filiere_id from URL or selected button
    const selectedFiliereBtn = document.querySelector('.filiere-btn.btn-primary');
    if (!selectedFiliereBtn) {
        console.error('No filiere selected');
        return;
    }

    const filiereId = selectedFiliereBtn.getAttribute('data-filiere-id');

    // Update button states
    document.querySelectorAll('.semester-btn').forEach(btn => {
        btn.classList.remove('btn-success');
        btn.classList.add('btn-outline-success');
    });

    // Activate selected button
    const selectedBtn = document.querySelector(`[data-semester="${semester}"]`);
    if (selectedBtn) {
        selectedBtn.classList.remove('btn-outline-success');
        selectedBtn.classList.add('btn-success');
    }

    // Store selected semester globally
    selectedSemester = semester;

    // Load data via AJAX instead of page reload
    loadEmploiDuTempsData(filiereId, semester);
}

function updateSemesterButtons(filiereName) {
    const semesterContainer = document.getElementById('semesterButtons');
    if (!semesterContainer) return;

    // Get filiere number (last character)
    const filiereNumber = filiereName.slice(-1);
    let semesters = [];
    let defaultSemester = null;

    if (filiereNumber === '1') {
        semesters = ['S1', 'S2'];
        defaultSemester = 'S1';
    } else if (filiereNumber === '2') {
        semesters = ['S3', 'S4'];
        defaultSemester = 'S3';
    } else if (filiereNumber === '3') {
        semesters = ['S5'];
        defaultSemester = 'S5';
    }

    // Clear existing buttons
    semesterContainer.innerHTML = '';

    // Add new semester buttons
    semesters.forEach(semester => {
        const button = document.createElement('button');
        button.type = 'button';
        button.className = 'btn btn-outline-success semester-btn';
        button.setAttribute('data-semester', semester);
        button.onclick = () => selectSemester(semester);
        button.textContent = semester;
        semesterContainer.appendChild(button);
    });

    // Auto-select default semester
    if (defaultSemester) {
        setTimeout(() => {
            autoSelectDefaultSemester(defaultSemester);
        }, 100);
    }
}

function clearSemesterFilter() {
    console.log('Clearing semester filter');

    // Get current filiere_id from URL or selected button
    const selectedFiliereBtn = document.querySelector('.filiere-btn.btn-primary');
    if (!selectedFiliereBtn) {
        console.error('No filiere selected');
        return;
    }

    const filiereId = selectedFiliereBtn.getAttribute('data-filiere-id');

    // Clear semester button states
    document.querySelectorAll('.semester-btn').forEach(btn => {
        btn.classList.remove('btn-success');
        btn.classList.add('btn-outline-success');
    });

    // Clear selected semester globally
    selectedSemester = null;

    // Load data via AJAX without semester filter
    loadEmploiDuTempsData(filiereId, null);
}

function filterUEsBySemester(semester) {
    // This function can be used to filter UEs based on selected semester
    console.log('Filtering UEs by semester:', semester);
    // Implementation can be added here if needed
}

// Show loading state
function showLoadingState() {
    // Show loading overlay on UE container
    const ueContainer = document.querySelector('.ue-carousel-content');
    if (ueContainer) {
        ueContainer.innerHTML = `
            <div class="text-center py-5">
                <div class="spinner-border text-success" role="status">
                    <span class="visually-hidden">Chargement...</span>
                </div>
                <p class="mt-3 text-muted">Chargement des UEs...</p>
            </div>
        `;
    }

    // Show loading overlay on schedule table
    const scheduleTable = document.querySelector('.schedule-table tbody');
    if (scheduleTable) {
        scheduleTable.innerHTML = `
            <tr>
                <td colspan="7" class="text-center py-5">
                    <div class="spinner-border text-success" role="status">
                        <span class="visually-hidden">Chargement...</span>
                    </div>
                    <p class="mt-3 text-muted">Chargement de l'emploi du temps...</p>
                </td>
            </tr>
        `;
    }
}

// Hide loading state
function hideLoadingState() {
    // Loading states will be replaced by updateUIWithData
}

// Update UI with loaded data
function updateUIWithData(data) {
    console.log('🎯 Updating UI with data for selected semester...');
    console.log('📊 Total schedules to display:', data.schedules?.length || 0);
    console.log('📚 Total UEs to display:', data.ues?.length || 0);

    // Debug logging
    if (data.debug_info) {
        console.log('🔍 Debug info:', data.debug_info);
    }

    console.log('📋 UEs received:', data.ues);
    console.log('⏰ Schedules received:', data.schedules);

    // Update UE carousel
    updateUECarousel(data.ues || []);

    // Update schedule table
    updateScheduleTable(data.schedules || []);

    // Update statistics
    updateStatistics(data.stats || {});

    // Update semester buttons if available semesters changed
    if (data.availableSemesters) {
        updateAvailableSemesters(data.availableSemesters, data.defaultSemester, data.selectedSemester);
    }

    // Auto-select default semester if none selected
    if (data.defaultSemester && data.selectedSemester === data.defaultSemester) {
        autoSelectDefaultSemester(data.defaultSemester);
    }
}

// Update UE carousel with new data
function updateUECarousel(ues) {
    const ueContainer = document.querySelector('.ue-carousel-content');
    const ueCounter = document.getElementById('ue-counter');

    if (ues.length === 0) {
        ueContainer.innerHTML = `
            <div class="ue-card-container active">
                <div class="text-center py-4">
                    <i class="fas fa-book fa-3x text-info mb-3"></i>
                    <h5 class="text-info">Aucune UE disponible</h5>
                    <p class="text-muted">Aucune unité d'enseignement trouvée pour cette sélection</p>
                </div>
            </div>
        `;
        if (ueCounter) {
            ueCounter.innerHTML = '<span class="text-warning"><i class="fas fa-exclamation-triangle me-1"></i>Aucune UE</span>';
        }
        return;
    }

    // Generate UE cards HTML
    let ueCardsHtml = '';
    ues.forEach((ue, index) => {
        ueCardsHtml += `
            <div class="ue-card-container ${index === 0 ? 'active' : ''}" data-ue-index="${index}">
                <div class="ue-header">
                    <strong>${ue.code}</strong>
                    <span class="ue-name">${ue.nom}</span>
                    <span class="badge bg-info ms-2">${ue.semestre}</span>
                </div>
                <div class="ue-type-options">
        `;

        // Show only available types from backend (FILTERED)
        const availableTypes = ue.available_types || [];

        console.log(`UE ${ue.code} - Available types:`, availableTypes, 'Placed info:', ue.placed_info);

        // Show CM only if available
        if (ue.heures_cm > 0 && availableTypes.includes('CM')) {
            ueCardsHtml += `
                <div class="ue-type-item cm"
                     draggable="true"
                     data-ue-id="${ue.id}"
                     data-ue-code="${ue.code}"
                     data-ue-name="${ue.nom}"
                     data-type="CM"
                     ondragstart="handleUEDragStart(event)">
                    <span class="type-label">CM</span>
                    <span class="type-hours">${ue.heures_cm}h</span>
                </div>
            `;
        }

        // Show TD only if available
        if (ue.heures_td > 0 && availableTypes.includes('TD')) {
            ueCardsHtml += `
                <div class="ue-type-item td"
                     draggable="true"
                     data-ue-id="${ue.id}"
                     data-ue-code="${ue.code}"
                     data-ue-name="${ue.nom}"
                     data-type="TD"
                     ondragstart="handleUEDragStart(event)">
                    <span class="type-label">TD</span>
                    <span class="type-hours">${ue.heures_td}h</span>
                </div>
            `;
        }

        // Show TP only if available
        if (ue.heures_tp > 0 && availableTypes.includes('TP')) {
            ueCardsHtml += `
                <div class="ue-type-item tp"
                     draggable="true"
                     data-ue-id="${ue.id}"
                     data-ue-code="${ue.code}"
                     data-ue-name="${ue.nom}"
                     data-type="TP"
                     ondragstart="handleUEDragStart(event)">
                    <span class="type-label">TP</span>
                    <span class="type-hours">${ue.heures_tp}h</span>
                </div>
            `;
        }

        ueCardsHtml += `
                </div>
            </div>
        `;
    });

    ueContainer.innerHTML = ueCardsHtml;

    // Update counter
    if (ueCounter) {
        ueCounter.innerHTML = `<span class="text-success"><i class="fas fa-check-circle me-1"></i>1 / ${ues.length}</span>`;
    }

    // Update carousel navigation
    updateCarouselNavigation(ues.length);

    // Reset carousel state
    currentUEIndex = 0;
    totalUEs = ues.length;
    updateCarouselDisplay();

    // IMPORTANT: Re-initialize drag and drop for new UE items
    setTimeout(() => {
        initializeDragAndDrop();
        console.log('Drag and drop re-initialized for UE carousel');
    }, 200);
}

// Update schedule table - SHOW EXISTING SCHEDULES FOR SELECTED SEMESTER
function updateScheduleTable(schedules) {
    const timeSlots = ['08:30-10:30', '10:30-12:30', '14:30-16:30', '16:30-18:30'];
    const days = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];

    const scheduleTableBody = document.querySelector('.schedule-table tbody');

    console.log('🔄 Creating schedule table with existing schedules for selected semester...');
    console.log('📊 Total schedules to display:', schedules.length);
    console.log('📋 Schedule details:', schedules);

    // Log each schedule for debugging
    schedules.forEach((schedule, index) => {
        console.log(`📅 Schedule ${index + 1}:`, {
            ue: schedule.unite_enseignement?.code || 'Unknown',
            day: schedule.jour_semaine,
            time: `${schedule.heure_debut}-${schedule.heure_fin}`,
            type: schedule.type_seance,
            group: schedule.group_number,
            semester: schedule.semestre
        });
    });

    // First, create empty table structure
    let tableHtml = '';
    timeSlots.forEach(timeSlot => {
        tableHtml += `<tr><td class="time-slot">${timeSlot}</td>`;
        days.forEach(day => {
            // Check if there are existing schedules for this day/time slot
            const daySchedules = schedules.filter(schedule => {
                const scheduleTimeSlot = `${schedule.heure_debut.substring(0,5)}-${schedule.heure_fin.substring(0,5)}`;
                return schedule.jour_semaine === day && scheduleTimeSlot === timeSlot;
            });

            tableHtml += `
                <td class="schedule-cell drop-zone"
                    data-day="${day}"
                    data-time-slot="${timeSlot}"
                    ondrop="drop(event)"
                    ondragover="allowDrop(event)">
            `;

            if (daySchedules.length > 0) {
                // Show existing schedules with ORANGE BUTTON (existing UEs)
                daySchedules.forEach(schedule => {
                    const abbreviation = schedule.unite_enseignement.nom.split(' ')
                        .map(word => word.charAt(0).toUpperCase())
                        .join('')
                        .substring(0, 4);

                    const groupText = schedule.group_number ? `-G${schedule.group_number}` : '';

                    tableHtml += `
                        <div class="schedule-slot ${schedule.type_seance.toLowerCase()}"
                             data-ue-id="${schedule.ue_id}"
                             data-day="${day}"
                             data-time-slot="${timeSlot}"
                             data-type="${schedule.type_seance}"
                             data-group="${schedule.group_number || ''}"
                             data-ue-code="${schedule.unite_enseignement.code}"
                             data-ue-name="${schedule.unite_enseignement.nom}"
                             data-schedule-id="${schedule.id}"
                             data-existing="true">
                            <div class="slot-type">${schedule.type_seance}${groupText}</div>
                            <div class="slot-code">${schedule.unite_enseignement.code}</div>
                            <div class="slot-abbreviation">${abbreviation}</div>
                            <div class="slot-teacher">${schedule.user ? schedule.user.name : 'Non assigné'}</div>

                            <!-- ONLY ORANGE BUTTON FOR EXISTING UEs -->
                            <div class="slot-buttons">
                                <button class="btn btn-sm btn-warning empty-slot-btn" title="Vider le créneau" onclick="emptySlot(this)">
                                    <i class="fas fa-eraser"></i>
                                </button>
                            </div>
                        </div>
                    `;
                });
            } else {
                // Show placeholder for empty slots
                tableHtml += `
                    <div class="text-center py-2 drop-placeholder">
                        <small class="text-muted">Glissez une UE ici</small>
                    </div>
                `;
            }

            tableHtml += '</td>';
        });
        tableHtml += '</tr>';
    });

    scheduleTableBody.innerHTML = tableHtml;

    console.log(`✅ Schedule table updated with ${schedules.length} existing schedules for selected semester`);
}

// REMOVED: autoDropExistingSchedules function - no longer needed
// Existing UEs from database will NOT be shown in the table

// Attach event listeners to all X buttons (ALL UEs treated the same)
function attachRemoveButtonListeners() {
    // Handle X buttons for ALL UEs (existing and new - NO DISTINCTION)
    const removeButtons = document.querySelectorAll('.remove-schedule');
    console.log('Attaching event listeners to', removeButtons.length, 'X buttons (ALL UEs)');

    removeButtons.forEach(button => {
        // Remove existing listeners to avoid duplicates
        button.replaceWith(button.cloneNode(true));
    });

    // Re-select after cloning and add listeners
    const freshRemoveButtons = document.querySelectorAll('.remove-schedule');
    freshRemoveButtons.forEach(button => {
        button.addEventListener('click', function() {
            console.log('X button clicked (return to carousel)');
            removeScheduleAndReturnUE(this);
        });
    });

    // REMOVED: Y button handling - now all UEs use same X button

    console.log('Event listeners attached to all buttons');
}

// Update statistics
function updateStatistics(stats) {
    // Update stats bar if it exists
    const statsElements = {
        'total_creneaux': stats.total_creneaux || 0,
        'cours_cm': stats.cours_cm || 0,
        'seances_td': stats.seances_td || 0,
        'seances_tp': stats.seances_tp || 0
    };

    Object.keys(statsElements).forEach(key => {
        const element = document.querySelector(`.stat-number[data-stat="${key}"]`);
        if (element) {
            element.textContent = statsElements[key];
        }
    });
}

// Update carousel navigation
function updateCarouselNavigation(totalUEs) {
    const navigationContainer = document.querySelector('.ue-carousel-navigation');
    if (!navigationContainer) return;

    if (totalUEs <= 1) {
        navigationContainer.style.display = 'none';
        return;
    }

    navigationContainer.style.display = 'flex';

    // Update indicators
    const indicatorsContainer = document.getElementById('carousel-indicators');
    if (indicatorsContainer) {
        let indicatorsHtml = '';
        for (let i = 0; i < totalUEs; i++) {
            indicatorsHtml += `
                <div class="carousel-dot ${i === 0 ? 'active' : ''}"
                     onclick="goToUE(${i})"
                     data-index="${i}"></div>
            `;
        }
        indicatorsContainer.innerHTML = indicatorsHtml;
    }
}

// Auto-select default semester
function autoSelectDefaultSemester(defaultSemester) {
    console.log('Auto-selecting default semester:', defaultSemester);

    // Update button states
    document.querySelectorAll('.semester-btn').forEach(btn => {
        btn.classList.remove('btn-success');
        btn.classList.add('btn-outline-success');
    });

    // Activate default semester button
    const defaultBtn = document.querySelector(`[data-semester="${defaultSemester}"]`);
    if (defaultBtn) {
        defaultBtn.classList.remove('btn-outline-success');
        defaultBtn.classList.add('btn-success');
    }

    // Update global variable
    selectedSemester = defaultSemester;

    // Update badge
    const semesterBadge = document.getElementById('selectedSemesterBadge');
    const semesterText = document.getElementById('selectedSemesterText');
    if (semesterBadge && semesterText) {
        semesterText.textContent = defaultSemester;
        semesterBadge.style.display = 'inline-block';
    }
}

// Update available semesters with default selection
function updateAvailableSemesters(availableSemesters, defaultSemester, selectedSemester) {
    const semesterContainer = document.getElementById('semesterButtons');
    if (!semesterContainer) return;

    // Clear existing buttons
    semesterContainer.innerHTML = '';

    // Add new semester buttons
    availableSemesters.forEach(semester => {
        const button = document.createElement('button');
        button.type = 'button';
        button.className = 'btn btn-outline-success semester-btn';
        button.setAttribute('data-semester', semester);
        button.onclick = () => selectSemester(semester);
        button.textContent = semester;

        // Auto-select if this is the selected semester
        if (semester === selectedSemester) {
            button.classList.remove('btn-outline-success');
            button.classList.add('btn-success');
        }

        semesterContainer.appendChild(button);
    });

    // Update global variable
    selectedSemester = selectedSemester || defaultSemester;

    // Update badge
    if (selectedSemester) {
        const semesterBadge = document.getElementById('selectedSemesterBadge');
        const semesterText = document.getElementById('selectedSemesterText');
        if (semesterBadge && semesterText) {
            semesterText.textContent = selectedSemester;
            semesterBadge.style.display = 'inline-block';
        }
    }
}

// Auto-select first filiere on page load
function autoSelectFirstFiliere() {
    // Check if there's already a selected filiere
    const selectedFiliere = document.querySelector('.filiere-btn.btn-primary');
    if (selectedFiliere) {
        console.log('Filiere already selected');
        return;
    }

    // Get first filiere button
    const firstFiliereBtn = document.querySelector('.filiere-btn');
    if (firstFiliereBtn) {
        const filiereName = firstFiliereBtn.getAttribute('data-filiere');
        const filiereId = firstFiliereBtn.getAttribute('data-filiere-id');

        console.log('Auto-selecting first filiere:', filiereName, filiereId);

        // Simulate click on first filiere
        selectFiliere(filiereName, filiereId);
    }
}

// Notification system
function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `alert alert-${type === 'error' ? 'danger' : type === 'success' ? 'success' : 'info'} alert-dismissible fade show position-fixed`;
    notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    notification.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

    // Add to page
    document.body.appendChild(notification);

    // Auto remove after 5 seconds
    setTimeout(() => {
        if (notification.parentElement) {
            notification.remove();
        }
    }, 5000);
}

// REMOVED: Test function no longer needed since we don't auto-drop existing schedules
</script>
@endpush