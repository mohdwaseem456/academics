<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Added for the inverse relationship

/**
 * App\Models\Student
 *
 * @property int $student_id
 * @property string $first_name
 * @property string|null $last_name
 * @property string|null $date_of_birth
 * @property string|null $phone_number
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Student newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Student newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Student query()
 * @method static \Illuminate\Database\Eloquent\Builder|Student whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Student whereDateOfBirth($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Student whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Student whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Student wherePhoneNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Student whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Student whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Student extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'students';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'student_id';

    /**
     * Indicates if the IDs are auto-incrementing.
     * Since student_id is a FK to users.user_id, it is not auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The data type of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'int';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'student_id', // This is the PK/FK
        'first_name',
        'last_name',
        'date_of_birth',
        'phone_number',
    ];

    // --- Relationships ---

    /**
     * Get the user record associated with the student.
     */
    public function user(): BelongsTo
    {
        // student_id is both the local key and the foreign key linking to the User model.
        return $this->belongsTo(User::class, 'student_id', 'user_id');
    }
}