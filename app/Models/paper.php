<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * App\Models\Paper
 *
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Faculty> $faculties
 * @property-read int|null $faculties_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Student> $students
 * @property-read int|null $students_count
 * @method static \Illuminate\Database\Eloquent\Builder|Paper newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Paper newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Paper query()
 * @method static \Illuminate\Database\Eloquent\Builder|Paper whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Paper whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Paper whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Paper whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Paper extends Model
{
    use HasFactory;

    protected $table = 'papers';

    protected $fillable = [
        'name',
    ];

    /**
     * Get the faculty members assigned to this paper (paper_faculties pivot table).
     */
    public function faculties(): BelongsToMany
    {
        // Using the custom intermediate table name 'paper_faculties'
        return $this->belongsToMany(Faculty::class, 'paper_faculties', 'paper_id', 'faculty_id');
    }

    /**
     * Get the students enrolled in this paper (student_papers pivot table).
     */
    public function students(): BelongsToMany
    {
        // Using the custom intermediate table name 'student_papers'
        // We attach the 'status' column from the pivot table
        return $this->belongsToMany(Student::class, 'student_papers', 'paper_id', 'student_id')
                    ->withPivot('status')
                    ->withTimestamps();
    }
}