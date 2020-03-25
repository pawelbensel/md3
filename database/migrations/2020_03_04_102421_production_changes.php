<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ProductionChanges extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('prop_l_dates', function (Blueprint $table){
           $table->string('source');
           $table->foreign('prop_id')->references('id')->on('props');
        });

        Schema::table('prop_picture_urls', function (Blueprint $table){
            $table->dropIndex('prop_picture_urls_picture_url_index');
        });

        Schema::table('prop_picture_urls', function (Blueprint $table){
            $table->text('picture_url')->change();
        });

        Schema::table('source_rows', function(Blueprint $table){
           $table->index('source', 'source_rows_source');
        });

        Schema::table('source_objects', function(Blueprint $table){
            $table->index('source', 'source_objects_source_index');
        });

    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('source_objects', function(Blueprint $table){
            $table->dropIndex( 'source_objects_source_index');
        });

        Schema::table('source_rows', function(Blueprint $table){
            $table->dropIndex( 'source_rows_source');
        });

        Schema::table('prop_picture_urls', function (Blueprint $table){
            $table->string('picture_url')->change();
        });

        Schema::table('prop_l_dates', function (Blueprint $table){
            $table->dropForeign('prop_l_dates_prop_id_foreign');
            $table->dropColumn('source');
        });
    }
}
