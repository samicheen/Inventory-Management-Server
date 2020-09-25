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
    public $quantity;
    public $unit;
    public $amount;
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
                    quantity,
                    unit,
                    amount,
                    timestamp
                FROM (SELECT item_id,
                             parent_item_id,
                             SUM(quantity) quantity,
                             unit,
                             SUM(amount) amount,
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

        $query = $query." ORDER BY name, size, grade";

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
    function getTotalAmount() {
        $total_query = "SELECT SUM(amount) total_amount
                        FROM ". $this->inventory_table;
        $total_stmt = $this->conn->prepare($total_query); 
        $total_stmt->execute();
        $total = $total_stmt->fetch();
        return $total["total_amount"];
    }

    // Add inventory
    function addInventory() {
         // query to insert record
        $query = "INSERT INTO
        " . $this->inventory_table . "
        SET
            item_id=:item_id,
            parent_item_id=:parent_item_id,
            quantity=:quantity,
            unit=:unit,
            rate=:rate,
            amount=:amount,
            update_timestamp=:timestamp,
            timestamp=:timestamp";

        // prepare query
        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->item_id = htmlspecialchars(strip_tags($this->item_id));
        $this->parent_item_id = htmlspecialchars(strip_tags($this->parent_item_id));
        $this->quantity = htmlspecialchars(strip_tags($this->quantity));
        $this->unit = htmlspecialchars(strip_tags($this->unit));
        $this->rate = htmlspecialchars(strip_tags($this->rate));
        $this->amount = htmlspecialchars(strip_tags($this->amount));
        $this->timestamp = htmlspecialchars(strip_tags($this->timestamp));

        // bind values
        $stmt->bindParam(":item_id", $this->item_id);
        if (empty($this->parent_item_id)) {
            $stmt->bindParam(":parent_item_id", $n=null, PDO::PARAM_NULL);
        } else {
            $stmt->bindParam(":parent_item_id", $this->parent_item_id);
        }
        $stmt->bindParam(":quantity", $this->quantity);
        $stmt->bindParam(":unit", $this->unit);
        $stmt->bindParam(":rate", $this->rate);
        $stmt->bindParam(":amount", $this->amount);
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
                sum(quantity) over(partition by item_id order by timestamp) sum_quantity
            from " . $this->inventory_table . " i
        ) n
            on  n.item_id = i.item_id
            and n.timestamp = i.timestamp
            and n.sum_quantity - i.quantity < :quantity
        set i.quantity = greatest(n.sum_quantity - :quantity, 0),
        i.amount = greatest(n.sum_quantity - :quantity, 0) * i.rate,
        i.update_timestamp = sysdate()
        where i.item_id = :item_id";

        // prepare query
        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->item_id = htmlspecialchars(strip_tags($this->item_id));
        $this->quantity = htmlspecialchars(strip_tags($this->quantity));
        //$this->timestamp = htmlspecialchars(strip_tags($this->timestamp));

        // bind values
        $stmt->bindParam(":item_id", $this->item_id);
        $stmt->bindParam(":quantity", $this->quantity);
        //$stmt->bindParam(":timestamp", $this->timestamp);

        // execute query
        if($stmt->execute()) {
            return true;
        }

        return false;
    }
}
?>