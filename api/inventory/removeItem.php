<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
  
// include database and object file
include_once '../config/database.php';
include_once '../models/item.php';
  
// get database connection
$database = new Database();
$db = $database->getConnection();
  
// prepare item object
$item = new Item($db);
  
// get item id
parse_str($_SERVER['QUERY_STRING'], $queries);
$itemNumber = $queries['itemNumber'];
  
// set item id to be deleted
$item->item_number = $itemNumber;
  
// delete the product
if($item->removeItem()) {
    // set response code - 200 ok
    http_response_code(200);
    // tell the user
    echo json_encode(array("message" => "Item was deleted."));
}
// if unable to delete the product
else {
    // set response code - 503 service unavailable
    http_response_code(503);
    // tell the user
    echo json_encode(array("message" => "Unable to delete item."));
}
?>