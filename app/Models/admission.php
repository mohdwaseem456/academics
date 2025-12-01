<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\Admission
 *
 * @property int $id
 * @property int $student_id
 * @property int $batch_id
 * @property string $admission_number
 * @property string $roll_number
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Batch $batch
 * @property-read \App\Models\Student $student
 * @method static \Illuminate\Database\Eloquent\Builder|Admission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Admission newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Admission query()
 * @method static \Illuminate\Database\Eloquent\Builder|Admission whereAdmissionNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Admission whereBatchId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Admission whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Admission whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Admission whereRollNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Admission whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Admission whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Admission extends Model
{
    use HasFactory;

    protected $table = 'admissions';

    protected $fillable = [
        'student_id',
        'batch_id',
        'admission_number',
        'roll_number',
    ];

    /**
     * Get the student associated with the admission record.
     */
    public function student(): BelongsTo
    {
        // Links back to the Student model using student_id
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }

    /**
     * Get the batch this admission belongs to.
     */
    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class);
    }
}