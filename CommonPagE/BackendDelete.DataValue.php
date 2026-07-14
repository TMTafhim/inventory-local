<?php
if(isset($_POST["Product_Merge_Delete"])){
    if(!authIsSuperAdmin($LoginReGiSterSession)){
        $_SESSION['warning_message']="Only employee ID 121 can merge/delete products.";
        echo "<script>window.open('?Product_Information/Setting','_self')</script>";
        exit;
    }

    $source_product_id = isset($_POST["source_product_id"]) ? (int)$_POST["source_product_id"] : 0;
    $target_product_id = isset($_POST["target_product_id"]) ? (int)$_POST["target_product_id"] : 0;

    if($source_product_id <= 0 || $target_product_id <= 0 || $source_product_id === $target_product_id){
        $_SESSION['warning_message']="Please select a valid duplicate product and master product.";
        echo "<script>window.open('?Product_Merge_Delete/Setting/$source_product_id','_self')</script>";
        exit;
    }

    $sourceStatement=$pdo->prepare("SELECT * FROM product_information WHERE id=:id AND deleted_at IS NULL LIMIT 1");
    $sourceStatement->execute(array(':id'=>$source_product_id));
    $sourceProduct=$sourceStatement->fetch();

    $targetStatement=$pdo->prepare("SELECT * FROM product_information WHERE id=:id AND deleted_at IS NULL LIMIT 1");
    $targetStatement->execute(array(':id'=>$target_product_id));
    $targetProduct=$targetStatement->fetch();

    if(!$sourceProduct || !$targetProduct){
        $_SESSION['warning_message']="Source or master product was not found.";
        echo "<script>window.open('?Product_Information/Setting','_self')</script>";
        exit;
    }

    $mergeTables=array(
        'requestion_detail',
        'requestion_draft_detail',
        'requestion_approval_detail',
        'distribution_history',
        'purchase_detail',
        'return_history_detail',
        'material_used_detail_history',
        'stock_transfer_information',
        'stock_information_detail',
        'product_detail_history'
    );

    try{
        $pdo->beginTransaction();

        $sourceStockStatement=$pdo->prepare("SELECT * FROM stock_information WHERE product_id=:source_product_id AND deleted_at IS NULL");
        $sourceStockStatement->execute(array(':source_product_id'=>$source_product_id));
        $sourceStockRows=$sourceStockStatement->fetchAll();

        foreach($sourceStockRows as $sourceStock){
            $targetStockStatement=$pdo->prepare("SELECT * FROM stock_information WHERE product_id=:target_product_id AND store_id=:store_id AND deleted_at IS NULL LIMIT 1");
            $targetStockStatement->execute(array(':target_product_id'=>$target_product_id, ':store_id'=>$sourceStock['store_id']));
            $targetStock=$targetStockStatement->fetch();

            if($targetStock){
                $mergeStockStatement=$pdo->prepare("
                    UPDATE stock_information
                    SET
                        `previous`=CAST(COALESCE(NULLIF(`previous`,''),0) AS DECIMAL(18,4)) + :previous_qty,
                        `new`=CAST(COALESCE(NULLIF(`new`,''),0) AS DECIMAL(18,4)) + :new_qty,
                        `return`=CAST(COALESCE(NULLIF(`return`,''),0) AS DECIMAL(18,4)) + :return_qty,
                        `total`=CAST(COALESCE(NULLIF(`total`,''),0) AS DECIMAL(18,4)) + :total_qty,
                        `distribution`=CAST(COALESCE(NULLIF(`distribution`,''),0) AS DECIMAL(18,4)) + :distribution_qty,
                        `stock`=CAST(COALESCE(NULLIF(`stock`,''),0) AS DECIMAL(18,4)) + :stock_qty,
                        updated_by=:updated_by,
                        updated_at=:updated_at
                    WHERE id=:target_stock_id
                ");
                $mergeStockStatement->execute(array(
                    ':previous_qty'=>(float)$sourceStock['previous'],
                    ':new_qty'=>(float)$sourceStock['new'],
                    ':return_qty'=>(float)$sourceStock['return'],
                    ':total_qty'=>(float)$sourceStock['total'],
                    ':distribution_qty'=>(float)$sourceStock['distribution'],
                    ':stock_qty'=>(float)$sourceStock['stock'],
                    ':updated_by'=>$LoginReGiSterSession,
                    ':updated_at'=>$current_time,
                    ':target_stock_id'=>$targetStock['id']
                ));
                $deleteSourceStock=$pdo->prepare("UPDATE stock_information SET deleted_by=:deleted_by,deleted_at=:deleted_at WHERE id=:source_stock_id");
                $deleteSourceStock->execute(array(':deleted_by'=>$LoginReGiSterSession, ':deleted_at'=>$current_time, ':source_stock_id'=>$sourceStock['id']));
            }else{
                $moveStockStatement=$pdo->prepare("UPDATE stock_information SET product_id=:target_product_id,product_category=:product_category,updated_by=:updated_by,updated_at=:updated_at WHERE id=:source_stock_id");
                $moveStockStatement->execute(array(
                    ':target_product_id'=>$target_product_id,
                    ':product_category'=>$targetProduct['product_category'],
                    ':updated_by'=>$LoginReGiSterSession,
                    ':updated_at'=>$current_time,
                    ':source_stock_id'=>$sourceStock['id']
                ));
            }
        }

        foreach($mergeTables as $mergeTable){
            $updateStatement=$pdo->prepare("UPDATE `$mergeTable` SET product_id=:target_product_id,updated_by=:updated_by,updated_at=:updated_at WHERE product_id=:source_product_id");
            $updateStatement->execute(array(
                ':target_product_id'=>$target_product_id,
                ':updated_by'=>$LoginReGiSterSession,
                ':updated_at'=>$current_time,
                ':source_product_id'=>$source_product_id
            ));
        }

        foreach(array('emergency_request_detail','emergency_stock_movement') as $mergeTable){
            $updateStatement=$pdo->prepare("UPDATE `$mergeTable` SET product_id=:target_product_id WHERE product_id=:source_product_id");
            $updateStatement->execute(array(
                ':target_product_id'=>$target_product_id,
                ':source_product_id'=>$source_product_id
            ));
        }

        $deleteProductStatement=$pdo->prepare("UPDATE product_information SET deleted_by=:deleted_by,deleted_at=:deleted_at,updated_by=:updated_by,updated_at=:updated_at WHERE id=:source_product_id");
        $deleteProductStatement->execute(array(
            ':deleted_by'=>$LoginReGiSterSession,
            ':deleted_at'=>$current_time,
            ':updated_by'=>$LoginReGiSterSession,
            ':updated_at'=>$current_time,
            ':source_product_id'=>$source_product_id
        ));

        $pdo->commit();
        $_SESSION['success_message']="Duplicate product merged successfully. Stock and all product references moved to the selected master product.";
        echo "<script>window.open('?Product_Usage_Search/Report/$target_product_id','_self')</script>";
        exit;
    }catch(Exception $exception){
        if($pdo->inTransaction()){
            $pdo->rollBack();
        }
        $_SESSION['warning_message']="Product merge failed: ".$exception->getMessage();
        echo "<script>window.open('?Product_Merge_Delete/Setting/$source_product_id','_self')</script>";
        exit;
    }
}

if(!empty($PageStatusCheck) && $PageStatusCheck=='DELETE'){

$pdo->query("UPDATE $DeleteDatabase SET deleted_by='$LoginReGiSterSession',deleted_at='$current_time' WHERE id='$DocumentData'");	

if(!empty($DeleteDatabase) && $DeleteDatabase=='requestion_histiory'){
	$informationpurchage = $pdo->query("SELECT * FROM `requestion_histiory` where id='$DocumentData'");	
	$rowdatapurchage = $informationpurchage->fetch() ;
$pdo->query("UPDATE project_material_aproval_status SET deleted_by='$LoginReGiSterSession',deleted_at='$current_time' WHERE invoice_id='".$rowdatapurchage["invoice_id"]."' and project_id='".$rowdatapurchage["project_id"]."'");	 
    
}

$_SESSION['success_message']=$success_message_Delete_data;
echo "<script>window.open('?$page_title/$MenuName','_self')</script>"; 	



 	
}

// Asset Product Delete Start
if(!empty($PageStatusCheck) && $PageStatusCheck=='DELETE_Asset_Management'){

$information_product = $pdo->query("SELECT * FROM `asset_product_detail_history` where  id='$DocumentData' and deleted_at is NULL"); 
$rowdata_information_product = $information_product->fetch(); 

$asset_product_id=$rowdata_information_product["product_id"];
$quantity=$rowdata_information_product["quantity"];


$pdo->query("UPDATE $DeleteDatabase SET deleted_by='$LoginReGiSterSession',deleted_at='$current_time' WHERE id='$DocumentData'");	

$informationProduct_Detail = $pdo->query("SELECT * FROM `asset_product_information` where  id='$asset_product_id'"); 
$rowdataProduct_Detail = $informationProduct_Detail->fetch();

$stock=$rowdataProduct_Detail["stock"];
$new_stock=$stock-$quantity;

$pdo->query("UPDATE asset_product_information SET stock='$new_stock' WHERE id='$asset_product_id'");	

$_SESSION['success_message']=$success_message_Delete_data;
echo "<script>window.open('?$page_title/$MenuName','_self')</script>"; 	
 	
}
// Asset Product Delete End


//Purchase Information Delete Start
if(!empty($PageStatusCheck) && $PageStatusCheck=='DELETE_Purchase'){

$pdo->query("UPDATE $DeleteDatabase SET deleted_by='$LoginReGiSterSession',deleted_at='$current_time' WHERE invoice_id='$DocumentData'");
	
$informationProduct_Detail = $pdo->query("SELECT * FROM `purchase_detail` where  purchase_detail.invoice_id='$DocumentData' and purchase_detail.deleted_at is NULL"); while ($rowdataProduct_Detail = $informationProduct_Detail->fetch()){	
$product_id=$rowdataProduct_Detail["product_id"];	
$store_id=$rowdataProduct_Detail["store_id"];
$quantity=$rowdataProduct_Detail["quantity"];

$Product_information = $pdo->query("SELECT *  FROM `stock_information` where product_id='$product_id' and store_id='$store_id' ");
$rowdataProduct_information = $Product_information->fetch();
	
$product_primary_id=$rowdataProduct_information["id"];	
$stock_previous=$rowdataProduct_information["stock"];
	
$stock_current=$stock_previous-$quantity;
			
$pdo->query("UPDATE `stock_information` set stock='$stock_current'  where id='$product_primary_id'");	
		
}
	
	

$pdo->query("UPDATE purchase_detail SET deleted_by='$LoginReGiSterSession',deleted_at='$current_time' WHERE invoice_id='$DocumentData'");
	
$_SESSION['success_message']=$success_message_Delete_data;
echo "<script>window.open('?$page_title/$MenuName','_self')</script>"; 	
 	
}
//Purchase Information Delete End


/*if(!empty($MenuName) && $MenuName=='SessionDistroy'){


header("location: $base_url");

session_destroy();
	
}*/

//Project Material Approval Delete Start
if(!empty($PageStatusCheck) && $PageStatusCheck=='DELETE_Project_Material_Approval'){

$pdo->query("UPDATE $DeleteDatabase SET deleted_by='$LoginReGiSterSession',deleted_at='$current_time' WHERE project_id='$DocumentData'");	
$_SESSION['success_message']=$success_message_Delete_data;
echo "<script>window.open('?$page_title/$MenuName','_self')</script>"; 	
 	
}
//Project Material Approval Delete End




//Project Material Approval Delete Start
if(!empty($PageStatusCheck) && $PageStatusCheck=='DELETE_Project_Material_Approval_Path'){

$pdo->query("UPDATE $DeleteDatabase SET deleted_by='$LoginReGiSterSession',deleted_at='$current_time' WHERE id='$DocumentData'");	
$_SESSION['success_message']=$success_message_Delete_data;
echo "<script>window.open('?$page_title/$MenuName','_self')</script>"; 	
 	
}
//Project Material Approval Delete End






if(!empty($MenuName) && $MenuName=='SessionDistroy'){

session_destroy();
//header("location: $base_url");
echo "<script>window.open('$base_url','_self')</script>";


	
}


?>
