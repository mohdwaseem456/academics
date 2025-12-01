<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne; // Added for relationships
use Laravel\Passport\HasApiTokens;

/**
 * App\Models\User
 *
 * @property int $user_id
 * @property int $user_type 1=Student, 2=Faculty
 * @property string $email
 * @property string $password
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Passport\Client> $clients
 * @property-read int|null $clients_count
 * @property-read \App\Models\Faculty|null $faculty
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \App\Models\Student|null $student
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Passport\Token> $tokens
 * @property-read int|null $tokens_count
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUserType($value)
 * @mixin \Eloquent
 */
class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'user_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_type',
        'email',
        'password',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // --- Type Constants based on requirements  ---

  const TYPE_STUDENT = 1; 
  const TYPE_FACULTY = 2;

    // --- Relationships to Student and Faculty Tables [cite: 8, 11] ---

    /**
     * Get the student record associated with the user.
     */
    public function student(): HasOne
    {
        // The student table uses user_id as its foreign key[cite: 8].
        return $this->hasOne(Student::class, 'user_id', 'user_id');
    }

    /**
     * Get the faculty record associated with the user.
     */
    public function faculty(): HasOne
    {
        // The faculties table uses user_id as its foreign key[cite: 11].
        return $this->hasOne(Faculty::class, 'user_id', 'user_id');
    }

    // --- Helper Methods ---

    /**
     * Check if the user is a student.
     */
    public function isStudent(): bool
    {
        return $this->user_type === self::TYPE_STUDENT;
    }

    /**
     * Check if the user is a faculty member.
     */
    public function isFaculty(): bool
    {
        return $this->user_type === self::TYPE_FACULTY;
    }
}