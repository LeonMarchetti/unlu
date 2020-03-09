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
        "resolucion",

        "aprobada", // Si la vinculación está aprobada, si tiene un acta asignada
        "tipo_usuario", // Join con "tipo_usuario" a través de "tipo_de_usuario"
        "usuario", // Join con "users" a través de "id_solicitante"
    ];

    protected $listable = [
        "aprobada",
        "tipo_usuario"
    ];

    /**
     * Set the initial query used by your Sprunje.
     */
    protected function baseQuery() {
        $instance = $this->classMapper->createInstance("vinculacion");
        return $instance->newQuery()->with('integrantes', 'solicitante', 'tipo_usuario');
    }

    /**
     * Filtrar por si tiene un acta y por lo tanto está aprobada
     *
     * @param Builder $query
     * @param mixed $value
     * @return $this
     */
    protected function filterAprobada($query, $value) {
        switch ($value) {
            case "aprobada":
                $query->whereNotNull('id_acta');
                break;
            case "no-aprobada":
                $query->whereNull('id_acta');
                break;
        }
        return $this;
    }

    /**
     * Listar por si tiene un acta y por lo tanto está aprobada
     *
     * @param Builder $query
     * @param mixed $value
     * @return $this
     */
    protected function listAprobada() {
        return [
            [ "value" => "aprobada", "text" => "Aprobada" ],
            [ "value" => "no-aprobada", "text" => "No aprobada" ],
        ];
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
     * Listar por tipo de usuario según su descripcion
     *
     * @param Builder $query
     * @param mixed $value
     * @return $this
     */
    protected function listTipoUsuario() {
        $resultado = [];
        $tipos_usuario = $this->classMapper->getClassMapping("tipo_de_usuario")::all();

        foreach ($tipos_usuario as $tipo) {
            $resultado[] = [ "value" => $tipo->description, "text" => $tipo->description ];
        }

        return $resultado;
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
