@extends('layouts.admin')

       @section('content')
           <div class="container-fluid">
               <div class="card shadow mb-4">
                   <div class="card-header py-3">
                       <div class="d-flex justify-content-between align-items-center">
                           <h6 class="m-0 font-weight-bold text-primary">Edit User: {{ $user->name }}</h6>
                           <a href="{{ route('admin.users.index') }}" class="btn btn-secondary btn-sm">
                               <i class="fas fa-arrow-left"></i> Back to Users
                           </a>
                       </div>
                   </div>
                   <div class="card-body">
                       <form method="POST" action="{{ route('admin.users.update', $user->id) }}">
                           @csrf
                           @method('PUT')

                           <div class="row mb-3">
                               <div class="col-md-6">
                                   <div class="form-group">
                                       <label for="name" class="form-label">Name</label>
                                       <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                       @error('name')
                                       <div class="invalid-feedback">{{ $message }}</div>
                                       @enderror
                                   </div>
                               </div>
                               <div class="col-md-6">
                                   <div class="form-group">
                                       <label for="email" class="form-label">Email</label>
                                       <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                       @error('email')
                                       <div class="invalid-feedback">{{ $message }}</div>
                                       @enderror
                                   </div>
                               </div>
                           </div>

                           <div class="row mb-3">
                               <div class="col-md-6">
                                   <div class="form-group">
                                       <label for="password" class="form-label">Password (leave blank to keep current)</label>
                                       <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password">
                                       @error('password')
                                       <div class="invalid-feedback">{{ $message }}</div>
                                       @enderror
                                   </div>
                               </div>
                               <div class="col-md-6">
                                   <div class="form-group">
                                       <label for="password_confirmation" class="form-label">Confirm Password</label>
                                       <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                                   </div>
                               </div>
                           </div>

                           <div class="row mb-3">
                               <div class="col-md-6">
                                   <div class="form-group">
                                       <label for="role" class="form-label">Role</label>
                                       <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                                           <option value="">Select Role</option>
                                           @foreach($roles as $role)
                                               <option value="{{ $role }}" {{ old('role', $user->role) == $role ? 'selected' : '' }}>
                                                   {{ ucfirst($role) }}
                                               </option>
                                           @endforeach
                                       </select>
                                       @error('role')
                                       <div class="invalid-feedback">{{ $message }}</div>
                                       @enderror
                                   </div>
                               </div>
                               <div class="col-md-6">
                                   <div class="form-group">
                                       <label for="departement_id" class="form-label">Department</label>
                                       <select class="form-select @error('departement_id') is-invalid @enderror" id="departement_id" name="departement_id">
                                           <option value="">Select Department</option>
                                           @foreach($departements as $departement)
                                               <option value="{{ $departement->id }}" {{ old('departement_id', $user->departement_id) == $departement->id ? 'selected' : '' }}>
                                                   {{ $departement->nom }}
                                               </option>
                                           @endforeach
                                       </select>
                                       @error('departement_id')
                                       <div class="invalid-feedback">{{ $message }}</div>
                                       @enderror
                                   </div>
                               </div>
                           </div>

                           <div class="row mb-3">
                               <div class="col-md-6">
                                   <div class="form-group">
                                       <label class="form-label">Specialities</label>
                                       <div class="card">
                                           <div class="card-body p-2" style="max-height: 200px; overflow-y: auto;">
                                               @foreach($specialites as $specialite)
                                                   <div class="form-check">
                                                       <input class="form-check-input" type="checkbox"
                                                              name="specialite[]"
                                                              id="specialite_{{ $loop->index }}"
                                                              value="{{ $specialite }}"
                                                           {{ in_array($specialite, old('specialite', is_array($user->specialite) ? $user->specialite : explode(',', $user->specialite ?? ''))) ? 'checked' : '' }}>
                                                       <label class="form-check-label" for="specialite_{{ $loop->index }}">
                                                           {{ $specialite }}
                                                       </label>
                                                   </div>
                                               @endforeach
                                           </div>
                                       </div>
                                       @error('specialite')
                                       <div class="invalid-feedback d-block">{{ $message }}</div>
                                       @enderror
                                   </div>
                               </div>
                               <div class="col-md-6">
                                   <div class="form-group" id="filiere-selection" style="display: none;">
                                       <label class="form-label">Filière (pour Coordonnateur)</label>
                                       <div class="card">
                                           <div class="card-body p-2">
                                               @php
                                                   $userFilieres = [];
                                                   if ($user->role === 'coordonnateur') {
                                                       $userFilieres = DB::table('coordonnateurs_filieres')
                                                           ->join('filieres', 'coordonnateurs_filieres.filiere_id', '=', 'filieres.id')
                                                           ->where('coordonnateurs_filieres.user_id', $user->id)
                                                           ->pluck('filieres.nom')
                                                           ->map(function($nom) {
                                                               return substr($nom, 0, 2); // Get base name (GI, ID, etc.)
                                                           })
                                                           ->unique()
                                                           ->toArray();
                                                   }
                                               @endphp
                                               <div class="form-check">
                                                   <input class="form-check-input" type="checkbox" name="filiere_base[]" id="filiere_gi_edit" value="GI"
                                                       {{ in_array('GI', $userFilieres) ? 'checked' : '' }}>
                                                   <label class="form-check-label" for="filiere_gi_edit">
                                                       <strong>GI - Génie Informatique</strong>
                                                       <small class="text-muted d-block">Inclut: GI1, GI2, GI3</small>
                                                   </label>
                                               </div>
                                               <div class="form-check">
                                                   <input class="form-check-input" type="checkbox" name="filiere_base[]" id="filiere_id_edit" value="ID"
                                                       {{ in_array('ID', $userFilieres) ? 'checked' : '' }}>
                                                   <label class="form-check-label" for="filiere_id_edit">
                                                       <strong>ID - Ingénierie des Données</strong>
                                                       <small class="text-muted d-block">Inclut: ID1, ID2, ID3</small>
                                                   </label>
                                               </div>
                                               <div class="form-check">
                                                   <input class="form-check-input" type="checkbox" name="filiere_base[]" id="filiere_gc_edit" value="GC"
                                                       {{ in_array('GC', $userFilieres) ? 'checked' : '' }}>
                                                   <label class="form-check-label" for="filiere_gc_edit">
                                                       <strong>GC - Génie Civil</strong>
                                                       <small class="text-muted d-block">Inclut: GC1, GC2, GC3</small>
                                                   </label>
                                               </div>
                                               <div class="form-check">
                                                   <input class="form-check-input" type="checkbox" name="filiere_base[]" id="filiere_gm_edit" value="GM"
                                                       {{ in_array('GM', $userFilieres) ? 'checked' : '' }}>
                                                   <label class="form-check-label" for="filiere_gm_edit">
                                                       <strong>GM - Génie Mécanique</strong>
                                                       <small class="text-muted d-block">Inclut: GM1, GM2, GM3</small>
                                                   </label>
                                               </div>
                                           </div>
                                       </div>
                                       @error('filiere_base')
                                       <div class="invalid-feedback d-block">{{ $message }}</div>
                                       @enderror
                                   </div>
                               </div>
                           </div>

                           <div class="form-group">
                               <button type="submit" class="btn btn-primary">Update User</button>
                           </div>
                       </form>
                   </div>
               </div>
           </div>
       @endsection

       @section('scripts')
       <script>
       document.addEventListener('DOMContentLoaded', function() {
           const roleSelect = document.getElementById('role');
           const filiereSelection = document.getElementById('filiere-selection');
           const specialiteSection = document.querySelector('.col-md-6:has([name="specialite[]"])');

           function toggleFiliereSelection() {
               const selectedRole = roleSelect.value;

               if (selectedRole === 'coordonnateur') {
                   filiereSelection.style.display = 'block';
                   // Hide specialite section for coordonnateur
                   if (specialiteSection) {
                       specialiteSection.style.display = 'none';
                   }
               } else {
                   filiereSelection.style.display = 'none';
                   // Show specialite section for other roles
                   if (specialiteSection) {
                       specialiteSection.style.display = 'block';
                   }
               }
           }

           // Initial check
           toggleFiliereSelection();

           // Listen for role changes
           roleSelect.addEventListener('change', toggleFiliereSelection);
       });
       </script>
       @endsection
