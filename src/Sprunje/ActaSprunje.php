<?php
namespace UserFrosting\Sprinkle\Unlu\Sprunje;

use UserFrosting\Sprinkle\Core\Facades\Debug;
use UserFrosting\Sprinkle\Core\Sprunje\Sprunje;

use UserFrosting\Sprinkle\Unlu\Database\Models\Acta;

class ActaSprunje extends Sprunje {
    protected $name = 'servicios';

    protected $sortable = [
        "fecha",
        "titulo",
    ];

    protected $filterable = [
        "fecha",
        "titulo"
    ];

    /**
     * Set the initial query used by your Sprunje.
     */
    protected function baseQuery() {
        $instance = new Acta();
        return $instance->newQuery();
    }
}