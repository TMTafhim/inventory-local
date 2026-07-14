<?php
require_once("BDB/Auth.php");
if (!empty($_SESSION['LoginReGiSterSession'])
    && !authIsSuperAdmin($_SESSION['LoginReGiSterSession'])
    && !empty($_SERVER['REQUEST_URI'])
    && strpos($_SERVER['REQUEST_URI'], '?System_Activity_Audit') !== false) {
    http_response_code(403);
}
include("SessionController.php");
//unset($_SESSION);
//print_r($_SESSION);
?>
<?php include("HeaderScript.Data.php");
?>
<body class="<?php if(!empty($_SESSION['LoginReGiSterSession'])){ echo "hold-transition sidebar-mini layout-fixed"; }else{ echo "hold-transition login-page"; } ?> ">
		
	<!--Session Popup Start-->	
<link rel="stylesheet" type="text/css" href="css/sweetalert.css">	
<script src="js/sweetalert.min.js"></script>
	<?php
   
    if(!empty($success_message))
    {
       echo "<script>document.addEventListener('DOMContentLoaded', function(){ appShowAlert(".json_encode($success_message).", 'success', 'Success'); });</script>";
    }
    
    if(!empty($warning_message))
    {
       echo "<script>document.addEventListener('DOMContentLoaded', function(){ appShowAlert(".json_encode($warning_message).", 'warning', 'গুরুত্বপূর্ণ সতর্কতা'); });</script>";
    }
?>
<!--Session Popup End-->
<?php

//$_SESSION['LoginReGiSterSession']="Polash";
if(!isset($_SESSION['LoginReGiSterSession'])){
include("Fontend.DataValue.php");	
if(!empty($page_title_data)){
	$page_title_data;
	}else{
		$page_title_data="Login";
		}
$file_check=file_exists("$page_title_data");	
	if($file_check==1){
	 include("$page_title_data");
	}else{
		include("Login.Doel.php");
		}		
}
else {	
	
if(!empty($page_title_data)){
	$page_title_data;
	}else{
		$page_title_data="BoXinfo";
		}
    $file_check=file_exists("CommonPagE/$page_title_data");
	
	if($file_check==1){
	 include("Home.Doel.php");
	}else{
		$page_title='Error';
		$page_title_data='Error.Doel.php';
		include("Home.Doel.php");
		}
	}






?>
<?php include("FooterScript.Data.php"); ?>	
	
</body>
</html>
