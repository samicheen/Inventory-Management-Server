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
include_once '../models/sub_item.php';
  
$database = new Database();
$db = $database->getConnection();

// initialize object
$sub_item = new SubItem($db);
  
// get posted data
$data = json_decode(file_get_contents("php://input"));

// make sure data is not empty
if(
    !empty($data->item_id) &&
    !empty($data->name) &&
    !empty($data->size) &&
    !empty($data->quantity) &&
    !empty($data->quantity->value) &&
    !empty($data->quantity->unit) &&
    !empty($data->timestamp) &&
    !empty($data->id)
){
    // set product property values
    $sub_item->item_id = $data->item_id;
    $sub_item->name = $data->name;
    $sub_item->size = $data->size;
    $sub_item->quantity = $data->quantity->value;
    $sub_item->unit = $data->quantity->unit;
    $sub_item->timestamp = $data->timestamp;
    $sub_item->id = $data->id; 
  
    // update sub item
    if($sub_item->updateSubItem()) {
        // set response code - 200 ok
        http_response_code(200);
        // send response
        echo json_encode(array("message" => "Sub item was updated."));
    }
  
    // if unable to update the product, tell the user
    else{
  
        // set response code - 503 service unavailable
        http_response_code(503);
  
        // tell the user
        echo json_encode(array("message" => "Unable to update product."));
    }
}
  
// tell the user data is incomplete
else{
  
    // set response code - 400 bad request
    http_response_code(400);
  
    // tell the user
    echo json_encode(array("message" => "Unable to update product. Data is incomplete."));
}
?>