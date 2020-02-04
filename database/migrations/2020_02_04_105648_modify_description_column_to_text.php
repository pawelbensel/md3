<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ModifyDescriptionColumnToText extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        try{
            DB::statement('DROP INDEX prop_descriptions_description_index on prop_descriptions ');
        } catch (\Illuminate\Database\QueryException $e){
        }

        Schema::table('prop_descriptions', function (Blueprint $table) {
            $table->text('description')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('prop_descriptions', function (Blueprint $table) {
            $table->dropIndex('description');
            $table->string('description')->change();
        });
    }
}
