<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// include database and object files
include_once '../config/database.php';
include_once '../models/inventory.php';
  
// instantiate database and product object
$database = new Database();
$db = $database->getConnection();
  
// initialize object
$inventory = new Inventory($db);

$item_id = $_GET['item_id'];

// query inventory
$stmt = $inventory->getInventory($item_id);
$num = $stmt->rowCount();
  
// check if more than 0 record found
if($num>0){
  
    // inventory array
    $inventory_arr["inventory"]=array();
    $inventory_arr["alerts"]=array();
  
    // retrieve our table contents
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        // extract row
        // this will make $row['name'] to
        // just $name only
        extract($row);
  
        $inventory_item=array(
            "inventory_id" => $inventory_id,
            "item_id" => $item_id,
            "name" => $name,
            "size" => $size,
            "grade" => $grade,
            "quantity" => array(
                "value" => $quantity,
                "unit" => $unit),
            "amount" => $amount,
            "timestamp" => $timestamp
            
        );
  
        array_push($inventory_arr["inventory"], $inventory_item);
    }
  
    // set response code - 200 OK
    http_response_code(200);
  
    // show invetory data in json format
    echo json_encode($inventory_arr);
} else{
  
    // set response code - 404 Not found
    http_response_code(404);
  
    // tell the user no records found
    echo json_encode(
        array("message" => "No records found.")
    );
}
?>