<?php
include("BDB/DBConnEction.php");

$request = 0;
if(isset($_POST['request'])){
   $request = $_POST['request'];
}

// Get username list


if($request == 3){
    
    
    $userid = 0;
    if(isset($_POST['userid'])){
      $userid = $_POST['userid'];
    }
    
$resultoutput = $pdo->query("SELECT product_information.id AS product_id FROM 	product_information  WHERE  product_information.deleted_at is NULL and product_information.name= '$userid'");    
  
    $users_arr = array();

    while( $row = $resultoutput->fetch()){
		$stock_product_id = $row['product_id'];
        $users_arr[] = array( "product_id" => $stock_product_id);
    }

    // encoding array to json format
    echo json_encode($users_arr);
    exit;
}