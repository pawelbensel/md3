<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSoftDeletes extends Migration
{
    private $tablesToSoftDelete = [
        'agent_emails',
        'agent_first_names',
        'agent_last_names',
        'agent_license_numbers',
        'agent_mls_ids',
        'agent_office',
        'agent_phones',
        'agent_prop',
        'agent_titles',
        'agent_types',
        'agents',
        'key_values',
        'office_addresses',
        'office_company_names',
        'office_emails',
        'office_mls_ids',
        'office_msa_ids',
        'office_names',
        'office_phones',
        'office_prop',
        'office_states',
        'office_websites',
        'office_zips',
        'offices',
        'prop_addresses',
        'prop_agent_mls_ids',
        'prop_basements',
        'prop_descriptions',
        'prop_garages',
        'prop_l_dates',
        'prop_mls_ids',
        'prop_mls_office_ids',
        'prop_mls_private_numbers',
        'prop_on_markets',
        'prop_picture_urls',
        'prop_prices',
        'prop_square_feets',
        'prop_statuses',
        'prop_total_bed_rooms',
        'prop_total_dining_rooms',
        'prop_total_eat_in_kitchens',
        'prop_total_family_rooms',
        'prop_total_living_rooms',
        'prop_total_rooms',
        'prop_year_builds',
        'prop_zips',
        'props',
        'similars',
    ];
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach ($this->tablesToSoftDelete as $table){
            Schema::table($table, function(Blueprint $table){
                $table->softDeletes();
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
        foreach ($this->tablesToSoftDelete as $table){
            Schema::table($table, function(Blueprint $table){
                $table->dropSoftDeletes();
            });
        }
    }
}
