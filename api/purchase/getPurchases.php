<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// include database and object files
include_once '../config/database.php';
include_once '../models/purchase.php';
  
// instantiate database and product object
$database = new Database();
$db = $database->getConnection();
  
// initialize object
$purchase = new Purchase($db);

// query inventory
$stmt = $purchase->getPurchases();
$num = $stmt->rowCount();
// check if more than 0 record found
if($num>0){
  
    // inventory array
    $purchase_arr["purchases"]=array();
    $purchase_arr["alerts"]=array();
  
    // retrieve our table contents
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        // extract row
        // this will make $row['name'] to
        // just $name only
        extract($row);

        $purchase_item=array(
            "purchase_id" => $purchase_id,
            "invoice_id" => $invoice_id,
            "item" => array(
                "name" => $item_name,
                "size" => $size,
                "grade" => $grade
            ),
            "vendor" => array(
                "name" => $vendor_name
            ),
            "quantity" => array(
                "value" => $quantity,
                "unit" => $unit),
            "rate" => $rate,
            "amount" => $amount,
            "timestamp" => $timestamp . ' UTC'
        );
        array_push($purchase_arr["purchases"], $purchase_item);
    }

    $purchase_arr["total_amount"] = $purchase->getTotalAmount();
  
    // set response code - 200 OK
    http_response_code(200);
  
    // show invetory data in json format
    echo json_encode($purchase_arr);
} else{
  
    // set response code - 404 Not found
    http_response_code(404);
  
    // tell the user no records found
    echo json_encode(
        array("message" => "No records found.")
    );
}
?>