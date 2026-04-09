<?php

namespace App\Models\Staff;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffReportIncidentsSafeguarding extends Model
{
    use HasFactory;

    protected $table = 'staff_report_incidents_safeguardings';
    public $timestamps = false;

    protected $fillable = [
        'staff_report_incident_id',
        'safeguarding_type_id',
    ];

    public function incident()
    {
        return $this->belongsTo(StaffReportIncidents::class, 'staff_report_incident_id');
    }

    public function safeguardingType()
    {
        return $this->belongsTo(SafeguardingType::class, 'safeguarding_type_id');
    }
}
