<?php
include("BDB/DBConnEction.php");
//Session Message Start
if(isset($_SESSION['success_message']))
	{
	    $success_message=$_SESSION['success_message'];
        if(!empty($success_message))
        {
        unset($_SESSION['success_message']);
        }
	}
	
	if(isset($_SESSION['warning_message']))
	{
	    $warning_message=$_SESSION['warning_message'];
        if(!empty($warning_message))
        {
           unset($_SESSION['warning_message']);
        }
	}
//Session Message End

//print_r($_SESSION); 
//unset($_SESSION);

/*$base_url="https://inventory.sigma-royal.com/";
$actual_link = "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
$main_link = "https://inventory.sigma-royal.com/";
$url_string_Data=str_replace("www.","",$actual_link);

$url_string=str_replace($base_url,"",$url_string_Data);
$url_string_0=str_replace("https:","",$url_string);
$url_string_1 = str_replace('?', '', $url_string_0);
//$url_string_output = str_replace('/', ' ', $url_string_1);

$forloopvalue = explode("/",$url_string_1);*/

if(!empty($forloopvalue['0'])){
	$page_title=$forloopvalue['0']; //echo "Polash";
	}else if(!empty($_SESSION['LoginReGiSterSession'])){
	$page_title="BoXinfo"; //echo "Polash";
	}else{
		$page_title="Login";
		}

if(!empty($forloopvalue['1'])){
	$MenuName=$forloopvalue['1'];
	}else{
	$MenuName='';
    }


if(!empty($forloopvalue['2'])){
	$DocumentData=$forloopvalue['2'];
	}
if(!empty($forloopvalue['3'])){
	$PageStatusCheck=$forloopvalue['3'];
	}
if(!empty($forloopvalue['4'])){
	$DeleteDatabase=$forloopvalue['4'];
	}

$page_title_data=$page_title.".Doel.php";
$company_email_address="polash@doelhosting.com";

$organization_name="The Royal Utilisation Services (Pvt.) Ltd";
$organization_address="<b>Address&nbsp;:&nbsp;</b> House#383, (3rd floor), Road#28,
New DOHS, Mohakhali, Dhaka-1206.
";
$organization_contact_address="<b>Contact&nbsp;:&nbsp;</b>+88002222281246, +880258810750";
$feviconicon="image/e_Services.png";
$organizationlogo="image/Doel e-Services.png";


?>


