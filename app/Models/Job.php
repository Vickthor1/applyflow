<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Job extends Model
{
    use HasFactory;

    protected $fillable = [
        "title",
        "company",
        "description",
        "location",
        "link",
        "match_score",
        "applied",
        "salary",
        "job_type",
        "source",
        "applied_at",
        "application_status",
        "user_id",
        "latitude",
        "longitude",
        "city",
        "country",
        "language",
        "distance_km"
    ];

    protected $casts = [
        'applied' => 'boolean',
        'applied_at' => 'datetime',
        'match_score' => 'integer',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'distance_km' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
