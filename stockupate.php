<?php
include("BDB/DBConnEction.php");
$informationpurchage = $pdo->query("SELECT * FROM `stock_information` where  deleted_at is NULL");
while ($rowdata = $informationpurchage->fetch()){ 
	$stock_id=$rowdata["id"];
    $store_id=$rowdata["store_id"];
    $product_id=$rowdata["product_id"];
	$previous=$rowdata["previous"];
	$new=$rowdata["new"];
	$total=$rowdata["total"];
	$distribution=$rowdata["distribution"];
	$stock=$rowdata["stock"];
	$return=$rowdata["return"];
	$product_category=$rowdata["product_category"];
	
	$sqltrnasaction="INSERT INTO `stock_information_detail`(`store_id`,`product_id`, `date`, `previous`, `new`, `total`, `distribution`, `stock`,`return`,`product_category`,`created_at`) VALUES (:store_id,:product_id,:date,:previous,:new,:total,:distribution,:stock,:return,:product_category,'$current_time')";

     $insert_trnasaction= $pdo->prepare($sqltrnasaction);
	 $insert_trnasaction->bindparam(":store_id", $store_id);
	 $insert_trnasaction->bindparam(":product_id", $product_id);
	 $insert_trnasaction->bindparam(":date", $previous_one_days);
	 $insert_trnasaction->bindparam(":previous", $previous);
	 $insert_trnasaction->bindparam(":new", $new);	 
	 $insert_trnasaction->bindparam(":total", $total);
	 $insert_trnasaction->bindparam(":distribution", $distribution);
	 $insert_trnasaction->bindparam(":stock", $stock);
	 $insert_trnasaction->bindparam(":return", $return);
	 $insert_trnasaction->bindparam(":product_category", $product_category);
	 $insert_trnasaction->execute();
	
	$pdo->query("UPDATE `stock_information` SET `previous`='$stock',`new`='0',`total`='$stock',`distribution`='0',`return`='0' WHERE id=$stock_id");
 }

	


?>
