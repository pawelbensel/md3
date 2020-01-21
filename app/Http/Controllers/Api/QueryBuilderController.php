<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Agent;
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
		

    	$queryBase = Agent::
            leftJoin('agent_first_names', 'agents.id', '=', 'agent_first_names.agent_id')
            ->leftJoin('agent_last_names', 'agents.id', '=', 'agent_last_names.agent_id')
            ->leftJoin('agent_office', 'agents.id', '=', 'agent_office.agent_id')
            ->leftJoin('offices', 'offices.id', '=', 'agent_office.office_id')
            ->leftJoin('office_names', 'offices.id', '=', 'office_names.office_id')
            ->leftJoin('agent_emails', 'agents.id', '=', 'agent_emails.agent_id');
            
        
    	$json = '{
		  	"logicalOperator": "all",
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

		$jsonObj = json_decode($json, true);
		//dd($jsonObj);
		$this->queryGeneratorService->getAnswers($queryBase, $jsonObj);
//		dd($queryBase->toSql());
		//dump($queryBase->get());
		return response()->json($queryBase->get(), 200);		


    }

}
