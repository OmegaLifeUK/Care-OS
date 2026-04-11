<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

class Training extends Model
{
    protected $table = 'training';

    protected $fillable = [
        'home_id',
        'training_name',
        'training_provider',
        'training_desc',
        'training_month',
        'training_year',
        'training_date',
        'status',
        'is_mandatory',
        'category',
        'expiry_months',
        'max_employees',
        'created_by',
        'updated_by',
        'is_deleted',
    ];

    protected $casts = [
        'is_mandatory' => 'boolean',
        'is_deleted' => 'boolean',
        'training_date' => 'date',
        'training_month' => 'integer',
        'expiry_months' => 'integer',
        'max_employees' => 'integer',
    ];

    // Relationships

    public function staffTrainings()
    {
        return $this->hasMany(StaffTraining::class, 'training_id');
    }

    // Scopes

    public function scopeForHome($query, $homeId)
    {
        return $query->where('home_id', $homeId);
    }

    public function scopeActive($query)
    {
        return $query->where('is_deleted', 0);
    }
}
