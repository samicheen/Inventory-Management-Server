<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// include database and object files
include_once '../config/database.php';
include_once '../models/sale.php';
  
// instantiate database and product object
$database = new Database();
$db = $database->getConnection();
  
// initialize object
$sale = new Sale($db);

// query inventory
$stmt = $sale->getSales();
$num = $stmt->rowCount();
  
// check if more than 0 record found
if($num>0){
  
    // inventory array
    $sales_arr["sales"]=array();
    $sales_arr["alerts"]=array();
  
    // retrieve our table contents
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        // extract row
        // this will make $row['name'] to
        // just $name only
        extract($row);
  
        $sale_item=array(
            "sale_id" => $sale_id,
            "item" => array(
                "id" => $item_id,
                "name" => $item_name,
                "grade" => $grade,
                "size" => $size),
            "customer" => array(
                "id" => $customer_id,
                "name" => $customer_name),
            "quantity" => array(
                "value" => $quantity,
                "unit" => $unit),
            "selling_price" => $selling_price,
            "amount" => $amount,
            "timestamp" => $timestamp
        );
  
        array_push($sales_arr["sales"], $sale_item);
    }
  
    // set response code - 200 OK
    http_response_code(200);
  
    // show invetory data in json format
    echo json_encode($sales_arr);
} else{
  
    // set response code - 404 Not found
    http_response_code(404);
  
    // tell the user no records found
    echo json_encode(
        array("message" => "No records found.")
    );
}
?>