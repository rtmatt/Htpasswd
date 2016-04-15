<?php

namespace RTMatt\Htpasswd\Commands;

use Illuminate\Console\Command;
use RTMatt\Htpasswd\HtpasswdManager;

class HTPasswordDelete extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'htpasswd:delete';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove Htpassword';


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
        $manager = new HtpasswdManager($this);
        $manager->delete();

    }
}
