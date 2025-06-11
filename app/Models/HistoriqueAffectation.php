<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoriqueAffectation extends Model
{
    use HasFactory;

    protected $table = 'historique_affectations';

    protected $fillable = [
        'ue_id', 'user_id', 'annee_universitaire', 'action', 'description', 'changes', 'created_by_name'
    ];

    protected $casts = [
        'changes' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function uniteEnseignement()
    {
        return $this->belongsTo(UniteEnseignement::class, 'ue_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
