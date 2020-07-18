<?php
class Inventory {
  
    // database connection and table name
    private $conn;
    private $item_table = "item";
    private $map_table = "item_sub_item_map";
    private $inventory_table = "inventory";
  
    // object properties
    public $inventory_id;
    public $item_id;
    public $name;
    public $size;
    public $grade;
    public $quantity;
    public $unit;
    public $amount;
    public $timestamp;
  
    // constructor with $db as database connection
    public function __construct($db){
        $this->conn = $db;
    }

    // Get inventory
    function getInventory($item_id) {
        // select all query
        $query = "SELECT
                    inventory_id,
                    " . $this->inventory_table . ".item_id as item_id,
                    name,
                    size,
                    grade,
                    quantity,
                    unit,
                    amount,
                    timestamp
                FROM
                    " . $this->inventory_table .
                    " INNER JOIN " . $this->item_table .
                    " ON inventory.item_id = item.item_id
                    INNER JOIN " . $this->map_table . " map";

        if(empty($item_id)) {
         $query = $query." ON item.item_id = map.item_id WHERE map.sub_item_id is null";
        } else {
         // for sub items
         $query = $query." ON item.item_id = map.sub_item_id WHERE map.item_id=:item_id";
        }

        // prepare query statement
        $stmt = $this->conn->prepare($query);

        // sanitize
        $item_id = htmlspecialchars(strip_tags($item_id));

        // bind values
        $stmt->bindParam(":item_id", $item_id);
        
        // execute query
        $stmt->execute();
        return $stmt;
    }

    // Add inventory
    function addInventory() {
         // query to insert record
        $query = "INSERT INTO
        " . $this->inventory_table . "
        SET
            item_id=:item_id,
            quantity=:quantity,
            unit=:unit,
            amount=:amount,
            timestamp=:timestamp
        ON DUPLICATE KEY UPDATE
        inventory_id=LAST_INSERT_ID(inventory_id),
        quantity=quantity+VALUES(quantity),
        amount=amount+VALUES(amount)";

        // prepare query
        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->item_id = htmlspecialchars(strip_tags($this->item_id));
        $this->quantity = htmlspecialchars(strip_tags($this->quantity));
        $this->unit = htmlspecialchars(strip_tags($this->unit));
        $this->amount = htmlspecialchars(strip_tags($this->amount));
        $this->timestamp = htmlspecialchars(strip_tags($this->timestamp));

        // bind values
        $stmt->bindParam(":item_id", $this->item_id);
        $stmt->bindParam(":quantity", $this->quantity);
        $stmt->bindParam(":unit", $this->unit);
        $stmt->bindParam(":amount", $this->amount);
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