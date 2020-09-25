<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// include database and object files
include_once '../config/database.php';
include_once '../models/item.php';
  
// instantiate database and product object
$database = new Database();
$db = $database->getConnection();
  
// initialize object
$item = new Item($db);

// query sub item
$stmt = $item->getItems();
$num = $stmt->rowCount();
  
// check if more than 0 record found
if($num>0){
  
    // sub item array
    $item_arr["items"]=array();
    $item_arr["alerts"]=array();
  
    // retrieve our table contents
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        // extract row
        // this will make $row['name'] to
        // just $name only
        extract($row);
  
        $item=array(
            "item_id" => $item_id,
            "name" => $name,
            "grade" => $grade,
            "size" => $size,
        );
  
        array_push($item_arr["items"], $item);
    }
  
    // set response code - 200 OK
    http_response_code(200);
  
    // show invetory data in json format
    echo json_encode($item_arr);
} else{
  
    // set response code - 404 Not found
    http_response_code(404);
  
    // tell the user no records found
    echo json_encode(
        array("message" => "No records found.")
    );
}
?>