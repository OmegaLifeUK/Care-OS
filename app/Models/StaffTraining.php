<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;
use Carbon\Carbon;

class StaffTraining extends Model
{
    protected $table = 'staff_training';

    protected $fillable = [
        'user_id',
        'training_id',
        'status',
        'due_date',
        'started_date',
        'completed_date',
        'expiry_date',
        'completion_notes',
        'assigned_by',
        'status_changed_by',
        'status_changed_at',
    ];

    protected $casts = [
        'due_date' => 'date',
        'started_date' => 'date',
        'completed_date' => 'date',
        'expiry_date' => 'date',
        'status_changed_at' => 'datetime',
    ];

    // Status constants
    const STATUS_NOT_STARTED = 0;
    const STATUS_COMPLETED = 1;
    const STATUS_ACTIVE = 2;

    // Relationships

    public function training()
    {
        return $this->belongsTo(Training::class, 'training_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Scopes

    public function scopeExpired($query)
    {
        return $query->whereNotNull('expiry_date')
                     ->where('expiry_date', '<', Carbon::today());
    }

    public function scopeExpiringSoon($query, $days = 30)
    {
        return $query->whereNotNull('expiry_date')
                     ->where('expiry_date', '>=', Carbon::today())
                     ->where('expiry_date', '<=', Carbon::today()->addDays($days));
    }
}
