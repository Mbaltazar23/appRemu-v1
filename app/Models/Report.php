<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;
    public static function getDescriptionList($school_id, $tuition_id)
    {
        return Parameter::leftJoin('insurances', 'parameters.description', '=', 'insurances.id') // Realiza el JOIN
            ->where('parameters.name', $tuition_id) // Filtra por el nombre del parámetro
            ->where('parameters.school_id', $school_id) // Filtra por el school_id
            ->select('parameters.description', 'insurances.name') // Selecciona los campos description y name
            ->groupBy('parameters.description', 'insurances.name') // Agrupa por description e insurance name
            ->get(); // Obtiene los resultados
    }

    public static function getDetailInsurance($parameter, $idInsurance, $school_id, $mount, $year)
    {
        return Worker::leftJoin('parameters', 'workers.id', '=', 'parameters.worker_id')
            ->leftJoin('liquidations', 'workers.id', '=', 'liquidations.worker_id')
            ->where('parameters.name', $parameter)
            ->where('parameters.description', $idInsurance)
            ->where('workers.school_id', $school_id)
            ->where('liquidations.month', $mount)
            ->where('liquidations.year', $year)
            ->select('workers.id as worker_id', 'liquidations.id as liquidation_id', 'workers.rut', 'workers.name','workers.last_name')
            ->get();
    }
}
