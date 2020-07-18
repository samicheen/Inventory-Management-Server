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
include_once '../models/vendor.php';
  
$database = new Database();
$db = $database->getConnection();

// initialize object
$vendor = new Vendor($db);
  
// get posted data
$data = json_decode(file_get_contents("php://input"));

$add_vendor_response["vendor_id"] = '';
$add_vendor_response["alerts"] = array();

// make sure data is not empty
if(
    !empty($data->name)
){
    // set product property values
    $vendor->name = $data->name;

    // create the product
    $vendor_id = $vendor->addVendor();
    if($vendor_id){
        $add_vendor_response["vendor_id"] = $vendor_id;
        // set response code - 201 created
        http_response_code(201);
        // send response
        echo json_encode($add_vendor_response);
    }
  
    // if unable to create the product, tell the user
    else{
  
        // set response code - 503 service unavailable
        http_response_code(503);
  
        // tell the user
        echo json_encode(array("message" => "Unable to add vendor."));
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