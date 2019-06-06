<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Kernel\acg\AcgFactory;
use Kernel\acg\AcgLaravel;

class AcgCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'acg {table}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $table = $this->argument('table');
        AcgFactory::run();
        AcgFactory::$app->laravel->run([
            "Table"=>$table,
            "controllerNamespace"=>"App\\Api\\Controllers\\V1",
            "modelNamespace"=>"App\\Models",
            "requestNamespace"=>"App\\Api\\Requests",
        ]);
    }
}
