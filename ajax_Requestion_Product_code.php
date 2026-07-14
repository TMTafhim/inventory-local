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
      //$search = ucfirst($_POST['search']);
         $search = strtoupper($_POST['search']);
    }



$resultoutput = $pdo->prepare("SELECT code FROM product_information WHERE code LIKE :search AND deleted_at IS NULL ORDER BY code ASC LIMIT 20");
$resultoutput->execute(array(':search' => '%' . $search . '%'));
	        while($result = $resultoutput->fetch()){	


		  $response[] = array("value"=>$result['code'],"label"=>$result['code']);
	}

    // encoding array to json format
    echo json_encode($response);
    exit;
}

if($request == 3){
    
    
    $userid = 0;
    if(isset($_POST['userid'])){
      $userid = $_POST['userid'];
    }
    
	$store_id=isset($_POST["store_id"]) ? (int)$_POST["store_id"] : 0;
	$resultoutput = $pdo->prepare("SELECT COALESCE(SUM(stock_information.stock),0) AS stock,product_information.id AS product_id,product_information.name AS product_name,product_information.code AS product_code,product_information.unit AS product_unit FROM product_information LEFT JOIN stock_information ON stock_information.product_id=product_information.id AND stock_information.store_id=:store_id AND stock_information.deleted_at IS NULL WHERE product_information.deleted_at IS NULL AND product_information.code=:product_code GROUP BY product_information.id,product_information.name,product_information.code,product_information.unit");
	$resultoutput->execute(array(':store_id'=>$store_id,':product_code'=>$userid));
  
    $users_arr = array();

    while( $row = $resultoutput->fetch()){
		$stock_product_id = $row['product_id'];
        $stock = $row['stock'];
        $product_name = $row['product_name'];
		$product_unit = $row['product_unit'];
        $users_arr[] = array( "available_quantity" => $stock,"product_id" => $stock_product_id,"product_name" => $product_name,"product_code" => $row['product_code'],"product_unit" => $product_unit);
    }

    // encoding array to json format
    echo json_encode($users_arr);
    exit;
}
