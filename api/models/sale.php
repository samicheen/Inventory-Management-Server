<?php
class Sale {
  
    // database connection and table name
    private $conn;
    private $customer_table = "customer";
    private $item_table = "item";
    private $sales_table = "sales";
  
    // object properties
    public $sale_id;
    public $item_id;
    public $item_name;
    public $size;
    public $grade;
    public $customer_id;
    public $customer_name;
    public $quantity;
    public $unit;
    public $selling_price;
    public $amount;
    public $timestamp;
  
    // constructor with $db as database connection
    public function __construct($db){
        $this->conn = $db;
    }

    // Get inventory
    function getSales() {
        // select all query
        $query = "SELECT
                    sale_id,
                    item.item_id as item_id,
                    item.name as item_name,
                    size,
                    grade,
                    customer.customer_id as customer_id,
                    customer.name as customer_name,
                    quantity,
                    unit,
                    selling_price,
                    amount,
                    timestamp
                FROM " . $this->sales_table .
                " INNER JOIN " . $this->item_table .
                " ON " . $this->sales_table . ".item_id = " . $this->item_table . ".item_id
                INNER JOIN " . $this->customer_table .
                " ON " . $this->sales_table . ".customer_id = " . $this->customer_table . ".customer_id";
        // prepare query statement
        $stmt = $this->conn->prepare($query);
        // execute query
        $stmt->execute();
        return $stmt;
    }

    // Sell Item
    function sellItem() {
        // query to insert record
       $query = "INSERT INTO
       " . $this->sales_table . "
       SET
           item_id=:item_id,
           customer_id=:customer_id,
           quantity=:quantity,
           unit=:unit,
           selling_price=:selling_price,
           amount=:amount,
           timestamp=:timestamp";
       // prepare query
       $stmt = $this->conn->prepare($query);

       // sanitize
       $this->item_id = htmlspecialchars(strip_tags($this->item_id));
       $this->customer_id = htmlspecialchars(strip_tags($this->customer_id));
       $this->quantity = htmlspecialchars(strip_tags($this->quantity));
       $this->unit = htmlspecialchars(strip_tags($this->unit));
       $this->selling_price = htmlspecialchars(strip_tags($this->selling_price));
       $this->amount = htmlspecialchars(strip_tags($this->amount));
       $this->timestamp = htmlspecialchars(strip_tags($this->timestamp));

       // bind values
       $stmt->bindParam(":item_id", $this->item_id);
       $stmt->bindParam(":customer_id", $this->customer_id);
       $stmt->bindParam(":quantity", $this->quantity);
       $stmt->bindParam(":unit", $this->unit);
       $stmt->bindParam(":selling_price", $this->selling_price);
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