<?php
namespace UserFrosting\Sprinkle\Unlu\Sprunje;

use UserFrosting\Sprinkle\Core\Facades\Debug;
use UserFrosting\Sprinkle\Core\Sprunje\Sprunje;

use UserFrosting\Sprinkle\Unlu\Database\Models\UsuarioUnlu as Usuario;

class UsuarioSprunje extends Sprunje {
    protected $name = 'users';

    protected $sortable = [
        "user_name",
        "full_name",
    ];

    protected $filterable = [
        "user_name",
        "full_name",
    ];

    /**
     * Set the initial query used by your Sprunje.
     */
    protected function baseQuery() {
        $instance = $this->classMapper->createInstance('user');
        return $instance->newQuery();
    }
}