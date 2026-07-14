<?php
include("BDB/DBConnEction.php");
	if(isset($_POST["product_id_edit"]))
		{
			$product_id_edit=$_POST["product_id_edit"];
			$product_category=$_POST["product_category"];
			
		$Product_Detail_Information = $pdo->query("SELECT * FROM product_information where id='".$product_id_edit."' ");
        $rowDataProductDetailInformation= $Product_Detail_Information->fetch();
        
        if(!empty($rowDataProductDetailInformation["code_no"])){
         echo "<script>document.getElementById('code').value='".$rowDataProductDetailInformation["code"]."';</script>";	
		echo "<script>document.getElementById('code_no').value='".$rowDataProductDetailInformation["code_no"]."';</script>";   
        }else{
        $Product_Category_info = $pdo->query("SELECT * FROM product_category where id='$product_category'");
         $rowDataCategory_info= $Product_Category_info->fetch();
         
        $ProductInformation = $pdo->query("SELECT * FROM product_information where product_category='".$rowDataCategory_info["id"]."' order by code_no DESC");
        $rowDataProductInformation= $ProductInformation->fetch(); 
        if(!empty($rowDataProductInformation["code_no"])){
        $new_code_no=$rowDataProductInformation["code_no"]+1;   
        }else{
        $new_code_no=1001;    
        }
        $code_number=$rowDataCategory_info["code"].$new_code_no;
         

		
		echo "<script>document.getElementById('code').value='".$code_number."';</script>";	
		echo "<script>document.getElementById('code_no').value='".$new_code_no."';</script>";	
            
        }

		}
?>

