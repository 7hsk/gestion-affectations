<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Affectation extends Model
{
    use HasFactory;

    protected $fillable = [
        'ue_id', 'user_id', 'type_seance', 'validee', 'annee_universitaire',
        'validee_par', 'date_validation', 'commentaire'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'date_validation' => 'datetime',
    ];

    // Status constants
    const STATUS_PENDING = 'en_attente';
    const STATUS_APPROVED = 'valide';
    const STATUS_REJECTED = 'rejete';
    const STATUS_REFUSED = 'refuse';
    const STATUS_CANCELLED = 'annule';

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
