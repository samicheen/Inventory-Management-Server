<?php
class Item {
  
    // database connection and table name
    private $conn;
    private $item_table = "item";
  
    // object properties
    public $item_id;
    public $name;
    public $size;
    public $grade;
    public $is_sub_item;
  
    // constructor with $db as database connection
    public function __construct($db){
        $this->conn = $db;
    }

    // Get items
    function getItems() {
        // select all query
        $query = "SELECT
                    item_id,
                    name,
                    size,
                    grade
                FROM " . $this->item_table .
                " ORDER BY name";
        
        // prepare query statement
        $stmt = $this->conn->prepare($query);

        // execute query
        $stmt->execute();
        return $stmt;
    }

    // Add item
    function addItem() {
         // query to insert record
        $query = "INSERT INTO
        " . $this->item_table . "
        SET name=:name, 
            size=:size,
            grade=:grade,
            is_sub_item=:is_sub_item
        ON DUPLICATE KEY UPDATE
        item_id=LAST_INSERT_ID(item_id)";
        // prepare query
        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->size = htmlspecialchars(strip_tags($this->size));
        $this->grade = htmlspecialchars(strip_tags($this->grade));
        $this->is_sub_item = htmlspecialchars(strip_tags($this->is_sub_item));

        // bind values
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":size", $this->size);
        $stmt->bindParam(":grade", $this->grade);
        $stmt->bindParam(":is_sub_item", $this->is_sub_item);

        // execute query
        if($stmt->execute()) {
            return $this->conn->lastInsertId();
        } 

        return 0;
    }
}
?>