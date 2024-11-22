<?php

namespace App\Helpers;

class LiquidationRecord
{
    public $id;
    public $title;
    public $value;
    public $inLiquidation;

    public function __construct($id, $title, $value, $inLiquidation)
    {
        $this->id = $id;
        $this->title = $title;
        $this->value = $value;
        $this->inLiquidation = $inLiquidation;
    }
}
