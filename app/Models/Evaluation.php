<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evaluation extends Model
{
    use HasFactory;

    protected $fillable = [
        'candidate_id',
        'type',
        'feedback',
        'score'
    ];

    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }
}
