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



$resultoutput = $pdo->query("SELECT * FROM product_information WHERE  name like '%$search%' ORDER BY name ASC limit 0,20");
	        while($result = $resultoutput->fetch()){	


		  $response[] = array("value"=>$result['name'],"label"=>$result['name']);
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
    
    $project_id=$_POST["project_id"];
$resultoutput = $pdo->query("SELECT SUM(distribution_quantity) AS distribution_quantity,product_id FROM 	distribution_history INNER JOIN product_information ON distribution_history.product_id=product_information.id WHERE project_id='$project_id' and product_information.deleted_at is NULL and product_information.name like '%$userid%'");    
  
    $users_arr = array();

    while( $row = $resultoutput->fetch()){
		$stock_product_id = $row['product_id'];
        $distribution_quantity = $row['distribution_quantity'];
        $users_arr[] = array( "distribution_quantity" => $distribution_quantity,"product_id" => $stock_product_id);
    }

    // encoding array to json format
    echo json_encode($users_arr);
    exit;
}