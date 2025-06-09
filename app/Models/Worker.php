<?php

namespace App\Models;

use App\Helpers\ConvertNumberToWords;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Worker extends Model {

    use HasFactory;

    // Fields that can be mass-assigned
    protected $fillable = [
        'insurance_AFP',
        'insurance_ISAPRE',
        'school_id',
        'rut',
        'name',
        'last_name',
        'birth_date',
        'address',
        'phone',
        'commune',
        'region',
        'nationality',
        'marital_status',
        'worker_type', // This is the worker type (teacher or non-teacher)
        'function_worker',
        'load_hourly_work',
        'worker_titular',
        'settlement_date',
    ];

    // Constants to define worker types
    const WORKER_TYPE_TEACHER = 1;
    const WORKER_TYPE_NON_TEACHER = 2;
    
    // Worker types list
    const WORKER_TYPES = [
        self::WORKER_TYPE_TEACHER => "Docente",
        self::WORKER_TYPE_NON_TEACHER => "No Docente",
    ];
    
    // List of worker functions (roles)
    const FUNCTION_WORKER = [
        1 => 'Docente de aula',
        2 => 'Administrativo Calificado',
        3 => 'Auxiliar',
        5 => 'Docente superior (Inspector general)',
        6 => 'Docente superior (UTP)',
        7 => 'Docente superior (Director)',
        8 => 'Administrativo General',
        9 => 'Inspector Patio',
    ];
    
    // Marital status types
    const MARITAL_STATUS = [
        1 => "SOLTERO(A)",
        2 => "CASADO(A)",
        3 => "VIUDO(A)",
        4 => "SEPARADO(A)",
    ];

    /**
     * Get the list of worker types.
     * @return array Worker types
     */
    public static function getWorkerTypes() {
        return self::WORKER_TYPES;
    }

    /**
     * Get the description of the worker's type.
     * @return string Worker type description
     */
    public function getDescriptionWorkerTypes() {
        return self::WORKER_TYPES[$this->worker_type]; // Returns description or "Unknown"
    }

    /**
     * Get the list of worker functions (roles).
     * @return array Worker functions
     */
    public static function getFunctionWorkerTypes() {
        return self::FUNCTION_WORKER;
    }

    /**
     * Get the description of the worker's function.
     * @return string Worker function description
     */
    public function getFunctionWorkerDescription() {
        return static::FUNCTION_WORKER[$this->function_worker]; // Returns description or "Unknown"
    }

    /**
     * Get the list of marital status types.
     * @return array Marital status types
     */
    public static function getMaritalStatusTypes() {
        return self::MARITAL_STATUS;
    }

    /**
     * Get the description of the worker's marital status.
     * @return string Marital status description
     */
    public function getMaritalStatusDescription() {
        return static::MARITAL_STATUS[$this->marital_status]; // Returns description or "Unknown"
    }

    /**
     * Get the names of the worker's insurances (AFP and ISAPRE).
     * @return array Insurance names
     */
    public function getInsuranceNames() {
        $insuranceAFPName = Insurance::getNameInsurance($this->insurance_AFP);
        $insuranceISAPREName = Insurance::getNameInsurance($this->insurance_ISAPRE);

        return [
            'insurance_AFP' => $insuranceAFPName,
            'insurance_ISAPRE' => $insuranceISAPREName,
        ];
    }

    /**
     * Create an array for hourly load based on the request input.
     * @param Request $request
     * Update the worker's hourly load.
     */
    public function createHourlyLoadArray(Request $request) {
        $loadHourlyWork = [
            'lunes' => $request->input('carga_lunes', 0),
            'martes' => $request->input('carga_martes', 0),
            'miercoles' => $request->input('carga_miercoles', 0),
            'jueves' => $request->input('carga_jueves', 0),
            'viernes' => $request->input('carga_viernes', 0),
            'sabado' => $request->input('carga_sabado', 0),
        ];
        // Store as JSON
        $this->load_hourly_work = json_encode($loadHourlyWork);
        // Save the updated worker
        $this->save();
    }

    /**
     * Create or update a worker with the provided data.
     * If a worker exists, update it; otherwise, create a new one.
     * @param array $data Worker data
     * @param Worker|null $worker
     * @return Worker The created or updated worker
     */
    public static function createOrUpdateWorker(array $data, Worker $worker = null) {
        if ($worker) {
            $worker->update($data); // Update the existing worker
        } else {
            $worker = self::create($data); // Create a new worker
        }

        return $worker;
    }

    /**
     * Get workers by school and worker type.
     * @param int $schoolId School ID
     * @param int|null $type Worker type (optional)
     * @return \Illuminate\Database\Eloquent\Collection Workers matching the criteria
     */
    public static function getWorkersBySchoolAndType($schoolId, $type = null) {
        $query = self::where('settlement_date', null)
                ->where('school_id', $schoolId);

        // Allow the type to be null or different from 3
        if ($type !== null && $type != 3) {
            $query->where('worker_type', $type);
        }

        return $query->orderBy('last_name')
                        ->orderBy('name')
                        ->get(['id', 'name', 'last_name', 'worker_type']);
    }

    // Method to prepare form data for contract creation or update
    public function prepareContractFormData() {
        // Initialize form data with default values
        $data = [
            'city' => '',
            'origin_city' => '',
            'schedule' => '',
            'levels' => '',
            'duration' => Contract::CONTRACT_TYPES[$this->contract->contract_type],
            'total_remuneration' => '',
            'remuneration_gloss' => '',
            'teaching_hours' => '',
            'curricular_hours' => '',
        ];
        // Get hourly work value (CARGAHORARIA)
        $hourlyWork = $this->parameters->where('name', "CARGAHORARIA")->first()->value;
        // Determine total remuneration based on worker type (Teacher or Non-Teacher)
        if ($this->worker_type == self::WORKER_TYPE_NON_TEACHER) {
            // For Non-Teacher workers, get the base salary (SUELDOBASEB)
            $sueldoB = $this->parameters->where('name', 'SUELDOBASEB')->first();
            $data['total_remuneration'] = $sueldoB->value;
        } else {
            // For Teacher workers, calculate based on the RBMN value and hourly work
            $rbnmTuituion = Tuition::where('title', "Valor RBMN")->value('tuition_id');
            $data['total_remuneration'] = Parameter::getParameterValue($rbnmTuituion, 0, $this->school_id) * $hourlyWork;
        }
        // Convert total remuneration to words (e.g., "Three hundred thousand")
        $data['remuneration_gloss'] = ConvertNumberToWords::convert($data['total_remuneration']);

        // Set the teaching or curricular hours based on worker type
        if ($this->worker_type == self::WORKER_TYPE_NON_TEACHER) {
            // Non-Teacher: Assign curricular hours
            $data['curricular_hours'] = $hourlyWork;
        } else {
            // Teacher: Assign teaching hours
            $data['teaching_hours'] = $hourlyWork;
        }

        // If a contract already exists, populate form fields with existing data
        if ($this->contract && $this->contract->details) {
            // Decode the existing contract details (JSON)
            $details = json_decode($this->contract->details);
            // Fill form data with existing contract details or leave default if not set
            $data['city'] = $details->city ?? $data['city'];
            $data['origin_city'] = $details->origin_city ?? $data['origin_city'];
            $data['schedule'] = $details->schedule ?? $data['schedule'];
            $data['levels'] = $details->levels ?? $data['levels'];
        }
        // Return the prepared form data
        return $data;
    }

    /**
     * Relationship with the 'insurances' table (AFP).
     */
    public function insuranceAFP() {
        return $this->belongsTo(Insurance::class, 'insurance_AFP');
    }

    /**
     * Relationship with the 'insurances' table (ISAPRE).
     */
    public function insuranceISAPRE() {
        return $this->belongsTo(Insurance::class, 'insurance_ISAPRE');
    }

    /**
     * Relationship with the 'licenses' table (licenses the worker has).
     */
    public function licenses() {
        return $this->hasMany(License::class);
    }

    /**
     * Relationship with the 'parameters' table (worker parameters).
     */
    public function parameters() {
        return $this->hasMany(Parameter::class);
    }

    /**
     * Relationship with the 'absences' table (worker absences).
     */
    public function absences() {
        return $this->hasMany(Absence::class);
    }

    /**
     * Relationship with the 'liquidations' table (worker liquidations).
     */
    public function liquidations() {
        return $this->hasMany(Liquidation::class);
    }

    /**
     * Relationship with the 'contracts' table (worker contract).
     */
    public function contract() {
        return $this->hasOne(Contract::class, 'worker_id');
    }

    /**
     * Relationship with the 'certificates' table (worker certificates).
     */
    public function certificates() {
        return $this->hasMany(Certificate::class);
    }

    /**
     * Relationship with the 'school' table (worker's school).
     */
    public function school() {
        return $this->belongsTo(School::class, 'school_id');
    }

}
