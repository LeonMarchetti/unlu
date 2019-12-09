<?php
namespace UserFrosting\Sprinkle\Unlu\Sprunje;

use UserFrosting\Sprinkle\Core\Facades\Debug;
use UserFrosting\Sprinkle\Core\Sprunje\Sprunje;

use UserFrosting\Sprinkle\Unlu\Database\Models\Servicio;

class ServicioSprunje extends Sprunje {
    protected $name = 'servicios';

    protected $sortable = [
        "denominacion"
    ];

    protected $filterable = [
        "denominacion",
        "observaciones"
    ];

    /**
     * Set the initial query used by your Sprunje.
     */
    protected function baseQuery() {
        $instance = new Servicio();

        // Alternatively, if you have defined a class mapping, you can use the classMapper:
        // $instance = $this->classMapper->createInstance('owl');

        return $instance->newQuery();
    }
}