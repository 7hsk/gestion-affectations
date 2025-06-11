@extends('layouts.admin')

                    @section('content')
                        <div class="container-fluid">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="m-0 font-weight-bold text-primary">Edit Department</h6>
                                        <a href="{{ route('admin.departements.index') }}" class="btn btn-secondary btn-sm">
                                            <i class="fas fa-arrow-left"></i> Back to List
                                        </a>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('admin.departements.update', $departement->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')

                                        <div class="mb-3">
                                            <label for="nom" class="form-label">Department Name</label>
                                            <input type="text" class="form-control @error('nom') is-invalid @enderror"
                                                   id="nom" name="nom" value="{{ old('nom', $departement->nom) }}" required>
                                            @error('nom')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="description" class="form-label">Description</label>
                                            <textarea class="form-control @error('description') is-invalid @enderror"
                                                      id="description" name="description" rows="3">{{ old('description', $departement->description) }}</textarea>
                                            @error('description')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-save"></i> Update Department
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <!-- Department Head Card -->
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Department Head Management</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <p><strong>Current Head:</strong>
                                            @if($departement->chef())
                                                {{ $departement->chef()->name }} ({{ $departement->chef()->email }})
                                            @else
                                                <span class="text-muted">No assigned head</span>
                                            @endif
                                        </p>
                                    </div>

                                    <form action="{{ route('admin.departements.updateChef', $departement) }}" method="POST">
                                        @csrf
                                        @method('PATCH')

                                        <div class="mb-3">
                                            <label for="chef_id" class="form-label">Select New Department Head</label>
                                            <select class="form-control @error('chef_id') is-invalid @enderror"
                                                    name="chef_id" id="chef_id" required>
                                                <option value="">Select a teacher</option>
                                                @foreach($users as $user)
                                                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                                @endforeach
                                            </select>
                                            @error('chef_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-user-shield"></i> Update Department Head
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endsection
