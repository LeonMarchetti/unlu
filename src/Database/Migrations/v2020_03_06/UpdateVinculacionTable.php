<?php

namespace UserFrosting\Sprinkle\Unlu\Database\Migrations\v2020_03_06;

use UserFrosting\Sprinkle\Core\Database\Migration;
use Illuminate\Database\Schema\Blueprint;

class UpdateVinculacionTable extends Migration {

    /**
     * {@inheritDoc}
     */
    public static $dependencies = [
        '\UserFrosting\Sprinkle\Unlu\Database\Migrations\v100\VinculacionTable'
    ];

    /**
     * {@inheritDoc}
     */
    public function up() {
        if ($this->schema->hasTable("vinculacion")) {
            $this->schema->table("vinculacion", function(Blueprint $table) {
                $table->string("actividad", 255)
                      ->default("")
                      ->change();
            });
        }
    }

    /**
     * {@inheritDoc}
     */
    public function down() {
        if ($this->schema->hasTable("vinculacion")) {
            $this->schema->table("vinculacion", function(Blueprint $table) {
                $table->string("actividad", 32)
                      ->change();
            });
        }
    }
}
