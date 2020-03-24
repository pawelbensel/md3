<?php


namespace App\Console\Commands;


use App\Rules\MlsExists;
use App\Rules\ReportSourceSupported;
use App\Rules\ReportSqlExists;
use App\Rules\TargetTableExists;
use App\Services\Report\Destination\RetsReportDestination;
use App\Services\Report\Exceptions\MappingException;
use App\Services\Report\Exceptions\NumberOfColumnsException;
use App\Services\Report\Interfaces\Mappable;
use App\Services\Report\Interfaces\TableDestination;
use App\Services\Report\ReportService;
use App\Services\Report\Source\DatabaseReportSource;
use App\Services\Report\Source\ReportSourceFactory;
use App\Services\Report\Source\RetsReportSource;
use App\Services\Report\SQL\SqlFactory;
use App\Services\Source\RetsSourceService;
use Doctrine\DBAL\Query\QueryException;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

class ReportCommand extends Command
{
    protected $signature = 'report { source : Type data source. }                               
                                   { --targetTable= : Choose table where to generate report }
                                   { --sql= : Sql which will be used to generate report }
                                   { --mls=* : Specify which mlses use to report. Required only then source equals to rets }';

    protected $description = 'Generate flexible reports';

    public function handle()
    {
        $this->validateInput();
        $commandArguments = new CommandArguments($this->arguments(), $this->options());
        $source = ReportSourceFactory::factory($commandArguments);
        $sql = SqlFactory::factory($commandArguments);
        $destination = new RetsReportDestination();

        if($source instanceof DatabaseReportSource) {
            $source->setSql($sql);
        }
        if($source instanceof RetsReportSource) {
            $source->setMlses($this->option('mls'));
        }

        if($destination instanceof TableDestination) {

            $destination->setTable($this->option('targetTable'));
        }

        $reportService = new ReportService($source, $destination);
        $status = 1;
        try {
            $status = $reportService->generete();
        } catch (MappingException $e) {
            $this->error($e->getMessage());
        } catch (NumberOfColumnsException $e) {
            $this->error($e->getMessage());
        } catch (QueryException $e) {
            $this->error($e->getMessage());
        }
        $this->info('Report process exited with status: '.$status);
    }

    /**
     * @return bool
     */
    private function validateInput()
    {
        $validator = Validator::make([
            'source' => $this->argument('source'),
            'sql' => $this->option('sql'),
            'targetTable' => $this->option('targetTable'),
            'mls' => $this->option('mls'),
        ], [
            'source' => ['required', 'string', new ReportSourceSupported],
            'sql' => ['required', 'string', new ReportSqlExists],
            'targetTable' => ['required', 'string', new TargetTableExists],
            'mls' => ['required_if:source,rets',new MlsExists],
        ]);

        if ($validator->fails()) {
            $this->error('==== Could not create report ====');

            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }
            exit(1);
        }

        return true;
    }
}
