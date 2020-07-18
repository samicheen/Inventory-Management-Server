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
include_once '../models/customer.php';
include_once '../models/sale.php';
  
$database = new Database();
$db = $database->getConnection();

// initialize object
$item = new Sale($db);
$customer = new Customer($db);
  
// get posted data
$data = json_decode(file_get_contents("php://input"));

$sell_item_response["sale_id"] = '';
$sell_item_response["alerts"] = array();

// make sure data is not empty
if (!empty($data->item_id) &&
    !empty($data->customer) &&
    !empty($data->customer->name) &&
    !empty($data->quantity) &&
    !empty($data->quantity->value) &&
    !empty($data->quantity->unit) &&
    !empty($data->selling_price) &&
    !empty($data->amount) &&
    !empty($data->timestamp)) {

        // set customer values
        $customer->name = $data->customer->name;

        $customer_id = $customer->addCustomer();

        // set sales property values
        $item->item_id = $data->item_id;
        $item->customer_id = $customer_id;
        $item->quantity = $data->quantity->value;
        $item->unit = $data->quantity->unit;
        $item->selling_price = $data->selling_price;
        $item->amount = $data->amount;
        $item->timestamp = $data->timestamp;
    
        // create the product
        $sale_id = $item->sellItem();
        if($sale_id) {
            $sell_item_response["sale_id"] = $sale_id;
            // set response code - 201 created
            http_response_code(201);
            // send response
            echo json_encode($sell_item_response);
        }
    
        // if unable to create the product, tell the user
        else {
    
            // set response code - 503 service unavailable
            http_response_code(503);
    
            // tell the user
            echo json_encode(array("message" => "Unable to add the sold product."));
        }
}
  
// tell the user data is incomplete
else {
  
    // set response code - 400 bad request
    http_response_code(400);
  
    // tell the user
    echo json_encode(array("message" => "Unable to sell product. Data is incomplete."));
}
?>