@extends('layouts.admin')

     @section('content')
         <div class="container-fluid">
             <div class="card shadow mb-4">
                 <div class="card-header py-3">
                     <div class="d-flex justify-content-between align-items-center">
                         <h6 class="m-0 font-weight-bold text-primary">Create New Department</h6>
                         <a href="{{ route('admin.departements.index') }}" class="btn btn-secondary btn-sm">
                             <i class="fas fa-arrow-left"></i> Back to List
                         </a>
                     </div>
                 </div>
                 <div class="card-body">
                     <form action="{{ route('admin.departements.store') }}" method="POST">
                         @csrf

                         <div class="mb-3">
                             <label for="nom" class="form-label">Department Name</label>
                             <input type="text" class="form-control @error('nom') is-invalid @enderror"
                                    id="nom" name="nom" value="{{ old('nom') }}" required>
                             @error('nom')
                                 <div class="invalid-feedback">{{ $message }}</div>
                             @enderror
                         </div>

                         <div class="mb-3">
                             <label for="description" class="form-label">Description</label>
                             <textarea class="form-control @error('description') is-invalid @enderror"
                                       id="description" name="description" rows="3">{{ old('description') }}</textarea>
                             @error('description')
                                 <div class="invalid-feedback">{{ $message }}</div>
                             @enderror
                         </div>

                         <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                             <button type="submit" class="btn btn-success">
                                 <i class="fas fa-save"></i> Create Department
                             </button>
                         </div>
                     </form>
                 </div>
             </div>

             <!-- Initial Department Head Assignment -->
             <div class="card shadow mb-4">
                 <div class="card-header py-3">
                     <h6 class="m-0 font-weight-bold text-primary">Department Head Assignment</h6>
                 </div>
                 <div class="card-body">
                     <div class="alert alert-info">
                         <i class="fas fa-info-circle"></i> You can assign a department head after creating the department.
                     </div>
                 </div>
             </div>
         </div>
     @endsection
