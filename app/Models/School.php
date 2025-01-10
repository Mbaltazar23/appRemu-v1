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

    public function getDependencyTextAttribute()
    {
        return self::DEPENDENCY_OPTIONS[$this->dependency] ?? 'No asignado';
    }

    public function getGranttTextAttribute()
    {
        return self::GRANTT_OPTIONS[$this->grantt] ?? 'No asignado';
    }
    /**
     * Obtiene el sostenedor relacionado al colegio.
     */
    public function sustainer()
    {
        return $this->belongsTo(Sustainer::class, 'sustainer_id');
    }

    public function schoolUsers()
    {
        return $this->hasMany(SchoolUser::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'school_users')->using(SchoolUser::class);
    }

    public function parameters()
    {
        return $this->hasMany(Parameter::class, 'school_id');
    }

    public function workers()
    {
        return $this->hasMany(Worker::class);
    }

    public function tuitions()
    {
        return $this->hasMany(Tuition::class);
    }

    public function operations()
    {
        return $this->hasMany(Operation::class);
    }

    public function bonuses()
    {
        return $this->hasMany(Bonus::class);
    }

    public function templates()
    {
        return $this->hasMany(Template::class);
    }

    public function payrolls()
    {
        return $this->hasMany(Payroll::class);
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }
}
