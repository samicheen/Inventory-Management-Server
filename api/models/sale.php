<?php
class Sale {
  
    // database connection and table name
    private $conn;
    private $inventory_table = "inventory";
    private $sub_item_table = "sub_item";
    private $sales_table = "sales";
  
    // object properties
    public $id;
    public $item_name;
    public $grade;
    public $size;
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

    // Get inventory
    function getSales() {
        // select all query
        $query = "SELECT
                    sales.id,
                    ifnull(item_name, name) as item_name,
                    ifnull(inventory.grade, sub_item.grade) as grade,
                    ifnull(inventory.size, sub_item.size) as size,
                    party_name,
                    sales.quantity,
                    sales.unit,
                    selling_price,
                    sales.amount,
                    sales.timestamp
                FROM " . $this->sales_table .
                " LEFT JOIN " . $this->inventory_table .
                " ON sales.item_id = inventory.item_number
                LEFT JOIN ". $this->sub_item_table .
                " ON sales.item_id = sub_item.id";
        // prepare query statement
        $stmt = $this->conn->prepare($query);
        // execute query
        $stmt->execute();
        return $stmt;
    }
}