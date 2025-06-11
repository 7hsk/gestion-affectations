<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UniteEnseignement extends Model
{
    use HasFactory;

    protected $table = 'unites_enseignement';

    protected $fillable = [
        'code', 'nom', 'specialite', 'heures_cm', 'heures_td', 'heures_tp',
        'semestre', 'est_vacant', 'vacataire_types',
        'groupes_td', 'groupes_tp',
        'filiere_id', 'departement_id', 'responsable_id'
    ];

    protected $casts = [
        'est_vacant' => 'boolean',
        'vacataire_types' => 'array',
        'heures_cm' => 'integer',
        'heures_td' => 'integer',
        'heures_tp' => 'integer',
        'groupes_td' => 'integer',
        'groupes_tp' => 'integer',
    ];

    // Computed attributes
    public function getTotalHoursAttribute()
    {
        return ($this->heures_cm ?? 0) + ($this->heures_td ?? 0) + ($this->heures_tp ?? 0);
    }

    public function getVolumeHoraireAttribute()
    {
        return $this->total_hours;
    }

    // Auto-generate niveau based on filière name
    public function getNiveauAttribute()
    {
        if ($this->filiere) {
            $nom = $this->filiere->nom;
            // Extract level from filière name (e.g., "GI1" -> "L1", "GI2" -> "L2", etc.)
            if (preg_match('/(\d)/', $nom, $matches)) {
                $number = $matches[1];
                if ($number <= 3) {
                    return 'L' . $number;
                } else {
                    return 'M' . ($number - 3);
                }
            }
        }
        return 'Non défini';
    }



    // Relationships
    public function filiere()
    {
        return $this->belongsTo(Filiere::class);
    }

    public function departement()
    {
        return $this->belongsTo(Departement::class);
    }

    public function responsable()
    {
        return $this->belongsTo(User::class, 'responsable_id');
    }

    public function affectations()
    {
        return $this->hasMany(Affectation::class, 'ue_id');
    }

    public function notes()
    {
        return $this->hasMany(Note::class, 'ue_id');
    }

    public function historiqueAffectations()
    {
        return $this->hasMany(HistoriqueAffectation::class, 'ue_id');
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class, 'ue_id');
    }

    public function reclamations()
    {
        return $this->hasMany(Reclamation::class, 'ue_id');
    }
}
