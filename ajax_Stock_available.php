<?php
include("BDB/DBConnEction.php");

$request = 0;
if(isset($_POST['request'])){
   $request = $_POST['request'];
}

// Get username list
// Get details
if($request == 2){
    $userid = 0;
    if(isset($_POST['userid'])){
      $userid = $_POST['userid'];
    }
   $store_id=''; 
if(isset($_POST['store_id'])){
      $store_id = ucfirst($_POST['store_id']);
    }

    $resultoutput = $pdo->query("SELECT stock_information.product_id AS product_id,stock_information.stock AS stock FROM stock_information INNER JOIN product_information ON product_information.id=stock_information.product_id WHERE product_information.name='$userid' and stock_information.store_id='$store_id'");
    $users_arr = array();

    while( $row = $resultoutput->fetch()){
		$product_id = $row['product_id'];
        $availablequantity = $row['stock'];

        $users_arr[] = array( "availablequantity" => $availablequantity,"product_id" => $product_id);
    }

    // encoding array to json format
    echo json_encode($users_arr);
    exit;
}


