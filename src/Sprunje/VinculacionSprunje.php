<?php
namespace UserFrosting\Sprinkle\Unlu\Sprunje;

use UserFrosting\Sprinkle\Core\Facades\Debug;
use UserFrosting\Sprinkle\Core\Sprunje\Sprunje;

use UserFrosting\Sprinkle\Unlu\Database\Models\Vinculacion;

class VinculacionSprunje extends Sprunje {
    protected $name = 'vinculaciones';

    protected $sortable = [
        "fecha_solicitud",
        "fecha_fin",
        "responsable",
        "cargo",
        "actividad",
        "tipo_de_usuario"
    ];

    protected $filterable = [
        "fecha_solicitud",
        "fecha_fin",
        "responsable",
        "cargo",
        "actividad",
        "tipo_de_usuario"
    ];

    /**
     * Set the initial query used by your Sprunje.
     */
    protected function baseQuery() {
        $instance = new Vinculacion();

        // Alternatively, if you have defined a class mapping, you can use the classMapper:
        // $instance = $this->classMapper->createInstance('owl');

        return $instance->newQuery()
            ->with('integrantes')
            ->with('solicitante')
            ->with('tipo_usuario');
    }
}