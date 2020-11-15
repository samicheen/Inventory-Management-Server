<?php
class Summary {
    // database connection and table name
    private $conn;
    private $item_table = "item";
    private $purchase_table = "purchase";
    private $inventory_table = "inventory";
    private $manufacture_table = "manufacture";
    private $sales_table = "sales";
    
    // constructor with $db as database connection
    public function __construct($db){
        $this->conn = $db;
    }

    function getSummary() {
            $query = "SELECT CONCAT(name, ' ', grade, ' (', size,' mm)') item_name,
                             IFNULL(purchase_qty, 0) purchase_qty,
                             IFNULL(opening_stock, 0) opening_stock,
                             IFNULL(closing_stock, 0) closing_stock,
                             IFNULL(man_qty, 0) man_qty,
                             IFNULL(sub_item_qty, 0) sub_item_qty,
                             IFNULL(sales_qty, 0) sales_qty,
                             IFNULL(sub_sales_qty, 0) sub_sales_qty,
                             inv.unit
                    FROM " . $this->item_table . " i
                    LEFT JOIN (SELECT item_id,
                                      SUM(quantity) purchase_qty
                                FROM " . $this->purchase_table . "
                                GROUP BY item_id) p
                    ON i.item_id = p.item_id 
                    RIGHT JOIN (SELECT item_id,
                                       SUM(opening_stock) opening_stock,
                                       SUM(closing_stock) closing_stock,
                                       unit
                                FROM " . $this->inventory_table . "
                                GROUP BY item_id) inv
                    ON i.item_id = inv.item_id
                    LEFT JOIN (SELECT item_id,
                                      SUM(quantity) man_qty
                                FROM " . $this->manufacture_table . "
                                GROUP BY item_id) m
                    ON i.item_id = m.item_id
                    LEFT JOIN (SELECT parent_item_id,
                                    SUM(closing_stock) sub_item_qty
                                FROM " . $this->inventory_table . "
                                GROUP BY parent_item_id) sub_inv
                    ON i.item_id = sub_inv.parent_item_id
                    LEFT JOIN (SELECT item_id,
                                      SUM(quantity) sales_qty
                                FROM " . $this->sales_table . "
                                GROUP BY item_id) s
                    ON i.item_id = s.item_id
                    LEFT JOIN (SELECT i.parent_item_id,
                                      SUM(s.quantity) sub_sales_qty
                                FROM " . $this->sales_table . " s
                                INNER JOIN (SELECT DISTINCT item_id,
                                                            parent_item_id 
                                            FROM " . $this->inventory_table . ") i
                                ON s.item_id = i.item_id
                                AND i.parent_item_id IS NOT NULL
                                GROUP BY i.parent_item_id) sub_s
                    ON i.item_id = sub_s.parent_item_id
                    ORDER BY name, grade, size";

            // prepare query statement
            $stmt = $this->conn->prepare($query);

            // execute query
            $stmt->execute();
            return $stmt;
    }
}
?>