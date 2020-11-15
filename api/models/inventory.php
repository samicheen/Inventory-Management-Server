<?php
class Inventory {
  
    // database connection and table name
    private $conn;
    private $item_table = "item";
    private $map_table = "item_sub_item_map";
    private $inventory_table = "inventory";
    private $purchase_table = "purchase";
  
    // object properties
    public $purchase_id;
    public $item_id;
    public $parent_item_id;
    public $name;
    public $size;
    public $grade;
    public $opening_stock;
    public $closing_stock;
    public $unit;
    public $opening_amount;
    public $closing_amount;
    public $update_timestamp;
    public $timestamp;
  
    // constructor with $db as database connection
    public function __construct($db){
        $this->conn = $db;
    }

    // Get inventory
    function getInventory($parent_item_id) {
        // select all query
        $query = "SELECT
                    inv.item_id as item_id,
                    name,
                    size,
                    grade,
                    opening_stock,
                    closing_stock,
                    unit,
                    opening_amount,
                    closing_amount,
                    timestamp
                FROM (SELECT item_id,
                             parent_item_id,
                             SUM(opening_stock) opening_stock,
                             SUM(closing_stock) closing_stock,
                             unit,
                             SUM(opening_amount) opening_amount,
                             SUM(closing_amount) closing_amount,
                             MAX(update_timestamp) timestamp 
                      FROM ". $this->inventory_table . "
                      GROUP BY item_id, parent_item_id, unit) inv
                INNER JOIN " . $this->item_table . " i
                ON inv.item_id = i.item_id";

        if(empty($parent_item_id)) {
         $query = $query." WHERE parent_item_id is null";
        } else {
         // for sub items
         $query = $query." WHERE parent_item_id = :parent_item_id";
        }

        $query = $query." ORDER BY name, size, grade, timestamp";

        // prepare query statement
        $stmt = $this->conn->prepare($query);

        // sanitize
        $parent_item_id = htmlspecialchars(strip_tags($parent_item_id));

        // bind values
        $stmt->bindParam(":parent_item_id", $parent_item_id);
        
        // execute query
        $stmt->execute();
        return $stmt;
    }

    // Get total amount
    function getTotalAmounts() {
        $total_query = "SELECT SUM(opening_amount) opening_amount,
                               SUM(closing_amount) closing_amount
                        FROM ". $this->inventory_table;
        $total_stmt = $this->conn->prepare($total_query); 
        $total_stmt->execute();
        $total = $total_stmt->fetch();
        return array("opening_amount" => $total["opening_amount"], 
        "closing_amount" => $total["closing_amount"]);
    }

    // Add inventory
    function addInventory() {
         // query to insert record
        $query = "INSERT INTO
        " . $this->inventory_table . "
        SET
            item_id=:item_id,
            parent_item_id=:parent_item_id,
            opening_stock=:opening_stock,
            closing_stock=:closing_stock,
            unit=:unit,
            rate=:rate,
            opening_amount=:opening_amount,
            closing_amount=:closing_amount,
            update_timestamp=:timestamp,
            timestamp=:timestamp";

        // prepare query
        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->item_id = htmlspecialchars(strip_tags($this->item_id));
        $this->parent_item_id = htmlspecialchars(strip_tags($this->parent_item_id));
        $this->opening_stock = htmlspecialchars(strip_tags($this->opening_stock));
        $this->closing_stock = htmlspecialchars(strip_tags($this->closing_stock));
        $this->unit = htmlspecialchars(strip_tags($this->unit));
        $this->rate = htmlspecialchars(strip_tags($this->rate));
        $this->opening_amount = htmlspecialchars(strip_tags($this->opening_amount));
        $this->closing_amount = htmlspecialchars(strip_tags($this->closing_amount));
        $this->timestamp = htmlspecialchars(strip_tags($this->timestamp));

        // bind values
        $stmt->bindParam(":item_id", $this->item_id);
        if (empty($this->parent_item_id)) {
            $stmt->bindParam(":parent_item_id", $n=null, PDO::PARAM_NULL);
        } else {
            $stmt->bindParam(":parent_item_id", $this->parent_item_id);
        }
        $stmt->bindParam(":opening_stock", $this->opening_stock);
        $stmt->bindParam(":closing_stock", $this->closing_stock);
        $stmt->bindParam(":unit", $this->unit);
        $stmt->bindParam(":rate", $this->rate);
        $stmt->bindParam(":opening_amount", $this->opening_amount);
        $stmt->bindParam(":closing_amount", $this->closing_amount);
        $stmt->bindParam(":timestamp", $this->timestamp);

        // execute query
        if($stmt->execute()) {
            // returns last autoincrement id
            return $this->conn->lastInsertId();
        }

        return 0;
    }

    // Update inventory
    function updateInventory(){
        //query to update records
        $query = "update " . $this->inventory_table . " i
        inner join (
            select 
                i.*, 
                sum(closing_stock) over(partition by item_id order by timestamp) sum_closing_stock
            from " . $this->inventory_table . " i
        ) n
            on  n.item_id = i.item_id
            and n.timestamp = i.timestamp
            and n.sum_closing_stock - i.closing_stock < :quantity
        set i.closing_stock = greatest(n.sum_closing_stock - :quantity, 0),
        i.closing_amount = greatest(n.sum_closing_stock - :quantity, 0) * i.rate,
        i.update_timestamp = :update_timestamp
        where i.item_id = :item_id";

        // prepare query
        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->item_id = htmlspecialchars(strip_tags($this->item_id));
        $this->quantity = htmlspecialchars(strip_tags($this->closing_stock));
        //$this->timestamp = htmlspecialchars(strip_tags($this->timestamp));

        // bind values
        $stmt->bindParam(":item_id", $this->item_id);
        $stmt->bindParam(":quantity", $this->closing_stock);
        $stmt->bindParam(":update_timestamp", $this->update_timestamp);

        // execute query
        if($stmt->execute()) {
            return true;
        }

        return false;
    }
}
?>