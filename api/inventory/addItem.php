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
include_once '../models/item.php';
  
$database = new Database();
$db = $database->getConnection();

// initialize object
$item = new Item($db);
  
// get posted data
$data = json_decode(file_get_contents("php://input"));

$add_item_response["item_number"] = '';
$add_item_response["alerts"] = array();

// make sure data is not empty
if(
    !empty($data->item_name) &&
    !empty($data->size) &&
    !empty($data->quantity) &&
    !empty($data->quantity->value) &&
    !empty($data->quantity->unit) &&
    !empty($data->timestamp) &&
    !empty($data->invoice_number) &&
    !empty($data->vendor) &&
    !empty($data->grade) &&
    !empty($data->rate) &&
    !empty($data->amount)
){
    // set product property values
    $item->item_name = $data->item_name;
    $item->size = $data->size;
    $item->quantity = $data->quantity->value;
    $item->unit = $data->quantity->unit;
    $item->timestamp = $data->timestamp;
    $item->invoice_number = $data->invoice_number;
    $item->vendor = $data->vendor;
    $item->grade = $data->grade;
    $item->rate = $data->rate;
    $item->amount = $data->amount;
  
    // create the product
    $item_number = $item->addItem();
    if($item_number){
        $add_item_response["item_number"] = $item_number;
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