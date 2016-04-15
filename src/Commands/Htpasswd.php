<?php

namespace RTMatt\Htpasswd\Commands;

use Illuminate\Console\Command;

class Htpasswd extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'htpasswd {name}';

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
        $this->generateProtection();
    }


    private function notify()
    {
        $this->info('Display this on the screen');
    }


    private function generateProtection()
    {
        $htpasswd_path = $this->generateHtpasswd();
        if ($htpasswd_path) {
            $this->generateHtaccess($htpasswd_path);
        }
    }


    private function removeProtection()
    {
        $this->info('removing htpwd');
    }


    private function htpasswdContents()
    {
        $name     = $this->argument('name');
        $password = crypt($name, base64_encode($name));
        $contents = $name . ':' . $password;

        return $contents;
    }


    private function htaccessContents($htpasswd_path)
    {

        $contents = "\r\nAuthType Basic";
        $contents .= "\r\nAuthName \"Demo Area - Password Protected\"";
        $contents .= "\r\nAuthUserFile " . $htpasswd_path;
        $contents .= "\r\nRequire valid-user";

        return $contents;
    }


    /**
     * @return string
     */
    private function generateHtpasswd()
    {

        if ($this->htpasswdExists()) {
            $this->info('prompt to approve overwrite');
            $approved = true;
        }
        $data          = $this->htpasswdContents();
        $htpasswd_path = base_path('.htpasswd');
        file_put_contents($htpasswd_path, $data);

        return $htpasswd_path;
    }


    /**
     * @param $htpasswd_path
     */
    private function generateHtaccess($htpasswd_path)
    {
        if ($this->checkHtAccess()) {
            $data2         = $this->htaccessContents($htpasswd_path);
            $htaccess_path = public_path('.htaccess');
            file_put_contents($htaccess_path, $data2, FILE_APPEND);
            $this->info('Password Protection Complete');
            return true;
        }
        return false;
    }


    private function htpasswdExists()
    {
        if(\File::exists(base_path('.htpasswd'))){
            $this->info('prompt user');
            return false;
            //true if they deny approval
        }
        return false;
    }


    private function htaccessHasAuth()
    {
        $contents = file_get_contents(public_path('.htaccess'));
        if (strpos($contents, 'Auth')) {
            return true;
        }

        return false;
    }


    private function checkHtAccess()
    {
        $flag     = false;
        $contents = file(public_path('.htaccess'));
        foreach ($contents as $index => $line) {
            if (strpos($line, 'Auth') === 0 || strpos($line, 'Require valid-user')=== 0) {
                unset( $contents[$index] );
                $flag = true;
                $this->info('match found - remember to ask if we should overrite');
            }
        }
        //@todo check flag and prompt user to override
        $contents = implode("",$contents);
        file_put_contents(public_path('.htaccess'), $contents);
        return true;
    }
}
