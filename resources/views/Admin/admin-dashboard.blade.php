<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-blue: #2C5AA0;
        }

        /* Disable Bootstrap collapse transitions */
        .collapsing {
            -webkit-transition: none;
            transition: none;
            display: none;
        }

        .topbar {
            background-color: var(--primary-blue);
            padding: 15px 0;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .topbar-left, .topbar-right {
            font-size: 24px;
            font-weight: bold;
            color: white;
        }

        .topbar-logo {
            height: 50px;
            width: auto;
        }

        .btn-custom {
            border: none;
            border-radius: 10px;
            padding: 20px;
            font-size: 18px;
            font-weight: bold;
            transition: all 0.3s;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
            margin-bottom: 10px;
        }

        .btn-custom:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
        }

        .btn-custom::before {
            content: '';
            position: absolute;
            top: -10px;
            right: -10px;
            width: 30px;
            height: 30px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
        }

        .btn-primary-custom {
            background: linear-gradient(135deg, #2C5AA0, #4a90e2);
            color: white;
        }

        .btn-success-custom {
            background: linear-gradient(135deg, #28a745, #5cb85c);
            color: white;
        }

        .btn-warning-custom {
            background: linear-gradient(135deg, #ffc107, #ffab00);
            color: white;
        }

        .content-section {
            background: #f8f9fa;
            padding: 30px 0;
        }

        .section-title-btn {
            background-color: var(--primary-blue);
            color: white;
            padding: 15px 20px;
            border-radius: 8px;
            border: none;
            font-size: 18px;
            margin: 5px 0;
            width: 100%;
            text-align: left;
            position: relative;
            transition: background-color 0.2s;
        }

        .section-title-btn:hover {
            background-color: #1f4175;
        }

        .section-title-btn i {
            margin-right: 10px;
        }

        .section-content {
            padding: 15px;
            background: white;
            border-radius: 0 0 8px 8px;
            margin-bottom: 15px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            border: 1px solid #dee2e6;
            border-top: none;
        }

        .section-container {
            margin-bottom: 10px;
        }

        /* Make collapse instant */
        .collapse {
            transition: none;
        }
    </style>
</head>
<body>
<!-- Topbar -->
<div class="topbar">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-4">
                <div class="topbar-left">
                    <i class="fas fa-school me-2"></i>School System
                </div>
            </div>
            <div class="col-md-4 text-center">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="topbar-logo">
            </div>
            <div class="col-md-4 text-end">
                <div class="topbar-right">
                    <i class="fas fa-user-shield me-2"></i>Admin
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Navigation Bar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.users.index') }}">
                        <i class="fas fa-users fa-fw"></i>
                        <span>Manage Users</span>
                    </a>
                </li>
            </ul>
            <div class="d-flex">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-sign-out-alt me-1"></i> Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>

<!-- Content Section -->
<div class="content-section">
    <div class="container">
        <h2 class="mb-4">Admin Control Panel</h2>

        <!-- Vertical Sections Layout -->
        <div class="row">
            <div class="col-md-12">
                <!-- User Management Section -->
                <div class="section-container">
                    <button class="section-title-btn" type="button" data-bs-toggle="collapse" data-bs-target="#userManagement">
                        <i class="fas fa-users"></i> User Management
                    </button>
                    <div class="collapse section-content" id="userManagement">
                        <div class="row g-4">
                            <div class="col-md-4">
                                <a href="{{ route('admin.users.index') }}" class="btn btn-custom btn-primary-custom w-100">
                                    <i class="fas fa-user-plus fa-2x mb-3"></i>
                                    <h3>Manage Users</h3>
                                    <p>Create, edit and delete users</p>
                                </a>
                            </div>
                            <div class="col-md-4">
                                <a href="#" class="btn btn-custom btn-primary-custom w-100">
                                    <i class="fas fa-user-shield fa-2x mb-3"></i>
                                    <h3>Roles & Permissions</h3>
                                    <p>Assign user roles and access rights</p>
                                </a>
                            </div>
                            <div class="col-md-4">
                                <a href="#" class="btn btn-custom btn-primary-custom w-100">
                                    <i class="fas fa-user-clock fa-2x mb-3"></i>
                                    <h3>User Activity</h3>
                                    <p>Monitor system usage</p>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Academic Structure Section -->
                <div class="section-container">
                    <button class="section-title-btn" type="button" data-bs-toggle="collapse" data-bs-target="#academicStructure">
                        <i class="fas fa-university"></i> Academic Structure
                    </button>
                    <div class="collapse section-content" id="academicStructure">
                        <div class="row g-4">
                            <div class="col-md-4">
                                <a href="{{ route('admin.departements.index') }}" class="btn btn-custom btn-success-custom w-100">
                                    <i class="fas fa-building fa-2x mb-3"></i>
                                    <h3>Departments</h3>
                                    <p>Manage departments</p>
                                </a>
                            </div>
                            <div class="col-md-4">
                                <a href="{{ route('admin.filieres.index') }}" class="btn btn-custom btn-success-custom w-100">
                                    <i class="fas fa-graduation-cap fa-2x mb-3"></i>
                                    <h3>Programs</h3>
                                    <p>Manage filières</p>
                                </a>
                            </div>
                            <div class="col-md-4">
                                <a href="{{ route('admin.affectations.index') }}" class="btn btn-custom btn-success-custom w-100">
                                    <i class="fas fa-tasks fa-2x mb-3"></i>
                                    <h3>Assignments</h3>
                                    <p>Manage course assignments</p>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- System & Reports Section -->
                <div class="section-container">
                    <button class="section-title-btn" type="button" data-bs-toggle="collapse" data-bs-target="#systemReports">
                        <i class="fas fa-cogs"></i> System & Reports
                    </button>
                    <div class="collapse section-content" id="systemReports">
                        <div class="row g-4">
                            <div class="col-md-4">
                                <a href="#" class="btn btn-custom btn-warning-custom w-100">
                                    <i class="fas fa-sliders-h fa-2x mb-3"></i>
                                    <h3>System Settings</h3>
                                    <p>Configure system parameters</p>
                                </a>
                            </div>
                            <div class="col-md-4">
                                <a href="{{ route('reports.teaching-loads') }}" class="btn btn-custom btn-warning-custom w-100">
                                    <i class="fas fa-chart-bar fa-2x mb-3"></i>
                                    <h3>Reports</h3>
                                    <p>Generate analytics & reports</p>
                                </a>
                            </div>
                            <div class="col-md-4">
                                <a href="#" class="btn btn-custom btn-warning-custom w-100">
                                    <i class="fas fa-bell fa-2x mb-3"></i>
                                    <h3>Notifications</h3>
                                    <p>System-wide announcements</p>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Custom JS to make collapse instant -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Remove transition duration for all collapse elements
        const collapses = document.querySelectorAll('.collapse');
        collapses.forEach(collapse => {
            collapse.style.transitionDuration = '0s';
        });

        // Optional: If you want to prevent the slight jump when closing
        const sectionButtons = document.querySelectorAll('[data-bs-toggle="collapse"]');
        sectionButtons.forEach(button => {
            button.addEventListener('click', function() {
                const target = document.querySelector(this.getAttribute('data-bs-target'));
                if (target.classList.contains('show')) {
                    target.style.display = 'none';
                    setTimeout(() => {
                        target.classList.remove('show');
                        target.style.display = '';
                    }, 10);
                }
            });
        });
    });
</script>
</body>
</html>
