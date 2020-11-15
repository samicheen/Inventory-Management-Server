<?php
class Item {
  
    // database connection and table name
    private $conn;
    private $item_table = "item";
    private $map_table = "item_sub_item_map";
  
    // object properties
    public $item_id;
    public $name;
    public $size;
    public $grade;
  
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

    function getSubItems() {
        $query = "SELECT 
                    b.item_id,
                    b.name,
                    b.grade,
                    b.size
        FROM " . $this->item_table . " a
        INNER JOIN " . $this->map_table ." map
        ON a.item_id = map.item_id
        INNER JOIN " . $this->item_table . " b
        ON b.item_id = map.sub_item_id
        WHERE a.item_id = ?";

        // prepare query statement
        $stmt = $this->conn->prepare($query);

        // bind item_id to get all related sub items
        $stmt->bindParam(1, $this->item_id);

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
            grade=:grade
        ON DUPLICATE KEY UPDATE
        item_id=LAST_INSERT_ID(item_id)";
        // prepare query
        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->size = htmlspecialchars(strip_tags($this->size));
        $this->grade = htmlspecialchars(strip_tags($this->grade));

        // bind values
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":size", $this->size);
        $stmt->bindParam(":grade", $this->grade);

        // execute query
        if($stmt->execute()) {
            // returns last autoincrement id and rows affected
            // $item_number = $this->conn->lastInsertId();
            // $affected_rows = $stmt->rowCount();
            // $is_map = true;
            // // If inserted or sub item then add into mapping
            // if($affected_rows || $this->item_id){
            //     $is_map = $this->addMapping($item_number);
            // }
            // if($item_number && $is_map){
            //     return $item_number;
            // }
            return $this->conn->lastInsertId();
        } 

        return 0;
    }

    // Add item sub_item mapping
    function addMapping($parent_item_id, $item_id) {
        // query to insert record
        $query = "INSERT INTO
        " . $this->map_table . "
        SET
            item_id=:item_id,
            sub_item_id=:sub_item_id";

        // prepare query
        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->item_id = htmlspecialchars(strip_tags($this->item_id));

        // bind values
        // if(empty($this->item_id)){
        //     $stmt->bindParam(":item_id", $item_number);
        //     $stmt->bindParam(":sub_item_id", $n=null, PDO::PARAM_NULL);
        // } else{
        //     $stmt->bindParam(":item_id", $this->item_id);
        //     $stmt->bindParam(":sub_item_id", $item_number); 
        // }
        $stmt->bindParam(":item_id", $parent_item_id);
        $stmt->bindParam(":sub_item_id", $item_id);

        // execute query
        if($stmt->execute()) {
            return true;
        }

        return false;
    }
}
?>