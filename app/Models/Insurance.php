<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Insurance extends Model {

    use HasFactory;

    // Constants for the types of insurance
    const AFP = 1;
    const ISAPRE = 2;
    const FONASA = 3;

    // Define fillable attributes to allow mass assignment
    protected $fillable = [
        'name',
        'type',
        'cotizacion',
        'rut',
    ];

    /**
     * Types of insurance.
     * 
     * This array defines the types of insurance available in the system
     * using constants defined in the class.
     * 
     * @var array<int, string>
     */
    const TYPES = [
        self::AFP => 'AFP', // AFP type insurance
        self::ISAPRE => 'Salud', // ISAPRE health insurance
            // self::FONASA => 'Fonasa', // Uncomment if FONASA is needed
    ];

    /**
     * Get the available insurance types as a collection.
     * 
     * This static method is used to retrieve all available insurance types.
     *
     * @return \Illuminate\Support\Collection
     */
    public static function getInsuranceTypes() {
        return collect(self::TYPES); // Return types as a collection for flexible handling
    }

    /**
     * Get the name of the insurance type.
     * 
     * This method is used to return the name of the insurance type
     * based on the value of the 'type' attribute of the insurance.
     *
     * @return string
     */
    public function getTypeName() {
        return self::TYPES[$this->type] ?? 'Desconocido'; // Returns a default value if not found
    }

    /**
     * Get the name of the insurance by its ID.
     * 
     * This static method returns the 'name' of the insurance based on its ID.
     *
     * @param int $id
     * @return string|null
     */
    public static function getNameInsurance($id) {
        return self::where('id', $id)->value('name'); // Get the 'name' field based on the ID
    }

    /**
     * Get the cotization of the insurance by its ID.
     * 
     * This static method returns the 'cotizacion' (rate) of the insurance based on its ID.
     *
     * @param int $id
     * @return float|null
     */
    public static function getCotizationInsurance($id) {
        return self::where('id', $id)->value('cotizacion'); // Get the 'cotizacion' field based on the ID
    }

    /**
     * Inverse relationship with workers who have AFP insurance.
     * 
     * This method defines a relationship where an insurance record
     * can be associated with many workers who have AFP insurance.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function workersAFP() {
        return $this->hasMany(Worker::class, 'insurance_AFP'); // Defines relationship using the 'insurance_AFP' foreign key
    }

    /**
     * Inverse relationship with workers who have ISAPRE insurance.
     * 
     * This method defines a relationship where an insurance record
     * can be associated with many workers who have ISAPRE insurance.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function workersISAPRE() {
        return $this->hasMany(Worker::class, 'insurance_ISAPRE'); // Defines relationship using the 'insurance_ISAPRE' foreign key
    }

}
