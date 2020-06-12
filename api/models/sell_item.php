<?php
class SellItem {
  
    // database connection and table name
    private $conn;
    private $table_name = "sales";

    // object properties
    public $item_id;
    public $party_name;
    public $quantity;
    public $unit;
    public $selling_price;
    public $amount;
    public $timestamp;

    // constructor with $db as database connection
    public function __construct($db){
        $this->conn = $db;
    }

    // Sell Item
    function sellItem() {
        // query to insert record
       $query = "INSERT INTO
       " . $this->table_name . "
       SET
           item_id=:item_id,
           party_name=:party_name,
           quantity=:quantity,
           unit=:unit,
           selling_price=:selling_price,
           amount=:amount,
           timestamp=:timestamp";
       // prepare query
       $stmt = $this->conn->prepare($query);

       // sanitize
       $this->item_id = htmlspecialchars(strip_tags($this->item_id));
       $this->party_name = htmlspecialchars(strip_tags($this->party_name));
       $this->quantity = htmlspecialchars(strip_tags($this->quantity));
       $this->unit = htmlspecialchars(strip_tags($this->unit));
       $this->selling_price = htmlspecialchars(strip_tags($this->selling_price));
       $this->amount = htmlspecialchars(strip_tags($this->amount));
       $this->timestamp = htmlspecialchars(strip_tags($this->timestamp));

       // bind values
       $stmt->bindParam(":item_id", $this->item_id);
       $stmt->bindParam(":party_name", $this->party_name);
       $stmt->bindParam(":quantity", $this->quantity);
       $stmt->bindParam(":unit", $this->unit);
       $stmt->bindParam(":selling_price", $this->selling_price);
       $stmt->bindParam(":amount", $this->amount);
       $stmt->bindParam(":timestamp", $this->timestamp);
       try{
           $stmt->execute();
       } catch(Exception $ex){
           return $ex->getMessage();
       }
       return $this->conn->lastInsertId();
    }
}
?>