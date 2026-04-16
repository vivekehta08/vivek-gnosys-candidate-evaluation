<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResumeScreening extends Model
{
    use HasFactory;

    protected $fillable = [
        'candidate_id',
        'status',
        'remarks'
    ];

    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }
}
