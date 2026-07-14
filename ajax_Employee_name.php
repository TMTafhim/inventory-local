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



$resultoutput = $pdo->query("SELECT employee_information.name_en AS name, hr_designation.name AS employee_designation FROM employee_information INNER JOIN hr_designation ON employee_information.designation=hr_designation.id WHERE  name_en like '%$search%' ORDER BY name_en ASC limit 0,20");
	        while($result = $resultoutput->fetch()){	


		  $response[] = array("value"=>$result['name'],"label"=>$result['name']." ".$result['employee_designation']);
	}

    // encoding array to json format
    echo json_encode($response);
    exit;
}

