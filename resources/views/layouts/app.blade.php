@extends('layouts.admin')

@section('title', 'Admin Panel')

@section('content')
    <!-- Floating Background Shapes -->
    <div class="admin-floating-shapes">
        <div class="admin-floating-shape"></div>
        <div class="admin-floating-shape"></div>
        <div class="admin-floating-shape"></div>
        <div class="admin-floating-shape"></div>
    </div>

    <div class="container-fluid">
        @hasSection('content')
            @yield('content')
        @else
            {{ $slot ?? '' }}
        @endif
    </div>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Custom Admin Dashboard Scripts -->
    <script>
        // Initialize any charts or interactive elements here
        document.addEventListener('DOMContentLoaded', function() {
            // Your dashboard initialization code
        });
    </script>
@endsection
