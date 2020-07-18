<?php
class Manufacture {
  
    // database connection and table name
    private $conn;
    private $manufacture_table = "manufacture";
    private $item_table = "item";
  
    // object properties
    public $manufacture_id;
    public $item_id;
    public $name;
    public $size;
    public $grade;
    public $quantity;
    public $timestamp;
  
    // constructor with $db as database connection
    public function __construct($db){
        $this->conn = $db;
    }

    // Get items in manufacturing
    function getManufacturingItems() {
        // select all query
        $query = "SELECT
                    manufacture_id,
                    ". $this->manufacture_table . ".item_id as item_id,
                    name,
                    size,
                    grade,
                    quantity,
                    unit,
                    timestamp
                FROM
                    " . $this->manufacture_table . "
                     INNER JOIN " . $this->item_table . "
                     ON ". $this->manufacture_table .".item_id = " . $this->item_table . ".item_id";
        // prepare query statement
        $stmt = $this->conn->prepare($query);
        // execute query
        $stmt->execute();
        return $stmt;
    }

    // Send items for manufacturing
    function addToManufacturing() {
         // query to insert record
        $query = "INSERT INTO
        " . $this->manufacture_table . "
        SET item_id=:item_id, 
            quantity=:quantity,
            unit=:unit,
            timestamp=:timestamp
        ON DUPLICATE KEY UPDATE
        manufacture_id=LAST_INSERT_ID(manufacture_id),
        quantity=quantity+VALUES(quantity)";
        // prepare query
        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->item_id = htmlspecialchars(strip_tags($this->item_id));
        $this->quantity = htmlspecialchars(strip_tags($this->quantity));
        $this->unit = htmlspecialchars(strip_tags($this->unit));
        $this->timestamp = htmlspecialchars(strip_tags($this->timestamp));

        // bind values
        $stmt->bindParam(":item_id", $this->item_id);
        $stmt->bindParam(":quantity", $this->quantity);
        $stmt->bindParam(":unit", $this->unit);
        $stmt->bindParam(":timestamp", $this->timestamp);

        // execute query
        if($stmt->execute()) {
            // returns last autoincrement id
            return $this->conn->lastInsertId();
        } 

        return 0;
    }
}
?>