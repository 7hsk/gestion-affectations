<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $fillable = [
        'type',
        'action',
        'description',
        'user_id',
        'subject_type',
        'subject_id',
        'properties',
        'ip_address',
        'user_agent'
    ];

    protected $casts = [
        'properties' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relationship to user who performed the action
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Polymorphic relationship to the subject of the activity
    public function subject()
    {
        return $this->morphTo();
    }

    // Static method to log activities
    public static function log($type, $action, $description, $subject = null, $properties = [])
    {
        $user = auth()->user();

        return static::create([
            'type' => $type,
            'action' => $action,
            'description' => $description,
            'user_id' => $user ? $user->id : null,
            'subject_type' => $subject ? get_class($subject) : null,
            'subject_id' => $subject ? $subject->id : null,
            'properties' => $properties,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);
    }

    // Helper method to get formatted time
    public function getTimeAttribute()
    {
        return $this->created_at ? $this->created_at->diffForHumans() : 'Date inconnue';
    }

    // Helper method to get formatted date
    public function getDateAttribute()
    {
        return $this->created_at ? $this->created_at->format('d/m/Y H:i') : 'Date inconnue';
    }

    // Helper method to get icon based on type
    public function getIconAttribute()
    {
        $icons = [
            'auth' => 'fas fa-sign-in-alt',
            'logout' => 'fas fa-sign-out-alt',
            'create' => 'fas fa-plus',
            'update' => 'fas fa-edit',
            'delete' => 'fas fa-trash',
            'approve' => 'fas fa-check',
            'reject' => 'fas fa-times',
            'upload' => 'fas fa-upload',
            'download' => 'fas fa-download',
            'export' => 'fas fa-file-export',
            'import' => 'fas fa-file-import',
            'system' => 'fas fa-cog',
            'security' => 'fas fa-shield-alt',
            'email' => 'fas fa-envelope',
            'notification' => 'fas fa-bell'
        ];

        return $icons[$this->type] ?? 'fas fa-info-circle';
    }

    // Helper method to get color based on type
    public function getColorAttribute()
    {
        $colors = [
            'auth' => 'success',
            'logout' => 'secondary',
            'create' => 'success',
            'update' => 'warning',
            'delete' => 'danger',
            'approve' => 'success',
            'reject' => 'danger',
            'upload' => 'primary',
            'download' => 'info',
            'export' => 'warning',
            'import' => 'info',
            'system' => 'dark',
            'security' => 'danger',
            'email' => 'info',
            'notification' => 'primary'
        ];

        return $colors[$this->type] ?? 'secondary';
    }
}
