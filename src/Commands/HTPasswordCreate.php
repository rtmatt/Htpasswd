<?php

namespace RTMatt\Htpasswd\Commands;

use Illuminate\Console\Command;

class HTPasswordCreate extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'htpasswd:make {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Htpassword';


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
        $manager = new \RTMatt\Htpasswd\HtpasswdManager($this);
        $manager->create();

    }
}
