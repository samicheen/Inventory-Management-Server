<?php
class Inventory{
  
    // database connection and table name
    private $conn;
    private $table_name = "inventory";
  
    // object properties
    public $barcode;
    public $item_name;
    public $size;
    public $quantity;
    public $timestamp;
  
    // constructor with $db as database connection
    public function __construct($db){
        $this->conn = $db;
    }

    // get inventory
    function getInventory(){
    
        // select all query
        $query = "SELECT
                    barcode, item_name, size, quantity, timestamp
                FROM
                    " . $this->table_name;
    
        // prepare query statement
        $stmt = $this->conn->prepare($query);
    
        // execute query
        $stmt->execute();
    
        return $stmt;
    }
}
?>