<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bonus extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'tuition_id',
        'school_id',
        'worker_id',
        'taxable',
        'is_bonus',
        'application',
        'type',
        'factor',
        'imputable',
    ];

    const APPLICATION_OPTIONS = [
        'H' => 'Es un monto que se reparte dependiendo de la cantidad de horas contratadas',
        'D' => 'Es un monto fijo que depende de cada trabajador',
        'F' => 'Es un monto que se reparte a cada trabajador por igual',
        'C' => 'Es un factor de la carga horaria',
        'T' => 'Es un factor de la carga horaria con tope de 30 horas',
        'I' => 'Es un factor de la renta imponible (solo aplicable a descuentos)',
    ];

    public static function getTypeLabel($type)
    {
        $types = Operation::getWorkerTypes();
        return $types[$type] ?? 'Desconocido'; // Devuelve 'Desconocido' si no se encuentra el tipo
    }

    public static function processCreateBonuses($data)
    {
        // Inicialización
        $monto = $data['amount'] ?? 0;
        $factor = ($data['factor'] ?? 100) / 100;

        // Validación de la aplicación
        if ($data['is_bonus'] == 0 && $data['application'] == "I") {
            return [
                'success' => false,
                'message' => 'Esta forma de aplicación es solo aplicable a descuentos.',
            ];

        } else {

            // Generar y guardar clases usando time
            $nombre = Tuition::createUniqueTuition($data['title'], $data['type'], $data['school_id'], 'time');
            Tuition::addTuition($nombre, $data['title'], 'O', 1, $data['school_id']);
            Tuition::addTuition("APLICA" . $nombre, "Aplicación de " . $data['title'], 'P', 0, $data['school_id']);

            // Generar y guardar clase valor usando "valor"
            $nombrev = Tuition::createUniqueTuition($data['title'], $data['type'], $data['school_id'], 'valor');
            Tuition::addTuition($nombrev, "Valor " . $data['title'], 'P', 0, $data['school_id']);

            // Crear el bono
            self::createBonus([
                'title' => $nombrev,
                'tuition_id' => $nombre,
                'school_id' => $data['school_id'],
                'taxable' => $data['taxable'],
                'is_bonus' => $data['is_bonus'],
                'application' => $data['application'],
                'type' => $data['type'],
                'factor' => $factor,
                'imputable' => $data['imputable'],
            ]);

            $operation = Operation::generateOperation($data, $nombre, $nombrev, $factor);

            // Crear la cadena de meses
            $meses = '';
            for ($i = 1; $i <= 12; $i++) {
                $meses .= isset($data['months']) && in_array($i, $data['months']) ? '1' : '0';
            }

            // Modificar la operación en el lugar correspondiente
            Operation::processOperation($nombre, $data, $operation, $meses, '+', 1); // Cambia el operador según lo necesites

            // Crear parámetro si aplica
            if ($data['application'] != "D") {
                self::createParameter($nombrev, $data['school_id'], $monto, $data['title']);
            }

            return [
                'success' => true,
                'message' => 'Bono creado correctamente.',
            ];
        }

    }

    public static function deleteOldFunction($id)
    {
        $bonus = Bonus::find($id); // Asegúrate de que 'bonus_id' está en $data

        if ($bonus) {
            $isBonus = $bonus->is_bonus;
            $taxable = $bonus->taxable;
            $type = $bonus->type;
            $imputable = $bonus->imputable;
            $name = $bonus->title;

            if ($isBonus == 0) {
                $operator = "+";
                if ($taxable == 0) {
                    if ($imputable == 0) {
                        $operationType = "TAXABLEANDIMPUTABLE";
                    } else {
                        $operationType = ($type == 1) ? "TAXABLEANDNOTIMPUTABLE" : "TAXABLEINCOMES";
                    }
                } else {
                    $operationType = "TOTALNONTAXABLE";
                }
            } else {
                $operator = "+";
                $operationType = "VOLUNTARYDISCOUNTS";
            }

            if ($type != 3) {
                if (($type == 2) && ($operationType == "TAXABLEANDIMPUTABLE")) {
                    $operationType = "TAXABLEINCOMES";
                }
                $originalOperation = Operation::getOperationFunction($operationType, $type, $bonus['school_id']);
                $originalOperation = str_replace(" $operator $name", '', $originalOperation);
                $newOperation = $originalOperation . " " . $operator . " " . $name;
                Operation::updateOperationFunction($operationType, $type, $newOperation, $bonus['school_id']);
            } else {
                // For teachers
                $originalOperation = Operation::getOperationFunction($operationType, 1, $bonus['school_id']);
                $originalOperation = str_replace(" $operator $name", '', $originalOperation);
                $newOperation = $originalOperation . " " . $operator . " " . $name;
                Operation::updateOperationFunction($operationType, 1, $newOperation, $bonus['school_id']);

                // For non-teachers
                if ($operationType == "TAXABLEANDIMPUTABLE") {
                    $operationType = "TAXABLEINCOMES";
                }
                $originalOperation = Operation::getOperationFunction($operationType, 2, $bonus['school_id']);
                $originalOperation = str_replace(" $operator $name", '', $originalOperation);
                $newOperation = $originalOperation . " " . $operator . " " . $name;
                Operation::updateOperationFunction($operationType, 2, $newOperation, $bonus['school_id']);
            }
        }
    }

    public static function processUpdateBonuses($data, $id)
    {
        $monto = $data['amount'] ?? 0; // O un valor que tenga sentido en tu contexto
        $factor = ($data['factor'] ?? 100) / 100;

        // Validación de la aplicación
        if ($data['is_bonus'] == 0 && $data['application'] == "I") {
            return [
                'success' => false,
                'message' => 'Esta forma de aplicación es solo aplicable a descuentos.',
            ];
        } else {
            $name = $data['title'];
            $type = $data['type'];

            // Obtener el objeto Bonus por su ID
            $bonus = Bonus::find($id); // Asegúrate de que 'bonus_id' está en $data

            if ($bonus) {
                $nameValue = $bonus->tuition_id; // Acceder a tuition_id desde el objeto Bonus

                Tuition::updateTitleTuition($nameValue, $name, $data['school_id']);
                Parameter::updateParamValue($bonus->title, $data['school_id'], $monto);
                self::deleteOldFunction($id);

                // Adding operation
                $operation = Operation::generateOperation($data, $name, $nameValue, $factor);

                // Crear la cadena de meses
                $meses = '';
                for ($i = 1; $i <= 12; $i++) {
                    $meses .= isset($data['months']) && in_array($i, $data['months']) ? '1' : '0';
                }

                Operation::processOperation($nameValue, $data, $operation, $meses, '+', ""); // Cambia el operador según lo necesites

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

    public static function deleteProcessBonus($data)
    {
        if ($data) {
            $title = $data['title'];
            $tuitionId = $data['tuition_id'];
            $type = $data['type'];
            Tuition::deleteTuition($tuitionId, $data['school_id']);
            Tuition::deleteTuition("APLICA" . $tuitionId, $data['school_id']);
            Tuition::deleteTuition($title, $data['school_id']);

            if ($type != 3) {
                Operation::deleteOperation($tuitionId, 1, $data['school_id']);
                Operation::deleteOperation($tuitionId, 2, $data['school_id']);
            } else {
                Operation::deleteOperation($tuitionId, $type, $data['school_id']);

            }

            Operation::processDeleteOperation($tuitionId, $data, "+");

            Parameter::deleteParamAll($title, $data['school_id']);
            Parameter::deleteParamAll("APLICA" . $title, $data['school_id']);

            //Recorrer posiciones
            $positions = Template::listTuitionPositionsInTemplate($data['school_id'], $type, $tuitionId);

            if ($positions->isNotEmpty()) {
                foreach ($positions as $position) {
                    // Elimina la línea de la plantilla
                    Template::deleteTemplateLine($data['school_id'], $type, $position->position);

                    // Establece la posición inicial para mover hacia arriba
                    $p = $position->position + 1;

                    // Mueve posiciones hacia arriba mientras existan
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

    public static function createParameter($nombrev, $school_id, $monto, $title)
    {
        (new Parameter())->createParameter([
            'name' => $nombrev,
            'school_id' => $school_id,
            'description' => "Valor de {$title}",
            'unit' => '',
            'start_date' => '',
            'end_date' => '',
            'value' => $monto,
        ]);
    }

    public static function createBonus(array $data)
    {
        return self::create($data);
    }

    public static function updateBonus(array $data, $tuitionId, $factor)
    {
        // Obtener el objeto Bonus por su tuition_id
        $bonus = self::where('tuition_id', $tuitionId)->first(); // Busca el primer bonus que coincida con el tuition_id

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
            return $bonus; // Retorna el objeto actualizado si es necesario
        }

        return null; // Devuelve null si no se encuentra el bonus
    }

    public static function deleteBonus($title, $school_id, $type)
    {
        return self::where('title', $title)
            ->where('school_id', $school_id)
            ->where('type', $type)
            ->delete();
    }

    public static function getBonusesByTypeAndApplication($schoolId, $type, $application)
    {
        return self::where('school_id', $schoolId)
            ->where(function($query) use ($type) {
                $query->where('type', $type)
                      ->orWhere('type', 3);
            })
            ->where('application', $application)
            ->get(['title', 'tuition_id']);
    }

    // Relación: Un Bonus pertenece a una School
    public function school()
    {
        return $this->belongsTo(School::class);
    }

    // Relación: Un Bonus pertenece a un Worker
    public function worker()
    {
        return $this->belongsTo(Worker::class);
    }
}
