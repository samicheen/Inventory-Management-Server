<?php
class SubItem {
  
    // database connection and table name
    private $conn;
    private $table_name = "sub_item";
  
    // object properties
    public $id;
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

    // Get sub items
    function getSubItems() {
        // select all query
        $query = "SELECT
                    id, item_id, name, size, quantity, unit, timestamp
                FROM
                    " . $this->table_name . " WHERE item_id = ?";
        // prepare query statement
        $stmt = $this->conn->prepare($query);
        // bind item_id to get all related sub items
        $stmt->bindParam(1, $this->item_id);
        // execute query
        $stmt->execute();
        return $stmt;
    }

    // Add sub item
    function addSubItem() {
         // query to insert record
        $query = "INSERT INTO
        " . $this->table_name . "
        SET
            item_id=:item_id,
            name=:name,
            size=:size,
            grade=:grade,
            quantity=:quantity,
            unit=:unit,
            timestamp=:timestamp";

        // prepare query
        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->item_id = htmlspecialchars(strip_tags($this->item_id));
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->size = htmlspecialchars(strip_tags($this->size));
        $this->grade = htmlspecialchars(strip_tags($this->grade));
        $this->quantity = htmlspecialchars(strip_tags($this->quantity));
        $this->unit = htmlspecialchars(strip_tags($this->unit));
        $this->timestamp = htmlspecialchars(strip_tags($this->timestamp));

        // bind values
        $stmt->bindParam(":item_id", $this->item_id);
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":size", $this->size);
        $stmt->bindParam(":grade", $this->grade);
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

    // delete the product
    function removeSubItem(){
        // delete query
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        // prepare query
        $stmt = $this->conn->prepare($query);
        // sanitize
        $this->item_number = htmlspecialchars(strip_tags($this->id));
        // bind id of record to delete
        $stmt->bindParam(1, $this->id);
        // execute query
        if($stmt->execute()){
            return true;
        }
        return false;
    }

    // update the product
    function updateSubItem() {
        // query to insert record
       $query = "UPDATE
       " . $this->table_name . "
       SET
           item_id=:item_id,
           name=:name,
           size=:size,
           quantity=:quantity,
           unit=:unit,
           timestamp=:timestamp
        WHERE
            id=:id";

       // prepare query
       $stmt = $this->conn->prepare($query);

       // sanitize
       $this->item_id = htmlspecialchars(strip_tags($this->item_id));
       $this->name = htmlspecialchars(strip_tags($this->name));
       $this->size = htmlspecialchars(strip_tags($this->size));
       $this->quantity = htmlspecialchars(strip_tags($this->quantity));
       $this->unit = htmlspecialchars(strip_tags($this->unit));
       $this->timestamp = htmlspecialchars(strip_tags($this->timestamp));
       $this->id = htmlspecialchars(strip_tags($this->id));

       // bind values
       $stmt->bindParam(":item_id", $this->item_id);
       $stmt->bindParam(":name", $this->name);
       $stmt->bindParam(":size", $this->size);
       $stmt->bindParam(":quantity", $this->quantity);
       $stmt->bindParam(":unit", $this->unit);
       $stmt->bindParam(":timestamp", $this->timestamp);
       $stmt->bindParam(":id", $this->id);

       // execute query
       if($stmt->execute()) {
           return true;
       }

       return false;
   }
}
?>