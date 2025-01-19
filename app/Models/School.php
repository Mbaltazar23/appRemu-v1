<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class School extends Model
{
    use HasFactory;

    protected $fillable = [
        'rut',
        'name',
        'rbd',
        'address',
        'commune',
        'region',
        'director',
        'rut_director',
        'phone',
        'email',
        'dependency',
        'grantt',
        'sustainer_id',
    ];

    const DEPENDENCY_OPTIONS = [
        1 => 'Particular',
        2 => 'Particular c/f/c',
        3 => 'Particular s/f/c',
    ];

    const GRANTT_OPTIONS = [
        1 => 'Particular',
        2 => 'Particular c/f/c',
        3 => 'Particular s/f/c',
    ];
    /**
     * Get the dependency text.
     * 
     * @return string - The dependency option text.
     */
    public function getDependencyTextAttribute()
    {
        return self::DEPENDENCY_OPTIONS[$this->dependency] ?? 'No Asignada';
    }
    /**
     * Get the grant text.
     * 
     * @return string - The grant option text.
     */
    public function getGranttTextAttribute()
    {
        return self::GRANTT_OPTIONS[$this->grantt] ?? 'No Asignada';
    }
    /**
     * Get the sustainer related to the school.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo - The sustainer relationship.
     */
    public function sustainer()
    {
        return $this->belongsTo(Sustainer::class);
    }
    /**
     * Get the users associated with the school.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany - The school-user relationship.
     */
    public function schoolUsers()
    {
        return $this->hasMany(SchoolUser::class);
    }
    /**
     * Get the users related to the school.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany - The many-to-many relationship with users.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'school_users')->using(SchoolUser::class);
    }
    /**
     * Get the parameters related to the school.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany - The school-parameter relationship.
     */
    public function parameters()
    {
        return $this->hasMany(Parameter::class, 'school_id');
    }
    /**
     * Get the workers related to the school.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany - The school-worker relationship.
     */
    public function workers()
    {
        return $this->hasMany(Worker::class);
    }
    /**
     * Get the tuitions related to the school.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany - The school-tuition relationship.
     */
    public function tuitions()
    {
        return $this->hasMany(Tuition::class);
    }
    /**
     * Get the operations related to the school.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany - The school-operation relationship.
     */
    public function operations()
    {
        return $this->hasMany(Operation::class);
    }
    /**
     * Get the bonuses related to the school.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany - The school-bonus relationship.
     */
    public function bonuses()
    {
        return $this->hasMany(Bonus::class);
    }
    /**
     * Get the templates related to the school.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany - The school-template relationship.
     */
    public function templates()
    {
        return $this->hasMany(Template::class);
    }
    /**
     * Get the payrolls related to the school.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany - The school-payroll relationship.
     */
    public function payrolls()
    {
        return $this->hasMany(Payroll::class);
    }
    /**
     * Get the certificates related to the school.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany - The school-certificate relationship.
     */
    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }
}
