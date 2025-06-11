<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
/*use Laravel\Sanctum\HasApiTokens;*/

class User extends Authenticatable
{
    use /*HasApiTokens*/ HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role', 'specialite', 'departement_id', 'filiere_id', 'matricule'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // Relationships
    public function departement()
    {
        return $this->belongsTo(Departement::class);
    }

    public function filiere()
    {
        return $this->belongsTo(Filiere::class);
    }

    public function affectations()
    {
        return $this->hasMany(Affectation::class);
    }

    public function notesUploaded()
    {
        return $this->hasMany(Note::class, 'uploaded_by');
    }

    public function notesEtudiant()
    {
        return $this->hasMany(Note::class, 'etudiant_id');
    }

    public function historiqueAffectations()
    {
        return $this->hasMany(HistoriqueAffectation::class);
    }

    public function logs()
    {
        return $this->hasMany(Log::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    public function reclamations()
    {
        return $this->hasMany(Reclamation::class);
    }

    public function chargesHoraires()
    {
        return $this->hasMany(ChargeHoraire::class);
    }

    public function fichiers()
    {
        return $this->hasMany(Fichier::class);
    }

    public function filieres()
    {
        return $this->belongsToMany(Filiere::class, 'coordonnateurs_filieres')
                    ->withTimestamps();
    }
}
