<?php

class dbValues{
    private $host;
    private $username;
    private $password;
    private $database;
    private static $dbValues;

    private function __construct()
    {
        $dbFiles = fopen("db.conf", "r") or die('no');
        $settings = array();
        while (!feof($dbFiles))
            $settings[] = fgets($dbFiles);
        for($i = 0; $i < count($settings); $i++){
            $elem = explode(" ", $settings[$i]);
            switch ($elem[0]){
                case 'hostname:':
                    $this->host = preg_replace('/\s+/', "", $elem[1]);
                    break;
                case 'username:':
                    $this->username = preg_replace('/\s+/', "", $elem[1]);
                    break;
                case 'password:':
                    $this->password = preg_replace('/\s+/', "", $elem[1]);
                    break;
                case 'db_name:':
                    $this->database = preg_replace('/\s+/', "", $elem[1]);
                    break;
            }
        }
    }

    public static function getInstance(){
        if (self::$dbValues == null){
            $c = __CLASS__;
            self::$dbValues = new $c;
        }
        return self::$dbValues;
    }

    public function getHost(){return $this->host;}
    public function getUser(){return $this->username;}
    public function getPass(){return $this->password;}
    public function getDb(){return $this->database;}
}