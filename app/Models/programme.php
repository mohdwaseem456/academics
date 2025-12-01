<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\Programme
 *
 * @property int $id
 * @property string $name
 * @property int $department_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Batch> $batches
 * @property-read int|null $batches_count
 * @property-read \App\Models\Department $department
 * @method static \Illuminate\Database\Eloquent\Builder|Programme newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Programme newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Programme query()
 * @method static \Illuminate\Database\Eloquent\Builder|Programme whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Programme whereDepartmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Programme whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Programme whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Programme whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Programme extends Model
{
    use HasFactory;

    protected $table = 'programmes';

    protected $fillable = [
        'name',
        'department_id',
    ];

    /**
     * Get the department that owns the programme.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the batches associated with the programme.
     */
    public function batches(): HasMany
    {
        return $this->hasMany(Batch::class);
    }
}