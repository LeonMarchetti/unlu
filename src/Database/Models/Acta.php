<?php

namespace UserFrosting\Sprinkle\Unlu\Database\Models;

use Illuminate\Database\Capsule\Manager as Capsule;
use UserFrosting\Sprinkle\Core\Database\Models\Model;

class Acta extends Model {
    /**
     * @var string The name of the table for the current model.
     */
    protected $table = 'acta';

    protected $fillable = [
        'ubicacion',
    ];
}