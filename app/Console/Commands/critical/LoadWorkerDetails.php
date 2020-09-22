<?php

namespace App\Console\Commands\Critical;

use App\Models\Users\WorkerDetail;
use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class LoadWorkerDetails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'run:loadworkerdetails';

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

        $users = User::all();

        foreach ($users as $user) {

            if (!$user->workerDetail) {

                WorkerDetail::create(['user_id' => $user->id, 'english_skill' => null, 'drivers_license' => null, 'right_to_work' => null]);

            }
        }

    }
}
