<?php

class PSS{
    private $_connection;
    private $_output;
    
    public function __construct($host, $port = 22, $hostkey = 'ssh-rsa'){
        if (!function_exists("ssh2_connect"))
            die("function ssh2_connect doesn't exist");
        
        if (!$this->_connection = ssh2_connect($host, $port, array('hostkey' => $hostkey)))
            die("FAIL: unable to connect to $host:$port");
    }
    
    public function auth_keyfile($username, $keyfilename, $passphrase = NULL){
        if (!ssh2_auth_pubkey_file(
                $this->_connection,
                $username,
                $keyfilename.'.pub',
                $keyfilename,
                $passphrase
            )
        ) die("FAIL: not authenticated");
    }
    
    public function exec($cmd){
        if (!($stream = ssh2_exec($this->_connection, $cmd ))) {
            echo $cmd."FAIL: unable to execute command";
        } else {
            // collect returning data from command
            stream_set_blocking($stream, true);
            $this->_output = "";
            while ($buf = fread($stream,4096)) {
                $this->_output .= $buf;
            }
            fclose($stream);
        }
    }

    public function getOutput(){
        return $this->_output;
    }
}

