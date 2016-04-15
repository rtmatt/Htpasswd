<?php

namespace RTMatt\Htpasswd;

use Illuminate\Console\Command;

class HtpasswdManager
{

    private $htpasswd_path;

    private $htpasswd_exists = false;


    function __construct(Command $command)
    {
        $this->command       = $command;
        $this->htpasswd_path = base_path('.htpasswd');
        $this->htaccess_path = public_path('.htaccess');
        if (\File::exists($this->htpasswd_path)) {
            $this->htpasswd_exists = true;
        }
        $this->htaccess_has_auth = $this->checkHtaccess();
    }


    public function create()
    {
        $htpassword_created = $this->generateHtpasswd();
        $htaccess_has_auth  = $this->htaccess_has_auth;
        //if password isn't created (preexits) and htaccess configured, do nothing
        if ( ! $htpassword_created && $htaccess_has_auth) {
            return $this->command->info('No password created.  Htaccess Auth already configured. Done.');
        }

        //if pwd generated and .htaccess  configured, overwrite it
        if ($htpassword_created && $htaccess_has_auth) {
            return $this->overwriteHtaccess();
        }

        //All Remaining cases...
        //if file generated and htaccess not configured, configure it
        //if file preexists and htaccess not configured, configure it
        return $this->writeHtaccess();

    }


    public function delete(){
        $this->removeHTPasswordFile();
        $this->removeAuthFromHtaccess();
        $this->command->info('Done');
    }


    /**
     * @return string
     */
    public function generateHtpasswd()
    {

        if ($this->htpasswd_exists) {
            if ($this->command->confirm('Password file exists.  Overwrite? [y|N]')) {
                $this->command->info('Overwriting password file.');

                return $this->createHtpasswdFile();
            }
            $this->command->info('Using Existing password file.');
            return false;
        }

        return $this->createHtpasswdFile();
    }


    protected function readHtAccessLines($match_function = null)
    {
        $match    = false;
        $resave   = false;
        $contents = file($this->htaccess_path);
        //  var_dump($match_function);
        foreach ($contents as $index => $line) {
            if (strpos($line, 'Auth') === 0 || strpos($line, 'Require valid-user') === 0) {
                // unset( $contents[$index] );
                if (is_callable($match_function)) {
                    $result = $match_function($index, $contents);
                    if ($contents != $result) {
                        $contents = $result;
                        $resave   = true;
                    }
                }
                $match = true;
                //$this->command->info('match found - remember to ask if we should overrite');
            }
        }
        if ($resave) {
            $this->writeFullHtaccessFile($contents);
        }

        return $match;
    }


    private function checkHtaccess($match_function = false)
    {
        return $this->readHtAccessLines();
    }


    /**
     * @return string
     */
    private function createHtpasswdFile()
    {
        $data = $this->htpasswdContents();
        file_put_contents($this->htpasswd_path, $data);
        $this->command->info('Password file created.');

        return $this->htpasswd_path;
    }


    private function overwriteHtaccess()
    {

        $this->removeAuthFromHtaccess();
        $this->addAuthToHtAccess();
        $this->command->info('Done.');
    }


    public function removeAuthFromHtaccess()
    {
        $this->command->info('Removing existing Auth from .htaccess.');
        $this->readHtAccessLines(function ($index, $array) {
            //if(array_key_exists($index-2,$array)){
            //    var_dump(trim($array[$index-2]));
            //}
            //Remove extra newlines
            if (array_key_exists($index - 1, $array) && array_key_exists($index - 2,
                    $array) && ( trim($array[$index - 1]) == "" )
            ) {
                unset( $array[$index - 1] );
            }
            unset( $array[$index] );

            return $array;
        });
    }


    /**
     * @param $contents
     */
    protected function writeFullHtaccessFile($contents)
    {
        //$this->command->info('Writing .htaccess');
        $contents = implode("", $contents);
        file_put_contents(public_path('.htaccess'), $contents);
    }


    private function addAuthToHtAccess()
    {
        $this->command->info('Adding new Auth to .htaccess');
        $contents = $this->htaccessAuthContents();
        file_put_contents($this->htaccess_path, $contents, FILE_APPEND);
    }


    private function htaccessAuthContents()
    {

        $contents = "\r\nAuthType Basic";
        $contents .= "\r\nAuthName \"Demo Area - Password Protected\"";
        $contents .= "\r\nAuthUserFile " . $this->htpasswd_path;
        $contents .= "\r\nRequire valid-user";

        return $contents;
    }


    private function writeHtaccess()
    {
        $this->addAuthToHtAccess();
        $this->command->info('Done');
    }


    private function htpasswdContents()
    {
        $name     = $this->command->argument('name');
        $password = crypt($name, base64_encode($name));
        $contents = $name . ':' . $password;

        return $contents;
    }


    private function removeHTPasswordFile()
    {

        if(file_exists($this->htpasswd_path)){
            unlink($this->htpasswd_path);
            $this->command->info('Password file deleted');
            return true;
        }
        return false;

    }

}