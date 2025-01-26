<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Contract extends Model {

    use HasFactory;

    // Set the table columns you can mass-assign
    protected $fillable = [
        'worker_id',
        'contract_type',
        'hire_date',
        'termination_date',
        'details',
        'replacement_reason',
        'annexes', // Added to store annexes as JSON
    ];

    // Define contract types (e.g., indefinite, fixed-term, replacement)
    const CONTRACT_TYPES = [
        1 => 'Indefinido',
        2 => 'Plazo fijo',
        3 => 'Reemplazo',
        4 => 'Residual',
    ];
    // Options for contract duration (for display in forms)
    const DURATION_OPTIONS = [
        'Indefinido' => 'Indefinido',
        'Plazo fijo' => 'Plazo fijo',
        'Reemplazo' => 'Reemplazo',
    ];
    // Schedule options (e.g., morning, afternoon, night shifts)
    const SCHEDULE_OPTIONS = [
        'Mañana' => 'Mañana',
        'Tarde' => 'Tarde',
        'Nocturna' => 'Nocturna',
    ];
    // Educational levels for the worker
    const LEVELS_OPTIONS = [
        'Básica' => 'Básica',
        'Media' => 'Media',
        'Superior' => 'Superior',
    ];

    /**
     * Accessor to retrieve annexes as an array.
     * 
     * This method is used to decode the 'annexes' attribute (stored as JSON in the database)
     * and return it as an array.
     * 
     * @param string $value The JSON string stored in the 'annexes' column
     * @return array The decoded annexes as an array
     */
    public function getAnnexesAttribute($value) {
        return json_decode($value, true) ?? []; // Decode JSON or return empty array if null
    }

    /**
     * Mutator to save annexes as a JSON string.
     * 
     * This method is used to encode the 'annexes' attribute as JSON before saving it to the database.
     * 
     * @param mixed $value The annexes data to be saved
     * @return void
     */
    public function setAnnexesAttribute($value) {
        $this->attributes['annexes'] = json_encode($value); // Encode the annexes as JSON before saving
    }

    /**
     * Returns the available contract types.
     * 
     * This method returns the `CONTRACT_TYPES` constant, which is an array of contract types.
     * 
     * @return array The available contract types
     */
    public static function getContractTypes() {
        return self::CONTRACT_TYPES;
    }

    /**
     * Returns the description of the contract type.
     * 
     * This method fetches the description for the contract type of a specific contract.
     * 
     * @return string The contract type description
     */
    public function getContractTypesDescription() {
        return self::CONTRACT_TYPES[$this->contract_type] ?? 'Desconocido'; // Return description or 'Desconocido' if not found
    }

    /**
     * Checks if a contract exists for a worker.
     * 
     * This method checks if there is an existing contract for a specific worker based on their ID.
     * 
     * @param int $idWorker The ID of the worker
     * @return bool True if a contract exists for the worker, false otherwise
     */
    public static function contractExists($idWorker) {
        return self::where('worker_id', $idWorker)->exists(); // Check if a contract exists for the worker
    }

    /**
     * Retrieves the contract of a worker.
     * 
     * This method fetches the contract for a specific worker based on their ID.
     * 
     * @param int $idWorker The ID of the worker
     * @return \App\Models\Contract|null The worker's contract or null if not found
     */
    public static function getContract($idWorker) {
        return self::where('worker_id', $idWorker)->first(); // Return the first contract for the worker
    }

    /**
     * Creates or updates a contract for a worker.
     * 
     * This method creates or updates a worker's contract using the data provided in the request.
     * It will either create a new contract or update an existing one based on the worker's ID.
     * 
     * @param int $workerId The ID of the worker
     * @param \Illuminate\Http\Request $request The HTTP request containing contract data
     * @return \App\Models\Contract The created or updated contract
     */
    public static function createOrUpdateContract($workerId, Request $request) {
        // Extract the contract-related data from the request
        $data = $request->only(['contract_type', 'hire_date', 'termination_date', 'replacement_reason']);

        // Create or update the contract based on the worker ID
        return self::updateOrCreate(['worker_id' => $workerId], $data);
    }

    /**
     * Relationship: A Contract belongs to a Worker.
     * 
     * This method defines the relationship between the Contract and Worker models. 
     * A contract is associated with a specific worker.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo The relationship with the Worker model
     */
    public function worker() {
        return $this->belongsTo(Worker::class, 'worker_id'); // Define the inverse relationship with Worker
    }

}
