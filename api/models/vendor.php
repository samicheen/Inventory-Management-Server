<?php
class Vendor {
  
    // database connection and table name
    private $conn;
    private $vendor_table = "vendor";
  
    // object properties
    public $vendor_id;
    public $name;
  
    // constructor with $db as database connection
    public function __construct($db){
        $this->conn = $db;
    }

    // Add vendor
    function addVendor() {
        // query to insert record
       $query = "INSERT INTO
       " . $this->vendor_table . "
       SET
           name=:name
        ON DUPLICATE KEY UPDATE
        vendor_id=LAST_INSERT_ID(vendor_id)";

       // prepare query
       $stmt = $this->conn->prepare($query);

       // sanitize
       $this->name = htmlspecialchars(strip_tags($this->name));

       // bind values
       $stmt->bindParam(":name", $this->name);

       // execute query
       if($stmt->execute()) {
           // returns last autoincrement id
           return $this->conn->lastInsertId();
       }

       return 0;
   }
}
?>