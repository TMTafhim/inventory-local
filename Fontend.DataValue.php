<?php

//Login Start
if(isset($_POST["LoginResponse"])){


$InsertDataVerification = $pdo->query("SELECT COUNT(id) FROM employee_information where email='".$_POST["LoginEmail"]."' and user_status='Active' and deleted_at is NULL");
$DataVerificationNumber=$InsertDataVerification->fetchColumn();
if(!empty($DataVerificationNumber) && $DataVerificationNumber==1){
	$LoginPasswordCheck = $pdo->query("SELECT employee_information.*,hr_department.name AS department,hr_designation.name AS designation FROM employee_information inner join hr_designation ON employee_information.designation=hr_designation.id INNER JOIN hr_department ON employee_information.department=hr_department.id  where employee_information.email='".$_POST["LoginEmail"]."' and employee_information.user_status='Active' and employee_information.deleted_at is NULL");
	$rowLoginPasswordCheck = $LoginPasswordCheck->fetch();
	
if(password_verify($_POST["LoginPassword"], $rowLoginPasswordCheck['password'])){
   $_SESSION['LoginReGiSterSession']=$rowLoginPasswordCheck['id'];
   $_SESSION['LOGINNAME']= $rowLoginPasswordCheck['name_en'];
   $_SESSION['DESIGNATION']= $rowLoginPasswordCheck['designation'];
   $_SESSION['USER_TYPE']= $rowLoginPasswordCheck['user_type'];
   $_SESSION['PHOTO']= $rowLoginPasswordCheck['photo'];
   $_SESSION['STORE_ID']= $rowLoginPasswordCheck['store_id'];
   activityAuditRecordImmediate($pdo,$rowLoginPasswordCheck['id'],'authentication','Login successful','Authentication',array('email'=>$rowLoginPasswordCheck['email']),$rowLoginPasswordCheck['store_id']);
   echo "<script>window.open('$actual_link','_self')</script>";
   }else{
   $_SESSION['warning_message']="Your User Name or Password is Incorrect.Please Try again";
   echo "<script>window.open('$actual_link','_self')</script>";
	}
 }else{
	
 
 $_SESSION['warning_message']="Your User Name or Password is Incorrect.Please Try again";
 echo "<script>window.open('$actual_link','_self')</script>";
}	
	
	
	
}
//Login End






?>
