<?php
class Item {
  
    // database connection and table name
    private $conn;
    private $table_name = "inventory";
  
    // object properties
    public $item_number;
    public $item_name;
    public $size;
    public $quantity;
    public $unit;
    public $timestamp;
    public $invoice_number;
    public $vendor;
    public $grade;
    public $rate;
    public $amount;
  
    // constructor with $db as database connection
    public function __construct($db){
        $this->conn = $db;
    }

    // Get inventory
    function getItems() {
        // select all query
        $query = "SELECT
                    item_number, item_name, size, quantity, unit, timestamp,
                    invoice_number, vendor, grade, rate, amount
                FROM
                    " . $this->table_name;
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
        " . $this->table_name . "
        SET
            item_name=:item_name,
            size=:size,
            quantity=:quantity,
            unit=:unit,
            timestamp=:timestamp,
            invoice_number=:invoice_number,
            vendor=:vendor, grade=:grade,
            rate=:rate,
            amount=:amount";

        // prepare query
        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->item_name = htmlspecialchars(strip_tags($this->item_name));
        $this->size = htmlspecialchars(strip_tags($this->size));
        $this->quantity = htmlspecialchars(strip_tags($this->quantity));
        $this->unit = htmlspecialchars(strip_tags($this->unit));
        $this->timestamp = htmlspecialchars(strip_tags($this->timestamp));
        $this->invoice_number = htmlspecialchars(strip_tags($this->invoice_number));
        $this->vendor = htmlspecialchars(strip_tags($this->vendor));
        $this->grade = htmlspecialchars(strip_tags($this->grade));
        $this->rate = htmlspecialchars(strip_tags($this->rate));
        $this->amount = htmlspecialchars(strip_tags($this->amount));

        // bind values
        $stmt->bindParam(":item_name", $this->item_name);
        $stmt->bindParam(":size", $this->size);
        $stmt->bindParam(":quantity", $this->quantity);
        $stmt->bindParam(":unit", $this->unit);
        $stmt->bindParam(":timestamp", $this->timestamp);
        $stmt->bindParam(":invoice_number", $this->invoice_number);
        $stmt->bindParam(":vendor", $this->vendor);
        $stmt->bindParam(":grade", $this->grade);
        $stmt->bindParam(":rate", $this->rate);
        $stmt->bindParam(":amount", $this->amount);

        // execute query
        if($stmt->execute()) {
            // returns last autoincrement id
            return $this->conn->lastInsertId();
        }

        return 0;
    }

    // update the product
    function updateItem() {
  
        // update query
        $query = "UPDATE
                    " . $this->table_name . "
                SET
                    item_name=:item_name,
                    size=:size,
                    quantity=:quantity,
                    unit=:unit,
                    timestamp=:timestamp,
                    invoice_number=:invoice_number,
                    vendor=:vendor, grade=:grade,
                    rate=:rate,
                    amount=:amount
                WHERE
                    item_number = :item_number";
    
        // prepare query statement
        $stmt = $this->conn->prepare($query);
    
        // sanitize
        $this->item_name = htmlspecialchars(strip_tags($this->item_name));
        $this->size = htmlspecialchars(strip_tags($this->size));
        $this->quantity = htmlspecialchars(strip_tags($this->quantity));
        $this->unit = htmlspecialchars(strip_tags($this->unit));
        $this->timestamp = htmlspecialchars(strip_tags($this->timestamp));
        $this->invoice_number = htmlspecialchars(strip_tags($this->invoice_number));
        $this->vendor = htmlspecialchars(strip_tags($this->vendor));
        $this->grade = htmlspecialchars(strip_tags($this->grade));
        $this->rate = htmlspecialchars(strip_tags($this->rate));
        $this->amount = htmlspecialchars(strip_tags($this->amount));
        $this->item_number=htmlspecialchars(strip_tags($this->item_number));
    
        // bind new values
        $stmt->bindParam(":item_name", $this->item_name);
        $stmt->bindParam(":size", $this->size);
        $stmt->bindParam(":quantity", $this->quantity);
        $stmt->bindParam(":unit", $this->unit);
        $stmt->bindParam(":timestamp", $this->timestamp);
        $stmt->bindParam(":invoice_number", $this->invoice_number);
        $stmt->bindParam(":vendor", $this->vendor);
        $stmt->bindParam(":grade", $this->grade);
        $stmt->bindParam(":rate", $this->rate);
        $stmt->bindParam(":amount", $this->amount);
        $stmt->bindParam(':item_number', $this->item_number);
    
        // execute the query
        if($stmt->execute()){
            return true;
        }
    
        return false;
    }


    // delete the product
    function removeItem(){
        // delete query
        $query = "DELETE FROM " . $this->table_name . " WHERE item_number = ?";
        // prepare query
        $stmt = $this->conn->prepare($query);
        // sanitize
        $this->item_number = htmlspecialchars(strip_tags($this->item_number));
        // bind id of record to delete
        $stmt->bindParam(1, $this->item_number);
        // execute query
        if($stmt->execute()){
            return true;
        }
        return false;
    }
}
?>