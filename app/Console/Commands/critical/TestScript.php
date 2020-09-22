<?php

namespace App\Console\Commands\Critical;

use App\Models\Users\WorkerDetail;
use App\User;
use DateTime;
use Illuminate\Console\Command;

class TestScript extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'run:test';

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

        $days = '365';
        $start_date = new DateTime(date("Y/m/d"));
        $end_date = new DateTime(date("Y/m/d",strtotime("+$days days")));
        $dd = date_diff($start_date,$end_date);
        echo "$dd->y year(s) $dd->m month(s) $dd->d day(s)";

    }
}
