<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// include database and object files
include_once '../config/database.php';
include_once '../models/summary.php';
  
// instantiate database and product object
$database = new Database();
$db = $database->getConnection();
  
// initialize object
$summary = new Summary($db);

// query summary
$stmt = $summary->getSummary();
$num = $stmt->rowCount();
  
// check if more than 0 record found
if($num>0){
  
    // summary array
    $summary_arr["summary"]=array();
    $summary_arr["alerts"]=array();
  
    // retrieve our table contents
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        // extract row
        // this will make $row['name'] to
        // just $name only
        extract($row);

        $summary_item=array(
            "item_name" => $item_name,
            "purchase_qty" => $purchase_qty,
            "opening_stock" => $opening_stock,
            "closing_stock" => $closing_stock,
            "man_qty" => $man_qty,
            "sub_item_qty" => $sub_item_qty,
            "sales_qty" => $sales_qty,
            "sub_sales_qty" => $sub_sales_qty,
            "unit" => $unit
        );
  
        array_push($summary_arr["summary"], $summary_item);
    }

    // set response code - 200 OK
    http_response_code(200);
  
    // show invetory data in json format
    echo json_encode($summary_arr);
} else{
  
    // set response code - 404 Not found
    http_response_code(404);
  
    // tell the user no records found
    echo json_encode(
        array("message" => "No records found.")
    );
}
?>