@extends('layouts.admin')

@section('content')
    <div class="container-fluid">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Department Details</h6>
                    <div>
                        <a href="{{ route('admin.departements.index') }}" class="btn btn-secondary btn-sm me-2">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                        <div class="dropdown d-inline">
                            <button class="btn btn-primary btn-sm" type="button" id="departmentActions"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-ellipsis-v"></i> Actions
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="departmentActions">
                                <li>
                                    <a class="dropdown-item" href="{{ route('admin.departements.edit', $departement->id) }}">
                                        <i class="fas fa-edit me-2"></i> Edit
                                    </a>
                                </li>
                                <li>
                                    <form action="{{ route('admin.departements.destroy', $departement->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="dropdown-item text-danger"
                                                onclick="return confirm('Are you sure you want to delete this department?');">
                                            <i class="fas fa-trash me-2"></i> Delete
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="row">
                    <div class="col-md-12 mb-4">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <h2>{{ $departement->nom }}</h2>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="m-0 font-weight-bold">Users ({{ $departement->users->count() }})</h6>
                            </div>
                            <div class="card-body">
                                @if($departement->users->count() > 0)
                                    <ul class="list-group">
                                        @foreach($departement->users as $user)
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                {{ $user->name }}
                                                <span class="badge bg-primary rounded-pill">{{ $user->role }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <p class="text-center py-3">No users found in this department</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="m-0 font-weight-bold">Course Units ({{ $departement->unitesEnseignement->count() }})</h6>
                            </div>
                            <div class="card-body">
                                @if($departement->unitesEnseignement->count() > 0)
                                    <ul class="list-group">
                                        @foreach($departement->unitesEnseignement as $ue)
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                {{ $ue->nom }}
                                                <span class="badge bg-primary rounded-pill">{{ $ue->code }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <p class="text-center py-3">No course units found in this department</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
