<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TmpLiquidation extends Model
{
 // La tabla asociada con el modelo
 protected $table = 'tmp_liquidation';

 // No necesitamos definir el campo 'id' ya que Laravel lo maneja automáticamente como auto-incrementable
 protected $primaryKey = 'id'; // Usamos 'id' como clave primaria autoincrementable

 // Definir los campos que se pueden asignar masivamente
 protected $fillable = ['tuition_id', 'title', 'value', 'in_liquidation'];

}
