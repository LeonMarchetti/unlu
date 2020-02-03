<?php

namespace UserFrosting\Sprinkle\Unlu\Database\Migrations\v2020_02_03;

use UserFrosting\Sprinkle\Core\Database\Migration;
use Illuminate\Database\Schema\Blueprint;

class UpdateServicioTable extends Migration {
    /**
     * {@inheritDoc}
     */
    public function up() {
        if ($this->schema->hasTable('servicio')) {
            $this->schema->table('servicio', function (Blueprint $table) {
                $table->boolean('necesita_acta')
                      ->default(false);
            });
        }
    }

    /**
     * {@inheritDoc}
     */
    public function down() {
        if ($this->schema->hasTable("servicio")) {
            $this->schema->table("servicio", function(Blueprint $table) {
                $table->dropColumn("necesita_acta");
            });
        }
    }
}