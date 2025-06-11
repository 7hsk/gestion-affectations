<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChargeHoraire extends Model
{
    use HasFactory;

    protected $table = 'charges_horaires';

    protected $fillable = [
        'user_id', 'annee_universitaire', 'total_volume_horaire'
    ];

    public $timestamps = false;

    protected $casts = [
        'created_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
