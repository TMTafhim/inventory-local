<?php
include("BDB/DBConnEction.php");

	if(isset($_POST["FROM_STORE_ID"])){
		echo "<option value=''>Select Product</option>";
	$FROM_STORE_ID=$_POST["FROM_STORE_ID"];


	$LoginPasswordCheck = $pdo->query("SELECT product_information.name AS name,product_information.id AS id FROM product_information INNER JOIN stock_information ON product_information.id=stock_information.product_id WHERE stock_information.store_id='$FROM_STORE_ID' and stock_information.deleted_at is NULL");
	while($illustratedrowdata = $LoginPasswordCheck->fetch()){
	echo "<option  value='".$illustratedrowdata["id"]."'>".$illustratedrowdata["name"]."</option>";	
		
		
	}	
		
		
	}

	if(isset($_POST["FROM_STORE_ID_To_Store_Select"])){
		echo "<option value=''>Select Store Name</option>";
	$FROM_STORE_ID_To_Store_Select=$_POST["FROM_STORE_ID_To_Store_Select"];
		
$LoginPasswordCheck = $pdo->query("SELECT * FROM store_information WHERE id!='$FROM_STORE_ID_To_Store_Select' and deleted_at is NULL");
	while($illustratedrowdata = $LoginPasswordCheck->fetch()){
	echo "<option  value='".$illustratedrowdata["id"]."'>".$illustratedrowdata["name"]."</option>";	
	}	
		
		
	}

	if(isset($_POST["FROM_STORE_MEDICINE_ID"])){
	$FROM_STORE_MEDICINE_ID=$_POST["FROM_STORE_MEDICINE_ID"];
    $Medicine_STOCK = $conn->query("SELECT STOCK FROM MEDICINE_STOCK_INFORMATION WHERE MEDICINE_STOCK_INFORMATION.ID='$FROM_STORE_MEDICINE_ID'");
    $rowDataMedicine_STOCK= $Medicine_STOCK->fetch();
    echo "<script>document.getElementById('availablequantity').value='".$rowDataMedicine_STOCK["0"]."';</script>";

		
	}

		if(isset($_POST["FROM_STORE_MEDICINE_ID_Generel_ID"])){
	$FROM_STORE_MEDICINE_ID_Generel_ID=$_POST["FROM_STORE_MEDICINE_ID_Generel_ID"];
    $Medicine_STOCK = $conn->query("SELECT STOCK FROM MEDICINE_STOCK_INFORMATION WHERE MEDICINE_STOCK_INFORMATION.ID='$FROM_STORE_MEDICINE_ID_Generel_ID'");
    $rowDataMedicine_STOCK= $Medicine_STOCK->fetch();
    echo "<script>document.getElementById('availablequantity').value='".$rowDataMedicine_STOCK["0"]."';</script>";

		
	}


		if(isset($_POST["FROM_STORE_MEDICINE_ID_Generel_ID"])){
		echo "<option value=''>Select Medicine</option>";
	$FROM_STORE_MEDICINE_ID_Generel_ID=$_POST["FROM_STORE_MEDICINE_ID_Generel_ID"];


	$LoginPasswordCheck = $conn->query("SELECT MEDICINE_STOCK_INFORMATION.MEDICINE_ID,MEDICINE_INFORMATION.NAME FROM MEDICINE_STOCK_INFORMATION INNER Join MEDICINE_INFORMATION On MEDICINE_STOCK_INFORMATION.MEDICINE_ID=MEDICINE_INFORMATION.ID  WHERE MEDICINE_STOCK_INFORMATION.STORE_ID='$FROM_STORE_MEDICINE_ID_Generel_ID'");
	while($illustratedrowdata = $LoginPasswordCheck->fetch()){
	echo "<option  value='".$illustratedrowdata["0"]."'>".$illustratedrowdata["1"]."</option>";	
		
		
	}	
		
		
	}

?>