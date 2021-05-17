<?php

// if(!isset($_SESSION)) {
//     session_start();
// }

class Database
{
    private $DIR = DB."migrate";
    private $db_hostname = "localhost";
    private $db_username = "root";
    private $db_password = "";
    private $db_name = "meter";

    public function __construct()
    {
        $this->migrate('settings');
        $this->migrate('users');
        $this->migrate('wallets');
        $this->migrate('transactions');
    }

    public function connection()
    {
        $db_connection = mysqli_connect($this->db_hostname, $this->db_username, $this->db_password, $this->db_name) or die('Invalid Storage Entry!');
        return $db_connection;
    }

    public function migrate($sqlfile, $override=false)
    {
        $filename = !$override?$this->DIR.'/'.$sqlfile.".sql":$sqlfile;
        $file = fopen($filename, "r");
        if (!$file) { die(); }
        $filetext = fread($file, filesize($filename));
        fclose($file);
        $sqlQuery = mysqli_query($this->connection(), $filetext);
        return true;
    }
}

?>