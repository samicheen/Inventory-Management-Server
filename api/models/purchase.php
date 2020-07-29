
<?php
class Purchase {
  
    // database connection and table name
    private $conn;
    private $purchase_table = "purchase";
    private $item_table = "item";
    private $vendor_table = "vendor";
  
    // object properties
    public $purchase_id;
    public $invoice_id;
    public $item_id;
    public $item_name;
    public $size;
    public $grade;
    public $vendor_id;
    public $vendor_name;
    public $quantity;
    public $unit;
    public $rate;
    public $amount;
    public $timestamp;

  
    // constructor with $db as database connection
    public function __construct($db){
        $this->conn = $db;
    }

    // Get purchases
    function getPurchases() {
        // select all query
        $query = "SELECT
                    purchase_id,
                    invoice_id,
                    item.name as item_name,
                    size,
                    grade,
                    vendor.name as vendor_name,
                    quantity,
                    unit,
                    rate,
                    amount,
                    timestamp 
                FROM " . $this->purchase_table .
                " INNER JOIN " . $this->item_table .
                " ON purchase.item_id = item.item_id
                INNER JOIN " . $this->vendor_table .
                " ON purchase.vendor_id = vendor.vendor_id";
        // prepare query statement
        $stmt = $this->conn->prepare($query);
        // execute query
        $stmt->execute();
        return $stmt;
    }

    // Get total amount
    function getTotalAmount() {
        $total_query = "SELECT SUM(amount) total_amount
                        FROM ". $this->purchase_table;
        $total_stmt = $this->conn->prepare($total_query); 
        $total_stmt->execute();
        $total = $total_stmt->fetch();
        return $total["total_amount"];
    }

    // Add Purchase
    function addPurchase() {
         // query to insert record
        $query = "INSERT INTO
        " . $this->purchase_table . "
        SET
            invoice_id=:invoice_id,
            item_id=:item_id,
            vendor_id=:vendor_id,
            quantity=:quantity,
            unit=:unit,
            rate=:rate,
            amount=:amount,
            timestamp=sysdate()";

        // prepare query
        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->invoice_id = htmlspecialchars(strip_tags($this->invoice_id));
        $this->item_id = htmlspecialchars(strip_tags($this->item_id));
        $this->vendor_id = htmlspecialchars(strip_tags($this->vendor_id));
        $this->quantity = htmlspecialchars(strip_tags($this->quantity));
        $this->unit = htmlspecialchars(strip_tags($this->unit));
        $this->rate = htmlspecialchars(strip_tags($this->rate));
        $this->amount = htmlspecialchars(strip_tags($this->amount));

        // bind values
        $stmt->bindParam(":invoice_id", $this->invoice_id);
        $stmt->bindParam(":item_id", $this->item_id);
        $stmt->bindParam(":vendor_id", $this->vendor_id);
        $stmt->bindParam(":quantity", $this->quantity);
        $stmt->bindParam(":unit", $this->unit);
        $stmt->bindParam(":rate", $this->rate);
        $stmt->bindParam(":amount", $this->amount);

        // execute query
        if($stmt->execute()) {
            // returns last autoincrement id
            return $this->conn->lastInsertId();
        }

        return 0;
    }

     // update purchase
    function updatePurchase() {
  
        // update query
        $query = "UPDATE
                    " . $this->purchase_table . "
                SET
                    invoice_id=:invoice_id,
                    item_id=:item_id,
                    vendor_id=:vendor_id,
                    quantity=:quantity,
                    unit=:unit,
                    rate=:rate,
                    amount=:amount,
                    timestamp=sysdate()
                WHERE
                    purchase_id = :purchase_id";
    
        // prepare query statement
        $stmt = $this->conn->prepare($query);
    
        // sanitize
        $this->purchase_id=htmlspecialchars(strip_tags($this->purchase_id));
        $this->invoice_id = htmlspecialchars(strip_tags($this->invoice_id));
        $this->item_id = htmlspecialchars(strip_tags($this->item_id));
        $this->vendor_id = htmlspecialchars(strip_tags($this->vendor_id));
        $this->quantity = htmlspecialchars(strip_tags($this->quantity));
        $this->unit = htmlspecialchars(strip_tags($this->unit));
        $this->rate = htmlspecialchars(strip_tags($this->rate));
        $this->amount = htmlspecialchars(strip_tags($this->amount));
    
        // bind new values
        $stmt->bindParam(":purchase_id", $this->purchase_id);
        $stmt->bindParam(":invoice_id", $this->invoice_id);
        $stmt->bindParam(":item_id", $this->item_id);
        $stmt->bindParam(":vendor_id", $this->vendor_id);
        $stmt->bindParam(":quantity", $this->quantity);
        $stmt->bindParam(":unit", $this->unit);
        $stmt->bindParam(":rate", $this->rate);
        $stmt->bindParam(":amount", $this->amount);
    
        // execute the query
        if($stmt->execute()){
            return true;
        }
    
        return false;
    }


    // delete the product
    function removePurchase(){
        // delete query
        $query = "DELETE FROM " . $this->purchase_table . " WHERE purchase_id = ?";
        // prepare query
        $stmt = $this->conn->prepare($query);
        // sanitize
        $this->purchase_id = htmlspecialchars(strip_tags($this->purchase_id));
        // bind id of record to delete
        $stmt->bindParam(1, $this->purchase_id);
        // execute query
        if($stmt->execute()){
            return true;
        }
        return false;
    }
}
?>