<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Filiere extends Model
{
    use HasFactory;

    protected $fillable = ['nom', 'departement_id'];

    // Relationships
    public function departement()
    {
        return $this->belongsTo(Departement::class);
    }

    public function unitesEnseignement()
    {
        return $this->hasMany(UniteEnseignement::class);
    }

    public function coordonnateurs()
    {
        return $this->belongsToMany(User::class, 'coordonnateurs_filieres')
                    ->withTimestamps();
    }
}
