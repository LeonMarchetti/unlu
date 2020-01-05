<?php

namespace UserFrosting\Sprinkle\Unlu\Database\Migrations\v500;

use UserFrosting\Sprinkle\Core\Database\Migration;
use Illuminate\Database\Schema\Blueprint;

class UpdateActaTable extends Migration {

    /**
     * {@inheritDoc}
     */
    public function up() {
        if ($this->schema->hasTable("acta")) {
            $this->schema->table("acta", function(Blueprint $table) {
                $table->date("fecha")->nullable();
                $table->string("titulo", 64)->nullable();
            });
        }
    }

    /**
     * {@inheritDoc}
     */
    public function down() {
        if ($this->schema->hasTable("acta")) {
            $this->schema->table("acta", function(Blueprint $table) {
                $table->dropColumn("fecha");
                $table->dropColumn("titulo");
            });
        }
    }
}