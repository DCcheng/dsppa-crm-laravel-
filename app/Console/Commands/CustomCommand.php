<?php

namespace App\Console\Commands;

use App\Models\Custom;
use Illuminate\Console\Command;

class CustomCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:customCommand';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'command:customCommand';

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
        $this->putOnHighSeas();
    }

    public function putOnHighSeas()
    {
        $timestamp = time() - config("webconfig._putOnHighSeasSecond");
        Custom::whereRaw("follow_up_time <= $timestamp")->update(["uid" => 0, "in_high_seas" => 1]);
    }


}
