<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyKeyValueTOText extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('key_values', function (Blueprint $table) {
            $table->text('value')->change();
            $table->index(['key','owner_id','owner_type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('key_values', function (Blueprint $table) {            
            $table->string('value')->change();
        });
    }
}
