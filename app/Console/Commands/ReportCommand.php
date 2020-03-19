<?php


namespace App\Console\Commands;


use App\Rules\ReportSourceSupported;
use App\Rules\ReportSqlExists;
use App\Services\Report\Source\ReportSourceFactory;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

class ReportCommand extends Command
{
    protected $signature = 'report { source : Type data source. }                               
                                   { --targetTable= : Choose table where to generate report }
                                   { --sql= : Sql which will be used to generate report }';

    protected $description = 'Generate flexible reports';

    public function handle()
    {
        $this->validateInput();
        $commandArguments = new CommandArguments($this->arguments(), $this->options());
        $source = ReportSourceFactory::factory($commandArguments);


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
        ], [
            'source' => ['required', 'string', new ReportSourceSupported ],
            'sql' => ['required', 'string', new ReportSqlExists ],
            'targetTable' => ['required'],
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
