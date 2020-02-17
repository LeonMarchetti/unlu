<?php

namespace UserFrosting\Sprinkle\Unlu\Database\Migrations\v2020_02_17;

use UserFrosting\Sprinkle\Core\Database\Migration;
use Illuminate\Database\Schema\Blueprint;

class UpdatePeticionTable extends Migration {
    /**
     * {@inheritDoc}
     */
    public function up() {
        if ($this->schema->hasTable('peticion')) {
            $this->schema->table('peticion', function (Blueprint $table) {
                $table->renameColumn('ubicacion', 'acta');
            });
        }
    }

    /**
     * {@inheritDoc}
     */
    public function down() {
        if ($this->schema->hasTable("peticion")) {
            $this->schema->table("peticion", function(Blueprint $table) {
                $table->renameColumn('acta', 'ubicacion');
            });
        }
    }
}