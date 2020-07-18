<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// include database and object files
include_once '../config/database.php';
include_once '../models/sub_item.php';
  
// instantiate database and product object
$database = new Database();
$db = $database->getConnection();
  
// initialize object
$sub_item = new SubItem($db);

$sub_item->item_id = isset($_GET['item_id']) ? $_GET['item_id'] : die();

// query sub item
$stmt = $sub_item->getSubItems();
$num = $stmt->rowCount();
  
// check if more than 0 record found
if($num>0){
  
    // sub item array
    $sub_item_arr["sub_items"]=array();
    $sub_item_arr["alerts"]=array();
  
    // retrieve our table contents
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        // extract row
        // this will make $row['name'] to
        // just $name only
        extract($row);
  
        $sub_item=array(
            "id" => $id,
            "item_id" => $item_id,
            "name" => $name,
            "size" => $size,
            "quantity" => array(
                "value" => $quantity,
                "unit" => $unit),
            "timestamp" => $timestamp
        );
  
        array_push($sub_item_arr["sub_items"], $sub_item);
    }
  
    // set response code - 200 OK
    http_response_code(200);
  
    // show invetory data in json format
    echo json_encode($sub_item_arr);
} else{
  
    // set response code - 404 Not found
    http_response_code(404);
  
    // tell the user no records found
    echo json_encode(
        array("message" => "No records found.")
    );
}
?>