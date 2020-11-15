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
include_once '../models/vendor.php';
include_once '../models/purchase.php';
include_once '../models/inventory.php';
  
$database = new Database();
$db = $database->getConnection();

// initialize object
$item = new Item($db);
$vendor = new Vendor($db);
$purchase = new Purchase($db);
$inventory = new Inventory($db);
  
// get posted data
$data = json_decode(file_get_contents("php://input"));

$add_purchase_response["purchase_id"] = '';
$add_purchase_response["alerts"] = array();

// make sure data is not empty
if(
    !empty($data->invoice_id) &&
    !empty($data->item) &&
    !empty($data->item->item_id) &&
    !empty($data->vendor) &&
    !empty($data->vendor->name) &&
    !empty($data->quantity) &&
    !empty($data->quantity->value) &&
    !empty($data->quantity->unit) &&
    !empty($data->rate) &&
    !empty($data->amount) &&
    !empty($data->timestamp)
){
    // set vendor values
    $vendor->name = $data->vendor->name;
    
    // set purchase values
    $purchase->invoice_id = $data->invoice_id;
    $purchase->item_id = $data->item->item_id;
    $purchase->quantity = $data->quantity->value;
    $purchase->unit = $data->quantity->unit;
    $purchase->rate = $data->rate;
    $purchase->amount = $data->amount;
    $purchase->timestamp = $data->timestamp;

    // create the product
    $vendor_id = $vendor->addVendor();

    $purchase->vendor_id = $vendor_id;

    $purchase_id = $purchase->addPurchase();

    // set inventory values
    $inventory->item_id = $data->item->item_id;
    $inventory->closing_stock = $data->quantity->value;
    $inventory->unit = $data->quantity->unit;
    $inventory->rate = $data->rate;
    $inventory->closing_amount = $data->amount;
    $inventory->timestamp = $data->timestamp;

    if($vendor_id && $purchase_id){
        $inventory_id = $inventory->addInventory();
        if($inventory_id) {
            $add_purchase_response["purchase_id"] = $purchase_id;
            // set response code - 201 created
            http_response_code(201);
            // send response
            echo json_encode($add_purchase_response);
        }
    }
  
    // if unable to create the product, tell the user
    else{
  
        // set response code - 503 service unavailable
        http_response_code(503);
  
        // tell the user
        echo json_encode(array("message" => "Unable to add purchase."));
    }
}
  
// tell the user data is incomplete
else{
  
    // set response code - 400 bad request
    http_response_code(400);
  
    // tell the user
    echo json_encode(array("message" => "Unable to add purchase. Data is incomplete."));
}
?>