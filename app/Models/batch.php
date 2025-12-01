<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\Batch
 *
 * @property int $id
 * @property string $name
 * @property int $programme_id
 * @property int $academic_year_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Admission> $admissions
 * @property-read int|null $admissions_count
 * @property-read \App\Models\Programme $programme
 * @method static \Illuminate\Database\Eloquent\Builder|Batch newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Batch newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Batch query()
 * @method static \Illuminate\Database\Eloquent\Builder|Batch whereAcademicYearId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Batch whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Batch whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Batch whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Batch whereProgrammeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Batch whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Batch extends Model
{
    use HasFactory;

    protected $table = 'batches';

    protected $fillable = [
        'name',
        'programme_id',
        'academic_year_id',
    ];

    /**
     * Get the programme this batch belongs to.
     */
    public function programme(): BelongsTo
    {
        return $this->belongsTo(Programme::class);
    }

    /**
     * Get the academic year this batch belongs to.
     */
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * Get the admissions records for this batch.
     */
    public function admissions(): HasMany
    {
        return $this->hasMany(Admission::class);
    }
}