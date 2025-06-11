<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reclamation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'ue_id', 'sujet', 'message', 'statut'
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function uniteEnseignement()
    {
        return $this->belongsTo(UniteEnseignement::class, 'ue_id');
    }
}
