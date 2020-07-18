<?php
class Customer {
  
    // database connection and table name
    private $conn;
    private $customer_table = "customer";
  
    // object properties
    public $customer_id;
    public $name;
  
    // constructor with $db as database connection
    public function __construct($db){
        $this->conn = $db;
    }

    // Add vendor
    function addCustomer() {
        // query to insert record
       $query = "INSERT INTO
       " . $this->customer_table . "
       SET
           name=:name
        ON DUPLICATE KEY UPDATE
        customer_id=LAST_INSERT_ID(customer_id)";

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