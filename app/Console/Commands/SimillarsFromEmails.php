<?php


namespace App\Console\Commands;


use App\Models\Agent;
use App\Models\Similar;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SimillarsFromEmails extends Command
{
    private $mergeService;

    protected $signature = 'simi';

    protected $description = 'Merge two object in MegaData in once.';



    public function handle()
    {
        $offset = 0;
        $limit = 1000;

        $sql = "
            SELECT email,
                count(DISTINCT agent_id) as qty,
                group_concat(DISTINCT agent_id) as agent
            FROM md3_prod.agent_emails
            GROUP BY email
            HAVING qty>1 AND qty<8
            LIMIT $offset, $limit
        ";

        while($emails = DB::connection('mysql')->select(DB::raw($sql))){
            echo "Offset: $offset".PHP_EOL;
            foreach ($emails as $email)
            {
                echo "Getting created_at for $email->agent".PHP_EOL;
                $created_at = (array)DB::connection('mysql')->select(DB::raw(
                    "SELECT id, created_at FROM agents WHERE id IN ($email->agent)"));
                // array of objects to array of array
                $j = json_encode($created_at);
                $created_at = json_decode($j,true);
                // sort by date
                usort($created_at, function($a1, $a2){
                    $t1 = strtotime($a1['created_at']);
                    $t2 = strtotime($a2['created_at']);
                    return $t1 - $t2;
                });

                echo "Similars creation starts".PHP_EOL;
                for ($i=0; $i<count($created_at); $i++)
                {
                    if(!isset($created_at[$i+1])){
                        break;
                    }
                    $similar = new Similar();
                    $similar->object_id  = $created_at[$i]['id'];
                    $similar->object_type = Agent::class;
                    $similar->similar_id = $created_at[$i+1]['id'];
                    $similar->similar_type = Agent::class;
                    $similar->matched_by = 'email';
                    $similar->matching_rate = 40;
                    $similar->save();
                }
                echo "Similars creation ends".PHP_EOL;
            }
            $offset += $limit;
            $sql = "
            SELECT email,
                count(DISTINCT agent_id) as qty,
                group_concat(DISTINCT agent_id) as agent
            FROM md3_prod.agent_emails
            GROUP BY email
            HAVING qty>1 AND qty<8
            LIMIT $offset, $limit
        ";
            echo "====== GETTING NEW PORTION ======".PHP_EOL;
        }

    }

}
