<?php


namespace App\Services\Report\SQL;


class ExampleReportSql extends ReportSql
{
    protected $sql = "SELECT 
                        street_number,
                        street_direction,
                        street_name,
                        street_suffix,
                        street_post_direction,
                        street_unit,city,
                        state,
                        zip,
                        price,
                        mls_agent_id,
                        mls_number,
                        mls_office_id 
                    FROM :orgMls_prop 
                    WHERE status LIKE '%pend%' 
                            AND CAST(price AS UNSIGNED)> '75000'
                    ";
}
