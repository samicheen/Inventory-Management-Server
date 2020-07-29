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
    !empty($data->item->name) &&
    !empty($data->item->size) &&
    !empty($data->item->grade) &&
    !empty($data->quantity) &&
    !empty($data->quantity->value) &&
    !empty($data->quantity->unit) &&
    !empty($data->rate) &&
    !empty($data->amount)
){
    //set item values
    $item->item_id = $data->item->item_id;
    $item->name = $data->item->name;
    $item->size = $data->item->size;
    $item->grade = $data->item->grade;

    $sub_item_id = $item->addItem();

    //set manufaturing values
    $manufacture->item_id = $data->item->item_id;
    $manufacture->quantity = $data->quantity->value * -1;
    $manufacture->unit = $data->quantity->unit;

    $manufacturingUpdated = $manufacture->addToManufacturing();

    // set product property values
    $inventory->item_id = $sub_item_id;
    $inventory->quantity = $data->quantity->value;
    $inventory->unit = $data->quantity->unit;
    $inventory->rate = $data->rate;
    $inventory->amount = $data->amount;
  
    // create the product
    $inventory_id = $inventory->addInventory();
    if($sub_item_id && $manufacturingUpdated && $inventory_id){
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