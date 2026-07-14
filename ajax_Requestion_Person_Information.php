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



$resultoutput = $pdo->query("SELECT * FROM employee_information WHERE  name_en like '%$search%' ORDER BY name_en ASC limit 0,20");
	        while($result = $resultoutput->fetch()){	


		  $response[] = array("value"=>$result['name_en'],"label"=>$result['name_en']);
	}

    // encoding array to json format
    echo json_encode($response);
    exit;
}

