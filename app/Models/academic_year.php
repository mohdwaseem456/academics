<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AcademicYear extends Model
{
    use HasFactory;

    protected $table = 'academic_years';

    protected $fillable = [
        'year', // e.g., '2024-2025'
        'status',
    ];

    /**
     * Get the batches associated with the academic year.
     */
    public function batches(): HasMany
    {
        return $this->hasMany(Batch::class);
    }
}