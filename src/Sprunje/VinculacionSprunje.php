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
        "id_acta",
    ];

    protected $filterable = [
        "fecha_solicitud",
        "fecha_fin",
        "responsable",
        "cargo",
        "actividad",
        "descripcion",
        "correo",

        "tipo_usuario", // Join con "tipo_usuario" a través de "tipo_de_usuario"
        "usuario", // Join con "users" a través de "id_solicitante"
    ];

    /**
     * Set the initial query used by your Sprunje.
     */
    protected function baseQuery() {
        $instance = new Vinculacion();

        // Alternatively, if you have defined a class mapping, you can use the classMapper:
        // $instance = $this->classMapper->createInstance('owl');

        return $instance->newQuery()->with('integrantes', 'solicitante', 'tipo_usuario');
    }

    /**
     * Filtrar por tipo de usuario según su descripcion
     *
     * @param Builder $query
     * @param mixed $value
     * @return $this
     */
    protected function filterTipoUsuario($query, $value) {
        $query->whereHas('tipo_usuario', function($subQuery) use ($value) {
            $subQuery->like('description', $value);
        });

        return $this;
    }

    /**
     * Filtrar por usuario solicitante según nombre de usuario (user_name)
     *
     * @param Builder $query
     * @param mixed $value
     * @return $this
     */
    protected function filterUsuario($query, $value) {
        $query->whereHas('solicitante', function($subQuery) use ($value) {
            $subQuery->like('user_name', $value);
        });

        return $this;
    }
}