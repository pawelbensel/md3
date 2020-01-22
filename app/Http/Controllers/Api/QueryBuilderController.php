<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Agent;
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
		
    	$userQuery = $request->get('query');
    	$marker = $request->get('marker');
    	$queryBase = Agent::
            leftJoin('agent_first_names', 'agents.id', '=', 'agent_first_names.agent_id')
            ->leftJoin('agent_last_names', 'agents.id', '=', 'agent_last_names.agent_id')
            ->leftJoin('agent_office', 'agents.id', '=', 'agent_office.agent_id')
            ->leftJoin('offices', 'offices.id', '=', 'agent_office.office_id')
            ->leftJoin('office_names', 'offices.id', '=', 'office_names.office_id')
            ->leftJoin('office_states', 'offices.id', '=', 'office_states.office_id')
            ->leftJoin('office_emails', 'offices.id', '=', 'office_emails.office_id')
            ->leftJoin('office_phones', 'offices.id', '=', 'office_phones.office_id')
            ->leftJoin('office_addresses', 'offices.id', '=', 'office_addresses.office_id')
            ->leftJoin('agent_emails', 'agents.id', '=', 'agent_emails.agent_id');
        if ($marker=='agents') {
        	$queryBase->select(DB::raw("
        		agents.id as agent_id,
        		offices.id as office_id,
        		group_concat(distinct(agent_first_names.first_name)) as first_name,
        		group_concat(distinct(agent_last_names.last_name)) as last_name,
        		group_concat(distinct(agent_emails.email)) as email,
        		group_concat(distinct(office_names.name)) as office_name, 
        		group_concat(distinct(office_emails.email)) as office_email, 
        		group_concat(distinct(office_phones.phone)) as office_phone,
        		group_concat(distinct(concat(office_addresses.address1,' ',office_addresses.address2,' ', office_addresses.city ))) as office_address"));
        	$queryBase->groupBy(['agents.id','offices.id']);

        } else {
			$queryBase->select(DB::raw("
				offices.id as office_id,
        		group_concat(distinct(office_names.name)) as office_name, 
        		group_concat(distinct(office_names.name)) as office_name, 
        		group_concat(distinct(office_emails.email)) as office_email, 
        		group_concat(distinct(office_phones.phone)) as office_phone,
        		group_concat(distinct(concat(office_addresses.address1,' ',office_addresses.address2,' ', office_addresses.city ))) as office_address,
        		count(agent_office.agent_id) as number_of_agents

        		"));
				
		
        	$queryBase->groupBy('offices.id');        	
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
		//dd($queryBase->toSql());
		//dump($queryBase->get());
		return response()->json($queryBase->get(), 200);		


    }

}
