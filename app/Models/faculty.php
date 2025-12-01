<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\Faculty
 *
 * @property int $faculty_id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Faculty newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Faculty newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Faculty query()
 * @method static \Illuminate\Database\Eloquent\Builder|Faculty whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Faculty whereFacultyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Faculty whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Faculty whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Faculty extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'faculties';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'faculty_id'; // Primary Key is faculty_id 

    /**
     * Indicates if the IDs are auto-incrementing.
     * Set to false since faculty_id is a FK to users.user_id.
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
        'faculty_id', // This is the PK/FK 
        'name',       // Name column 
    ];

    // --- Relationships ---

    /**
     * Get the user record associated with the faculty member.
     */
    public function user(): BelongsTo
    {
        // Links the faculty record back to the user record using faculty_id.
        return $this->belongsTo(User::class, 'faculty_id', 'user_id');
    }
}