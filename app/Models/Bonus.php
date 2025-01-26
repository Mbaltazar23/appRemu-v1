<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bonus extends Model
{
    use HasFactory;

    // Variables that can be mass-assigned to the Bonus model
    protected $fillable = [
        'title',         // The title of the bonus
        'tuition_id',    // The unique tuition ID associated with the bonus
        'school_id',     // The ID of the school the bonus belongs to
        'worker_id',     // The ID of the worker the bonus applies to
        'taxable',       // Flag indicating whether the bonus is taxable (0 = No, 1 = Yes)
        'is_bonus',      // Flag indicating if it is a bonus (1 = Yes, 0 = No)
        'application',   // Type of application for the bonus
        'type',          // The type of bonus
        'factor',        // A factor used for calculating the bonus
        'imputable',     // Flag indicating if the bonus is imputable (0 = No, 1 = Yes)
    ];
    
      // Describes the application types for the bonus
      const APPLICATION_OPTIONS = [
        'H' => 'Es un monto que se reparte dependiendo de la cantidad de horas contratadas',
        'D' => 'Es un monto fijo que depende de cada trabajador',
        'F' => 'Es un monto que se reparte a cada trabajador por igual',
        'C' => 'Es un factor de la carga horaria',
        'T' => 'Es un factor de la carga horaria con tope de 30 horas',
        'I' => 'Es un factor de la renta imponible (solo aplicable a descuentos)',
    ];

    /**
     * Gets a label for the type of bonus based on the type ID.
     * 
     * @param int $type The type ID of the bonus
     * @return string The label for the bonus type, or 'Desconocido' if the type is not found
     */
    public static function getTypeLabel($type)
    {
        $types = Operation::getWorkerTypes();
        return $types[$type] ?? 'Desconocido'; // Returns 'Unknown' if the type is not found
    }
      /**
     * Processes the creation of a new bonus, including tuition and operation creation.
     * 
     * @param array $data The data used to create the bonus
     * @return array Result with success or failure and a message
     */
    public static function processCreateBonuses($data)
    {
        // Initialize the amount and factor (defaulting factor to 100%)
        $monto = $data['amount'] ?? 0;
        $factor = ($data['factor'] ?? 100) / 100;
        
        // Validates the application type for the bonus
        if ($data['is_bonus'] == 0 && $data['application'] == "I") {
            return [
                'success' => false,
                'message' => 'Esta forma de aplicación es solo aplicable a descuentos.',
            ];
        } else {
            // Generate and save classes using "time"
            $nombre = Tuition::createUniqueTuition($data['title'], $data['type'], $data['school_id'], 'time');
            Tuition::addTuition($nombre, $data['title'], 'O', 1, 1, $data['school_id']);
            Tuition::addTuition("APLICA" . $nombre, "Aplicación de " . $data['title'], 'P', 0, 0, $data['school_id']);
            
            // Generate and save class value using "valor"
            $nombrev = Tuition::createUniqueTuition($data['title'], $data['type'], $data['school_id'], 'valor');
            Tuition::addTuition($nombrev, "Valor " . $data['title'], 'P', 0, 0, $data['school_id']);
            
            // Create the bonus entry in the database
            self::createBonus([
                'title' => $nombre,
                'tuition_id' => $nombrev,
                'school_id' => $data['school_id'],
                'taxable' => $data['taxable'],
                'is_bonus' => $data['is_bonus'],
                'application' => $data['application'],
                'type' => $data['type'],
                'factor' => $factor,
                'imputable' => $data['imputable'],
            ]);
            
            // Generate the operation related to the bonus
            $operation = Operation::generateOperation($data, $nombre, $nombrev, $factor);
            
            // Create the months string (for each month in a year)
            $meses = '';
            for ($i = 1; $i <= 12; $i++) {
                $meses .= isset($data['months']) && in_array($i, $data['months']) ? '1' : '0';
            }
            
            // Process the operation
            Operation::processOperation($nombre, $data, $operation, $meses, '+', 1); // Change the operator as needed
            
            // Create a parameter for the bonus if the application type is not "D"
            if ($data['application'] != "D") {
                self::createParameter($nombrev, $data['school_id'], $monto, $data['title']);
            }
            
            return [
                'success' => true,
                'message' => 'Bono creado correctamente.',
            ];
        }
    }

        /**
     * Processes the update of an existing bonus.
     * 
     * @param array $data The updated data for the bonus
     * @param int $id The ID of the bonus to update
     * @return array Result with success or failure and a message
     */
    public static function processUpdateBonuses($data, $id)
    {
        $monto = $data['amount'] ?? 0; // Amount associated with the bonus
        $factor = ($data['factor'] ?? 100) / 100;

        // Validate the application type for the bonus
        if ($data['is_bonus'] == 0 && $data['application'] == "I") {
            return [
                'success' => false,
                'message' => 'Esta forma de aplicación es solo aplicable a descuentos.',
            ];
        } else {
            $name = $data['title'];
            $type = $data['type'];
            // Retrieve the bonus object by its ID
            $bonus = Bonus::find($id); // Make sure 'bonus_id' is in $data
            $namev = $bonus['tuition_id'];

            if ($bonus) {
                $nameValue = $bonus->title; // Access tuition_id from the Bonus object

                Tuition::updateTitleTuition($nameValue, $name, $data['school_id']);
                Parameter::updateParamValue($bonus['tuition_id'], $data['school_id'], $monto);
                self::deleteOldFunction($id, $data['school_id']);

                // Generate the operation
                $operation = Operation::generateOperation($data, $nameValue, $namev, $factor);

                // Create the months string
                $meses = '';
                for ($i = 1; $i <= 12; $i++) {
                    $meses .= isset($data['months']) && in_array($i, $data['months']) ? '1' : '0';
                }

                // Process the operation
                Operation::processOperation($nameValue, $data, $operation, $meses, '+', ""); // Change the operator as needed

                if ($type != 3) {
                    Operation::deleteOperation($nameValue, $type, $data['school_id']);
                    Operation::addOperation($nameValue, $type, $operation, $meses, $data['school_id']);
                } else {
                    Operation::deleteOperation($nameValue, 1, $data['school_id']);
                    Operation::addOperation($nameValue, 1, $operation, $meses, $data['school_id']);

                    Operation::deleteOperation($nameValue, 2, $data['school_id']);
                    Operation::addOperation($nameValue, 2, $operation, $meses, $data['school_id']);
                }

                self::updateBonus($data, $nameValue, $factor);
            }

        }
        return [
            'success' => true,
            'message' => 'Bono actualizado correctamente.',
        ];
    }

    /**
     * Deletes an old bonus operation by removing the associated operations and tuition entries.
     * 
     * @param int $id The ID of the bonus to delete
     * @param int $schoolId The school ID associated with the bonus
     */
    public static function deleteOldFunction($id, $schoolId)
    {
        $bonus = Bonus::find($id); // Find the bonus by its ID

        if ($bonus) {
            $isBonus = $bonus->is_bonus;
            $taxable = $bonus->taxable;
            $type = $bonus->type;
            $imputable = $bonus->imputable;
            $name = $bonus->title;

            // Determine the type of operation based on bonus properties
            if ($isBonus == 0) {
                $operator = "+";
                if ($taxable == 0) {
                    if ($imputable == 0) {
                        $operationType = "IMPONIBLEEIMPUTABLE";
                    } else {
                        $operationType = ($type == 1) ? "IMPONIBLEYNOIMPUTABLE" : "RENTAIMPONIBLESD";
                    }
                } else {
                    $operationType = "TOTALNOIMPONIBLE";
                }
            } else {
                $operator = "+";
                $operationType = "DESCUENTOSVOLUNTARIOS";
            }
            
            // Handle operations based on bonus type
            if ($type != 3) {
                if (($type == 2) && ($operationType == "IMPONIBLEEIMPUTABLE")) {
                    $operationType = "RENTAIMPONIBLESD";
                }
                $originalOperation = Operation::getOperationFunction($operationType, $type, $schoolId);
                if (strpos($originalOperation, $name) == 0) {
                    $originalOperation = str_replace($name . " + ", "", $originalOperation);
                } else {
                    $originalOperation = str_replace(" " . $operator . " " . $name, "", $originalOperation);
                }
                $newOperation = $originalOperation . " " . $operator . " " . $name;
                Operation::updateOperationFunction($operationType, $type, $newOperation, $schoolId);
            } else {
                // Handle operations for teachers (type 1)
                $originalOperation = Operation::getOperationFunction($operationType, 1, $schoolId);
                if (strpos($originalOperation, $name) == 0) {
                    $originalOperation = str_replace($name . " + ", "", $originalOperation);
                } else {
                    $originalOperation = str_replace(" " . $operator . " " . $name, "", $originalOperation);
                }
                $newOperation = $originalOperation . " " . $operator . " " . $name;
                Operation::updateOperationFunction($operationType, 1, $newOperation, $schoolId);
                
                // Handle operations for non-teachers (type 2)
                if ($operationType == "IMPONIBLEEIMPUTABLE") {
                    $operationType = "RENTAIMPONIBLESD";
                }
                $originalOperation = Operation::getOperationFunction($operationType, 2, $schoolId);
                $originalOperation = str_replace(" ".$operator." ".$name,"", $originalOperation);
                $newOperation = $originalOperation . " " . $operator . " " . $name;
                Operation::updateOperationFunction($operationType, 2, $newOperation, $schoolId);
            }
        }
    }

    /**
     * Deletes a bonus based on the provided data.
     * 
     * @param request $data The data associated with the bonus to delete
     * @return array Result with success or failure and a message
     */
    public static function deleteProcessBonus($data)
    {
        if ($data) {
            $title = $data['title'];
            $tuitionId = $data['tuition_id'];
            $type = $data['type'];
            Tuition::deleteTuition($title, $data['school_id']);
            Tuition::deleteTuition("APLICA" . $title, $data['school_id']);
            Tuition::deleteTuition($tuitionId, $data['school_id']);

            if ($type != 3) {
                Operation::deleteOperation($title, $type, $data['school_id']);
            } else {
                Operation::deleteOperation($title, 1, $data['school_id']);
                Operation::deleteOperation($title, 2, $data['school_id']);
            }

            Operation::processDeleteOperation($title, $data, "+");

            Parameter::deleteParamAll($tuitionId, $data['school_id']);
            Parameter::deleteParamAll("APLICA" . $title, $data['school_id']);

            // Iterate through positions to adjust templates
            $positions = Template::listTuitionPositionsInTemplate($data['school_id'], $type, $title);

            if ($positions->isNotEmpty()) {
                foreach ($positions as $position) {
                    // Remove the line from the template
                    Template::deleteTemplateLine($data['school_id'], $type, $position->position);
                    $p = $position->position + 1;
                    while (Template::positionExists($data['school_id'], $type, $p) && $p >= $position->position) {
                        Template::movePositionUp($data['school_id'], $type, $p);
                        $p++;
                    }
                }
            }

            self::deleteBonus($title, $data['school_id'], $type);
        }
        return [
            'success' => true,
            'message' => 'Bono Eliminado correctamente.',
        ];
    }
    /**
     * Creates a parameter associated with a bonus.
     * 
     * @param string $nombrev The unique tuition ID for the bonus
     * @param int $school_id The school ID associated with the parameter
     * @param float $monto The value of the parameter
     * @param string $title The title of the bonus
     */
    public static function createParameter($nombrev, $school_id, $monto, $title)
    {
        (new Parameter())->createParameter([
            'name' => $nombrev,
            'school_id' => $school_id,
            'description' => "Valor de {$title}",
            'value' => $monto,
        ]);
    }
    /**
     * Creates a new bonus.
     * 
     * @param array $data The data to create the bonus
     * @return Bonus The created bonus object
     */
    public static function createBonus(array $data)
    {
        return self::create($data);
    }

    /**
     * Updates an existing bonus.
     * 
     * @param array $data The updated data for the bonus
     * @param string $tuitionId The tuition ID associated with the bonus
     * @param float $factor The updated factor for the bonus
     * @return Bonus|null The updated bonus object or null if not found
     */
    public static function updateBonus(array $data, $tuitionId, $factor)
    {
        // Retrieve the bonus by its tuition ID
        $bonus = self::where('tuition_id', $tuitionId)->first();
        if ($bonus) {
            $bonus->update([
                'school_id' => $data['school_id'],
                'taxable' => $data['taxable'],
                'is_bonus' => $data['is_bonus'],
                'application' => $data['application'],
                'type' => $data['type'],
                'factor' => $factor,
                'imputable' => $data['imputable'],
            ]);
            return $bonus;
        }
        return null; // Return null if no bonus is found
    }

    /**
     * Deletes a bonus based on the provided title, school ID, and type.
     * 
     * @param string $title The title of the bonus
     * @param int $school_id The school ID associated with the bonus
     * @param int $type The type of bonus
     * @return int The number of rows affected by the delete operation
     */
    public static function deleteBonus($title, $school_id, $type)
    {
        return self::where('title', $title)
            ->where('school_id', $school_id)
            ->where('type', $type)
            ->delete();
    }

    /**
     * Retrieves all bonuses of a specific type and application for a given school.
     * 
     * This method is useful for fetching bonuses that match a specific type and application,
     * where the type can either be the provided `type` or 3 (a wildcard type, which can be interpreted
     * as a special case).
     *
     * @param int $schoolId The ID of the school to fetch bonuses for
     * @param int $type The type of the bonus (e.g., 1 for regular bonuses, 2 for special bonuses)
     * @param string $application The application type (e.g., "H" for hourly-based, "D" for fixed amount)
     * @return \Illuminate\Database\Eloquent\Collection A collection of Bonus objects matching the criteria
     */
    public static function getBonusesByTypeAndApplication($schoolId, $type, $application)
    {
        // Fetch bonuses based on the given school ID, type, and application type
        return self::where('school_id', $schoolId)
            ->where(function ($query) use ($type) {
                // The type can be the specified type or type 3 (which might represent a special case)
                $query->where('type', $type)
                    ->orWhere('type', 3);
            })
            ->where('application', $application) // Filter by application type (e.g., hourly, fixed)
            ->get(['title', 'tuition_id']); // Return only the title and tuition_id fields
    }

    /**
     * Relationship: A Bonus belongs to a School.
     * 
     * This method defines the relationship between the Bonus model and the School model.
     * It indicates that each bonus is associated with one school, and we can access that school
     * using the `school()` method.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo The relationship between Bonus and School
     */
    public function school()
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Relationship: A Bonus belongs to a Worker.
     * 
     * This method defines the relationship between the Bonus model and the Worker model.
     * It indicates that each bonus is associated with a specific worker, and we can access that worker
     * using the `worker()` method.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo The relationship between Bonus and Worker
     */
    public function worker()
    {
        return $this->belongsTo(Worker::class);
    }

}
