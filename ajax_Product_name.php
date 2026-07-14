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

