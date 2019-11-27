<?php

namespace UserFrosting\Sprinkle\Unlu\Database\Migrations\v400;

use UserFrosting\Sprinkle\Core\Database\Migration;
use Illuminate\Database\Schema\Blueprint;

class UpdateUsuarioUnluTable extends Migration {

    /**
     * {@inheritDoc}
     */
    public static $dependencies = [
        '\UserFrosting\Sprinkle\Unlu\Database\Migrations\v100\UsuarioUnluTable'
    ];

    /**
     * {@inheritDoc}
     */
    public function up() {
        if ($this->schema->hasTable("usuario_unlu")) {
            $this->schema->table("usuario_unlu", function(Blueprint $table) {
                $table->boolean("activo")
                    ->default(false)
                    ->change();
            });
        }
    }

    /**
     * {@inheritDoc}
     */
    public function down() {
        if ($this->schema->hasTable("usuario_unlu")) {
            $this->schema->table("usuario_unlu", function(Blueprint $table) {
                $table->boolean("activo")
                    ->default(null)
                    ->change();
            });
        }
    }
}