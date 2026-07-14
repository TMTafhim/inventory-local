<?php



//Employee Information Edit Start
if(isset($_POST["Employee_information_Edit"])){
		
  if(!empty($_FILES['photo']['name'])){
		    
	  $temp = explode(".", $_FILES['photo']['name']);
      $newfilename = $temp["0"].round(microtime(true)) . '.' . end($temp);
      $file_tmp= $_FILES['photo']['tmp_name'];
      move_uploaded_file($file_tmp, "HRPhoto/" . $newfilename);
	  $pdo->query("UPDATE `employee_information` SET photo='$newfilename'  where id=$DocumentData");
	  }
		
		
   if(!empty($_FILES['hr_cv']['name'])){
		    
	  $temp = explode(".", $_FILES['hr_cv']['name']);
      $newfilenamehr_cv = $temp["0"].round(microtime(true)) . '.' . end($temp);
      $file_tmp= $_FILES['hr_cv']['tmp_name'];
      move_uploaded_file($file_tmp, "HRCV/" . $newfilenamehr_cv);
	  
	  $pdo->query("UPDATE `employee_information` SET hr_cv='$newfilename'  where id=$DocumentData");
	  
	  }	
	
	
	if(!empty($_FILES['signature']['name'])){
		    
	  $temp = explode(".", $_FILES['signature']['name']);
	  $last_extension=end($temp);
		
	  if(!empty(end($temp)) && ($last_extension=='jpg' or $last_extension=='png')){
	  $newfilenameSignature= $temp["0"].round(microtime(true)) . '.' . end($temp);
      $file_tmpnewfilenameSignature= $_FILES['signature']['tmp_name'];
      move_uploaded_file($file_tmpnewfilenameSignature, "Signature/".$newfilenameSignature);
	  
	  $pdo->query("UPDATE `employee_information` SET signature='$newfilenameSignature'  where id=$DocumentData");  
		  
	  }	
      
	  
	  }	
	
	
	

   if(!empty($_FILES['photo']['name'])){ 
	  $temp = explode(".", $_FILES['photo']['name']);
      $newfilename = $temp["0"].round(microtime(true)) . '.' . end($temp);
      $file_tmp= $_FILES['photo']['tmp_name'];
      move_uploaded_file($file_tmp, "image/" . $newfilename);
	 
	  $editimage = $pdo->query("UPDATE `$PageStatusCheck` SET photo='$newfilename'  where id=$DocumentData");
	  }
	
	if(!empty($_POST["no_value"])){
		$new_password = password_hash($_POST["no_value"], PASSWORD_DEFAULT);	
		$password_name=',`password`';	
		$password_value="'".$new_password."'";	
		}else{
		$password_name='';	
		$password_value="";		
		}

	
// Array Update Loop Start

$valuedata='';
    $copyArray=$_POST;
    $sliced = array_slice($copyArray, 0, -1);
    
    
    foreach ($sliced as $key => $value) {
      $valuedata.="`".$key."`=:".$key.",";
	}
    
    $valuedata_send=substr($valuedata,0,-1);
	if(!empty($password_value)){
	$sql="UPDATE `employee_information` SET ". $valuedata_send.",updated_by='$LoginReGiSterSession',updated_at=now()$password_name=$password_value WHERE id=$DocumentData";	
	}else{
	$sql="UPDATE `employee_information` SET ". $valuedata_send.",updated_by='$LoginReGiSterSession',updated_at=now() WHERE id=$DocumentData";	
	}
    
    
    foreach( $sliced as $field => $value ) $params[":{$field}"]=$value;
	$statement = $pdo->prepare( $sql );
	$statement->execute( $params );

//Array Update Loop End
	
	$_SESSION['success_message']=$success_message_data;
     echo "<script>window.open('?$page_title/$MenuName','_self')</script>"; 
	 
	
	}
	
	
//Employee Information Edit End


//Edit Start
if(isset($_POST["Edit_all_Doc"])){
		
		

   if(!empty($_FILES['photo']['name'])){ 
	  $temp = explode(".", $_FILES['photo']['name']);
      $newfilename = $temp["0"].round(microtime(true)) . '.' . end($temp);
      $file_tmp= $_FILES['photo']['tmp_name'];
      move_uploaded_file($file_tmp, "image/" . $newfilename);
	 
	  $editimage = $pdo->query("UPDATE `$PageStatusCheck` SET photo='$newfilename'  where id=$DocumentData");
	  }
	
	

	
// Array Update Loop Start

$valuedata='';
    $copyArray=$_POST;
    $sliced = array_slice($copyArray, 0, -1);
    
    
    foreach ($sliced as $key => $value) {
      $valuedata.="`".$key."`=:".$key.",";
	}
    
    $valuedata_send=substr($valuedata,0,-1);
    $sql="UPDATE `$PageStatusCheck` SET ". $valuedata_send.",updated_by='$LoginReGiSterSession',updated_at=now() WHERE id=$DocumentData";
    
    foreach( $sliced as $field => $value ) $params[":{$field}"]=$value;
	$statement = $pdo->prepare( $sql );
	$statement->execute( $params );

//Array Update Loop End
	
	$_SESSION['success_message']=$success_message_data;
     echo "<script>window.open('?$page_title/$MenuName','_self')</script>"; 
	 
	
	}

if(isset($_POST["User_Application_Permission_Update"])){
	
if(!empty($_POST["store_id"])){
$store_id=$_POST["store_id"];	
}else{
$store_id='';	
}
	
	
if(!empty($_POST["name_create"])){
$name_create=$_POST["name_create"];	
}else{
$name_create='';	
}	
	
	
	
	
if(!empty($_POST["name_update"])){
$name_update=$_POST["name_update"];	
}else{
$name_update='';	
}
	
if(!empty($_POST["name_delete"])){
$name_delete=$_POST["name_delete"];	
}else{
$name_delete='';	
}
if(!empty($_POST["name_View"])){
$name_View=$_POST["name_View"];	
}else{
$name_View='';	
}
	
if(!empty($_POST["name_Distribution"])){
$name_Distribution=$_POST["name_Distribution"];	
}else{
$name_Distribution='';	
}
	

	
$role_permission=$name_create.", ".$name_update.", ".$name_delete.", ".$name_View.", ".$name_Distribution;	
	
$pdo->query("UPDATE `employee_information` SET role_permission='$role_permission',store_id='$store_id'  where id=$DocumentData");	
$_SESSION['success_message']=$success_message_data;
     echo "<script>window.open('?$page_title','_self')</script>"; 
	 	
}
	
	
//Edit End

//Menu Access contreol Start
	
	if(isset($_POST["user_menu_permission_edit"])){
	 $bascic__info_value = array();
	 foreach ($_POST as $key => $value) {
		 if (strpos($key, 'menupermission') === 0 && is_string($value)) {
			 $bascic__info_value[] = $value === 'Purchase History/Input' ? 'Purchase History' : $value;
		 }
	 }
	 $bascic__info_value = array_values(array_unique($bascic__info_value));
   
	 $menupermission=json_encode($bascic__info_value); 
		
		
	  $sql="UPDATE employee_information SET menu_access=:menu_access WHERE ID=:employee_id";

	  $insert_data= $pdo->prepare($sql);
	  $insert_data->bindparam(":menu_access", $menupermission);
	  $insert_data->bindparam(":employee_id", $PageStatusCheck);
	  $insert_data->execute();
	 
	 $_SESSION['success_message']=$success_message_data;
     echo "<script>window.open('?$page_title/$MenuName','_self')</script>"; 
	  
	}
	
	//Menu Access Control End



?>
