<?php

namespace RTMatt\Htpasswd\Providers;

use Illuminate\Support\ServiceProvider;

class HtpasswdServiceProvider extends ServiceProvider
{

    protected $commands = [
        'RTMatt\Htpasswd\Commands\Htpasswd'
    ];


    /**
     * Register any package services.
     *
     * @return void
     */
    public function register(){
        $this->commands($this->commands);
    }
}