<?php
include("BDB/DBConnEction.php");
if(isset($_POST["product_category"])){
	$product_category=(int)$_POST["product_category"];

	$Product_Category_info = $pdo->prepare("SELECT * FROM product_category where id=:product_category");
	$Product_Category_info->execute(array(':product_category'=>$product_category));
	$rowDataCategory_info= $Product_Category_info->fetch();

	if(empty($rowDataCategory_info)){
		if(!empty($_POST['response_type']) && $_POST['response_type']==='json'){
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode(array('error'=>'Category not found.'));
			exit;
		}
		exit;
	}

	$ProductInformation = $pdo->prepare("SELECT * FROM product_information where product_category=:product_category order by CAST(code_no AS UNSIGNED) DESC, code_no DESC LIMIT 1");
	$ProductInformation->execute(array(':product_category'=>$rowDataCategory_info["id"]));
	$rowDataProductInformation= $ProductInformation->fetch();
	if(!empty($rowDataProductInformation["code_no"])){
		$new_code_no=(int)$rowDataProductInformation["code_no"]+1;
	}else{
		$new_code_no=1001;
	}
	$code_number=$rowDataCategory_info["code"].$new_code_no;

	if(!empty($_POST['response_type']) && $_POST['response_type']==='json'){
		header('Content-Type: application/json; charset=utf-8');
		echo json_encode(array(
			'code'=>$code_number,
			'code_no'=>$new_code_no
		));
		exit;
	}

	echo "<script>document.getElementById('code').value='".htmlspecialchars($code_number,ENT_QUOTES)."';</script>";
	echo "<script>document.getElementById('code_no').value='".htmlspecialchars($new_code_no,ENT_QUOTES)."';</script>";
}
?>
