<?php
include("BDB/DBConnEction.php");

$request = 0;
if(isset($_POST['request'])){
   $request = $_POST['request'];
}

// Get username list
if($request == 1){
    $search = "";
    if(isset($_POST['search'])){
      $search = ucfirst($_POST['search']);
    }
   $store_id=''; 
if(isset($_POST['store_id'])){
      $store_id = ucfirst($_POST['store_id']);
    }

$resultoutput = $pdo->query("SELECT product_information.name AS name,product_information.id AS id FROM product_information INNER JOIN stock_information ON product_information.id=stock_information.product_id WHERE stock_information.store_id='$store_id' and product_information.name like '%$search%' ORDER BY product_information.name ASC limit 0,20");
	        while($result = $resultoutput->fetch()){	


		  $response[] = array("value"=>$result['name'],"label"=>$result['name']);
	}

    echo json_encode($response);
    exit;
}

