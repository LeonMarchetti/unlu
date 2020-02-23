<?php
namespace UserFrosting\Sprinkle\Unlu\Sprunje;

use UserFrosting\Sprinkle\Core\Facades\Debug;
use UserFrosting\Sprinkle\Core\Sprunje\Sprunje;

use UserFrosting\Sprinkle\Unlu\Database\Models\Peticion;

class PeticionSprunje extends Sprunje {
    protected $name = 'peticiones';

    protected $sortable = [
        "fecha_inicio",
        "fecha_fin",
        "aprobada",
    ];

    protected $filterable = [
        "fecha_inicio",
        "fecha_fin",
        "descripcion",
        "observaciones",

        "nombre",
        "servicio",
        "usuario",
        "vinculacion",
    ];

    /**
     * Set the initial query used by your Sprunje.
     */
    protected function baseQuery() {
        $instance = $this->classMapper->createInstance('peticion');
        return $instance->newQuery()->with('servicio', 'usuario', 'vinculacion');
    }

    /**
     * Filtrar por nombre de usuario
     *
     * @param Builder $query
     * @param mixed $value
     * @return $this
     */
    protected function filterNombre($query, $value) {
        $query->whereHas('usuario', function($subQuery) use ($value) {
            $subQuery->like('first_name', $value)
                     ->orLike('last_name', $value);
        });
        return $this;
    }

    /**
     * Filtrar por servicio
     *
     * @param Builder $query
     * @param mixed $value
     * @return $this
     */
    protected function filterServicio($query, $value) {
        $query->whereHas('servicio', function($subQuery) use ($value) {
            $subQuery->like('denominacion', $value);
        });

        return $this;
    }

    /**
     * Filtrar por usuario
     *
     * @param Builder $query
     * @param mixed $value
     * @return $this
     */
    protected function filterUsuario($query, $value) {
        $query->whereHas('usuario', function($subQuery) use ($value) {
            $subQuery->like('user_name', $value);
        });

        return $this;
    }

    /**
     * Filtrar por vinculación
     *
     * @param Builder $query
     * @param mixed $value
     * @return $this
     */
    protected function filterVinculacion($query, $value) {
        $query->whereHas('vinculacion', function($subQuery) use ($value) {
            $subQuery->like('actividad', $value);
        });

        return $this;
    }
}
