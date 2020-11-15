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
include_once '../models/inventory.php';
include_once '../models/manufacture.php';
include_once '../models/item.php';
  
$database = new Database();
$db = $database->getConnection();

// initialize object
$item = new Item($db);
$manufacture = new Manufacture($db);
$inventory = new Inventory($db);
  
// get posted data
$data = json_decode(file_get_contents("php://input"));

$add_inventory_response["inventory_id"] = '';
$add_inventory_response["alerts"] = array();

// make sure data is not empty
if(
    !empty($data->item) &&
    !empty($data->item->item_id) &&
    !empty($data->closing_stock) &&
    !empty($data->closing_stock->value) &&
    !empty($data->closing_stock->unit) &&
    !empty($data->rate) &&
    !empty($data->closing_amount) &&
    !empty($data->timestamp)
){
    $success = 1;

    // update manufacturing only if it is subitem
    if (!empty($data->item->parent_item_id)) {

        // add mapping
        $success = $item->addMapping($data->item->parent_item_id, $data->item->item_id);

        //set manufaturing values
        $manufacture->item_id = $data->item->parent_item_id;
        $manufacture->quantity = $data->closing_stock->value * -1;
        $manufacture->unit = $data->quantity->unit;
        $manufacture->timestamp = $data->timestamp;

        $success = $manufacture->addToManufacturing();
    }

    // set product property values
    $inventory->item_id = $data->item->item_id;
    $inventory->parent_item_id = $data->item->parent_item_id;
    $inventory->opening_stock = $data->opening_stock->value;
    $inventory->closing_stock = $data->closing_stock->value;
    $inventory->unit = $data->closing_stock->unit;
    $inventory->rate = $data->rate;
    $inventory->opening_amount = $data->opening_amount;
    $inventory->closing_amount = $data->closing_amount;
    $inventory->timestamp = $data->timestamp;
  
    // create the product
    $inventory_id = $inventory->addInventory();
    if($success && $inventory_id){
        $add_inventory_response["inventory_id"] = $inventory_id;
        // set response code - 201 created
        http_response_code(201);
        // send response
        echo json_encode($add_inventory_response);
    }
  
    // if unable to create the product, tell the user
    else{
  
        // set response code - 503 service unavailable
        http_response_code(503);
  
        // tell the user
        echo json_encode(array("message" => "Unable to add inventory."));
    }
}
  
// tell the user data is incomplete
else{
  
    // set response code - 400 bad request
    http_response_code(400);
  
    // tell the user
    echo json_encode(array("message" => "Unable to add inventory. Data is incomplete."));
}
?>