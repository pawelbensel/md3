<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMd3ColumnsForPropertiesTables extends Migration
{
    private $tables = [
        'prop_zips',
        'prop_year_builds',
        'prop_total_rooms',
        'prop_total_living_rooms',
        'prop_total_family_rooms',
        'prop_total_eat_in_kitchens',
        'prop_total_dining_rooms',
        'prop_total_bed_rooms',
        'prop_addresses',
        'prop_statuses',
        'prop_sold_prices',
        'prop_picture_urls',
        'prop_prices',
        'prop_on_markets',
        'prop_mls_private_numbers',
        'prop_mls_ids',
        'prop_mls_office_ids',
        'prop_agent_mls_ids',
        'prop_garages',
        'prop_descriptions',
        'prop_basements',
        'prop_square_feets',
    ];
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach ($this->tables as $tableName){
            Schema::table($tableName, function (Blueprint $table) {
                $table->string('source');
                $table->integer('checked')->default(1); /*numer of checked when compare data before update*/
                $table->integer('passed')->default(1); /*numer of passed tries when compare data before update*/
                $table->tinyInteger('matching_rate')->default(100); /*% value of match where add - default 100% when add new record*/
                $table->string('matched_by')->nullable(); /*fields used to match the record when add */
                $table->unsignedBigInteger('source_row_id'); /* source record id */
            });
        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        foreach ($this->tables as $tableName){
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropColumn('source');
                $table->dropColumn('checked'); /*numer of checked when compare data before update*/
                $table->dropColumn('passed'); /*numer of passed tries when compare data before update*/
                $table->dropColumn('matching_rate'); /*% value of match where add - default 100% when add new record*/
                $table->dropColumn('matched_by'); /*fields used to match the record when add */
                $table->dropColumn('source_row_id'); /* source record id */
            });
        }
    }
}
