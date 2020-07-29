<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token");
header("HTTP/1.1 200 OK");
  
// include database and object files
include_once '../config/database.php';
include_once '../models/manufacture.php';
include_once '../models/inventory.php';
  
$database = new Database();
$db = $database->getConnection();

// initialize object
$item = new Manufacture($db);
$inventory = new Inventory($db);
  
// get posted data
$data = json_decode(file_get_contents("php://input"));

$add_item_response["manufacture_id"] = '';
$add_item_response["alerts"] = array();

// make sure data is not empty
if(
    !empty($data->item_id) &&
    !empty($data->quantity) &&
    !empty($data->quantity->value) &&
    !empty($data->quantity->unit)
){
    // set product property values
    $item->item_id = $data->item_id;
    $item->quantity = $data->quantity->value;
    $item->unit = $data->quantity->unit;

    $inventory->item_id = $data->item_id;
    $inventory->quantity = $data->quantity->value;

    $inventory_updated = $inventory->updateInventory();

    // create the product
    $manufacture_id = $item->addToManufacturing();
    if($manufacture_id && $inventory_updated){
        $add_item_response["manufacture_id"] = $manufacture_id;
        // set response code - 201 created
        http_response_code(201);
        // send response
        echo json_encode($add_item_response);
    }
  
    // if unable to create the product, tell the user
    else{
  
        // set response code - 503 service unavailable
        http_response_code(503);
  
        // tell the user
        echo json_encode(array("message" => "Unable to create product."));
    }
}
  
// tell the user data is incomplete
else{
  
    // set response code - 400 bad request
    http_response_code(400);
  
    // tell the user
    echo json_encode(array("message" => "Unable to create product. Data is incomplete."));
}
?>