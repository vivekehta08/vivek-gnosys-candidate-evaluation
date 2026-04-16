<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Candidate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'resume',
        'final_status',
        'overall_score'
    ];

    public function screening()
    {
        return $this->hasOne(ResumeScreening::class);
    }

    public function evaluations()
    {
        return $this->hasMany(Evaluation::class);
    }

    public function getCurrentStageAttribute()
    {
        if ($this->final_status) {
            return 'Final: ' . $this->final_status;
        }

        if (! $this->screening) {
            return 'Screening';
        }

        if ($this->screening->status === 'Shortlisted') {
            return 'Evaluation';
        }

        return $this->screening->status;
    }
}
