<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// include database and object files
include_once '../config/database.php';
include_once '../models/manufacture.php';
  
// instantiate database and product object
$database = new Database();
$db = $database->getConnection();
  
// initialize object
$item = new Manufacture($db);

// query manufacturing
$stmt = $item->getManufacturingItems();
$num = $stmt->rowCount();
  
// check if more than 0 record found
if($num>0){
  
    // manufacture array
    $manufacture_arr["manufactures"]=array();
    $inventory_arr["alerts"]=array();
  
    // retrieve our table contents
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        // extract row
        // this will make $row['name'] to
        // just $name only
        extract($row);
  
        $manufacture_item=array(
            "manufacture_id" => $manufacture_id,
            "item" => array(
                "item_id" => $item_id,
                "name" => $name,
                "size" => $size,
                "grade" => $grade),
            "quantity" => array(
                "value" => $quantity,
                "unit" => $unit),
            "timestamp" => $timestamp
            
        );
  
        array_push($manufacture_arr["manufactures"], $manufacture_item);
    }
  
    // set response code - 200 OK
    http_response_code(200);
  
    // show invetory data in json format
    echo json_encode($manufacture_arr);
} else{
  
    // set response code - 404 Not found
    http_response_code(404);
  
    // tell the user no records found
    echo json_encode(
        array("message" => "No records found.")
    );
}
?>