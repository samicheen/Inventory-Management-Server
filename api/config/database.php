<?php
class Database{
  
    private $host = "194.59.164.106:3306";
    private $db_name = "u447373500_vinayakashot";
    private $username = "u447373500_vinayakashot";
    private $password = "SAM#yash27";
    public $conn;
  
    // get the database connection
    public function getConnection(){
  
        $this->conn = null;
  
        try{
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8");
        }catch(PDOException $exception){
            echo "Connection error: " . $exception->getMessage();
        }
  
        return $this->conn;
    }
}
?>