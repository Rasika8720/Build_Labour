<?php

namespace App\Console\Commands\Critical;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DatabaseRerun extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'run:database';

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

        $confirm = readline("Are you sure you want to reset the DB? Please enter 'build_labour' to confirm: ");

        if (trim($confirm) != 'build_labour') {

            echo "\n";
            echo 'DB reset unsuccessful';
            exit;
        }

        echo 'You enter: '. $confirm . "\n";
        echo 'Restting DB.....';

        DB::statement('DROP DATABASE ' . env('DB_DATABASE'));
        DB::statement('CREATE DATABASE ' . env('DB_DATABASE'));

        sleep(5/700);
        echo shell_exec('php artisan migrate');
        echo shell_exec('php artisan db:seed');
        echo shell_exec('php artisan l5-swagger:generate');
    }
}
