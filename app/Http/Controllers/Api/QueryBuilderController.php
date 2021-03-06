<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Agent;
use App\Models\Prop;
use Illuminate\Support\Facades\DB;

//use App\Services\QueryGeneratorService;
//use ;

class QueryBuilderController extends Controller
{
    private $queryGeneratorService;

    public function __construct()
    {
	    $this->queryGeneratorService = new \App\Services\QueryGeneratorService();   
    }

    public function build(Request $request)
    {
		//dd($request->all());
    	$userQuery = $request->get('query');
    	$marker = $request->get('marker');
    	$queryBase = Agent::
            leftJoin('agent_first_names', 'agents.id', '=', 'agent_first_names.agent_id')
            ->leftJoin('agent_last_names', 'agents.id', '=', 'agent_last_names.agent_id')
            ->leftJoin('agent_office', 'agents.id', '=', 'agent_office.agent_id')
            ->leftJoin('agent_mls_ids', 'agents.id', '=', 'agent_mls_ids.agent_id')

            ->leftJoin('offices', 'offices.id', '=', 'agent_office.office_id')
            ->leftJoin('office_names', 'offices.id', '=', 'office_names.office_id')
            ->leftJoin('office_states', 'offices.id', '=', 'office_states.office_id')
            ->leftJoin('office_emails', 'offices.id', '=', 'office_emails.office_id')
            ->leftJoin('office_phones', 'offices.id', '=', 'office_phones.office_id')
            ->leftJoin('office_addresses', 'offices.id', '=', 'office_addresses.office_id')
            ->leftJoin('office_mls_ids', 'offices.id', '=', 'office_mls_ids.office_id')
            ->leftJoin('agent_emails', 'agents.id', '=', 'agent_emails.agent_id')
            ->leftJoin('agent_phones', 'agents.id', '=', 'agent_phones.agent_id')
            ->leftJoin('agent_types', 'agents.id', '=', 'agent_types.agent_id')
            ->leftJoin('agent_prop', 'agents.id', '=', 'agent_prop.agent_id')
            ->leftJoin('props', 'props.id', '=', 'agent_prop.prop_id')
            ->leftJoin('prop_prices', 'props.id', '=', 'prop_prices.prop_id')
            ->leftJoin('prop_zips', 'props.id', '=', 'prop_zips.prop_id')
            ->leftJoin('prop_statuses', 'props.id', '=', 'prop_statuses.prop_id')
            ->leftJoin('prop_addresses', 'props.id', '=', 'prop_addresses.prop_id')
            ->leftJoin('prop_square_feets', 'props.id', '=', 'prop_square_feets.prop_id')
            ->leftJoin('prop_year_builds', 'props.id', '=', 'prop_year_builds.prop_id');

        if ($marker=='agents') {
        	$queryBase->select(DB::raw("
        		agents.id as agent_id,
        		offices.id as office_id,
        		group_concat(distinct(agent_first_names.first_name)) as first_name,
        		group_concat(distinct(agent_last_names.last_name)) as last_name,
        		group_concat(distinct(agent_emails.email)) as agent_email,
                group_concat(distinct(agent_phones.phone)) as agent_phone,
                group_concat(distinct(agent_types.type)) as agent_type,
                group_concat(distinct(concat(agent_mls_ids.mls_id,'(',agent_mls_ids.mls_name, ')'))) as agent_mls_id,
        		group_concat(distinct(office_names.name)) as office_name, 
        		group_concat(distinct(office_emails.email)) as office_email, 
        		group_concat(distinct(office_phones.phone)) as office_phone,
        		group_concat(distinct(concat(office_addresses.address1,' ',office_addresses.address2,' ', office_addresses.city ))) as office_address,
                group_concat(distinct(concat(office_mls_ids.mls_id,'(',office_mls_ids.mls_name, ')'))) as office_mls_id, 
                count(distinct(agent_prop.prop_id)) as number_of_props"

            ));
        	$queryBase->groupBy(['agents.id','offices.id']);

        } 
        if ($marker=='offices') 
        {
			$queryBase->select(DB::raw("
				offices.id as office_id,        		
        		group_concat(distinct(office_names.name)) as office_name, 
        		group_concat(distinct(office_emails.email)) as office_email, 
        		group_concat(distinct(office_phones.phone)) as office_phone,
        		group_concat(distinct(concat(office_addresses.address1,' ',office_addresses.address2,' ', office_addresses.city ))) as office_address,
                group_concat(distinct(concat(office_mls_ids.mls_id,'(',office_mls_ids.mls_name, ')'))) as office_mls_id,
        		count(distinct(agent_office.agent_id)) as number_of_agents,
                count(distinct(agent_prop.prop_id)) as number_of_props
        		"));
				
		
        	$queryBase->groupBy('offices.id');        	
        } 

        if ($marker=='listings') 
        {

            $queryBase = Prop::
            leftJoin('agent_prop', 'props.id', '=', 'agent_prop.prop_id')
            ->leftJoin('agents', 'agents.id', '=', 'agent_prop.agent_id')
            ->leftJoin('agent_first_names', 'agents.id', '=', 'agent_first_names.agent_id')
            ->leftJoin('agent_last_names', 'agents.id', '=', 'agent_last_names.agent_id')
            ->leftJoin('agent_mls_ids', 'agents.id', '=', 'agent_mls_ids.agent_id')
            ->leftJoin('agent_office', 'agents.id', '=', 'agent_office.agent_id')
            ->leftJoin('offices', 'offices.id', '=', 'agent_office.office_id')
            ->leftJoin('office_names', 'offices.id', '=', 'office_names.office_id')
            ->leftJoin('office_mls_ids', 'offices.id', '=', 'office_mls_ids.office_id')
            /*->leftJoin('office_states', 'offices.id', '=', 'office_states.office_id')
            ->leftJoin('office_emails', 'offices.id', '=', 'office_emails.office_id')
            ->leftJoin('office_phones', 'offices.id', '=', 'office_phones.office_id')
            ->leftJoin('office_addresses', 'offices.id', '=', 'office_addresses.office_id')
            ->leftJoin('agent_emails', 'agents.id', '=', 'agent_emails.agent_id')
            
            ->leftJoin('props', 'props.id', '=', 'agent_prop.prop_id')*/
            ->leftJoin('prop_prices', 'props.id', '=', 'prop_prices.prop_id')
            ->leftJoin('prop_statuses', 'props.id', '=', 'prop_statuses.prop_id')
            ->leftJoin('prop_addresses', 'props.id', '=', 'prop_addresses.prop_id')
            ->leftJoin('prop_mls_ids', 'props.id', '=', 'prop_mls_ids.prop_id')
            ->leftJoin('prop_square_feets', 'props.id', '=', 'prop_square_feets.prop_id')
            ->leftJoin('prop_zips', 'props.id', '=', 'prop_zips.prop_id')
            ->leftJoin('prop_year_builds', 'props.id', '=', 'prop_year_builds.prop_id');
        

        //group_concat(distinct(prop_year_builds.year_build)) as year_build,
            
            $queryBase->select(DB::raw("
                props.id,                                
                group_concat(distinct(prop_addresses.county)) as county,
                group_concat(distinct(prop_addresses.city)) as prop_city, 
                group_concat(distinct(prop_zips.zip)) as zip,                
                group_concat(distinct(prop_addresses.street_name)) as street_name,
                group_concat(distinct(prop_addresses.street_number)) as street_number,                
                group_concat(distinct(prop_year_builds.year_build)) as year_build, 
                group_concat(distinct(prop_square_feets.square_feet)) as square_feet,                
                group_concat(distinct(concat(prop_mls_ids.mls_id,'(',prop_mls_ids.mls_name, ')'))) as prop_mls_id,
                group_concat(distinct(concat(prop_statuses.status))) as prop_status,
                group_concat(distinct(office_names.name)) as office_name, 
                group_concat(distinct(concat(office_mls_ids.mls_id,'(',office_mls_ids.mls_name, ')'))) as office_mls_id,
                group_concat(distinct(agent_first_names.first_name)) as agent_first_name,
                group_concat(distinct(agent_last_names.last_name)) as agent_last_name,
                group_concat(distinct(concat(agent_mls_ids.mls_id,'(',agent_mls_ids.mls_name, ')'))) as agent_mls_id
                "));
            $queryBase->groupBy('props.id');             

            //$queryBase->whereRaw('props.id is not null');
            //$queryBase->whereRaw('prop_addresses.prop_id is not null');
            //dd($queryBase->toSql());

        

        }
        
        
    	$json = '{
		  	"logicalOperator": "any",
		  	"children": [
		    {
		      "type": "query-builder-rule",
		      "query": {
		        "rule": "first_name",
		        "operator": "contains",
		        "operand": "Vegetable",
		        "value": "John"
		      }
		    },
		    
		    {
		      "type": "query-builder-rule",
		      "query": {
		        "rule": "office_names.name",
		        "operator": "contains",
		        "operand": "Vegetable",
		        "value": "Exit"
		      }
		    }
		  ]
		}';

			
		$this->queryGeneratorService->getAnswers($queryBase, $userQuery);
		//echo $queryBase->toSql();

		//dump($queryBase->get());
		return response()->json($queryBase->get(), 200);		


    }

}
