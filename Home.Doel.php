<?php
require_once("BDB/Auth.php");
$LoginReGiSterSession=$_SESSION['LoginReGiSterSession'];
$menu_access=array();
$role_permission='';
$user_Login_information= $pdo->query("select * FROM employee_information WHERE ID='".$_SESSION['LoginReGiSterSession']."'");
$row_Login_Datauser_information = $user_Login_information->fetch();
$login_has_full_access=authIsSuperAdmin($LoginReGiSterSession);
if(!empty($page_title)){
$success_message_data=str_replace("_"," ","Thank you. your  $page_title Data Save Successfully");
$failure_invalid_message_data=str_replace("_"," ","Sorry. your  $page_title information not save");	
$failure_message_data=str_replace("_"," ","Sorry. your  $page_title information already exit our system ");
$success_message_edit_data=str_replace("_"," ","Thank you. your  $page_title Data Update Successfully");
$success_message_Delete_data=str_replace("_"," ","Thank you. your  $page_title Data Deleted Successfully");	
}
$login_user_store_id=$row_Login_Datauser_information["store_id"];
if(!empty($row_Login_Datauser_information["menu_access"])){
  $menu_access=json_decode($row_Login_Datauser_information["menu_access"],true);
	if(!is_array($menu_access)){
		$menu_access=array();
	}
	$menu_access=array_values(array_unique(array_map(function($menuName){
		return $menuName==='Purchase History/Input' ? 'Purchase History' : $menuName;
	},$menu_access)));
         }

if(!empty($row_Login_Datauser_information["role_permission"])){
  $role_permission=$row_Login_Datauser_information["role_permission"];
         }

if($login_has_full_access){
  $_SESSION['USER_TYPE']='Admin';
  $menu_access=array(
    'Requisition Draft',
    'Emergency Request',
    'Requestion',
    'Distribution Pending',
    'Material Received Status',
    'Distribution List',
    'Distribution History',
    'Purchase History',
    'Stock',
    'Indivisual Stock',
    'Return History',
    'Project Material Used History',
    'Stock Transfer',
    'Report',
    'Setting',
    'HR'
  );
  $role_permission='Create, Update, Delete, View, Distribution';
}

if(!empty($row_Login_Datauser_information["designation"])){
$user_Designation_information= $pdo->query("select * FROM hr_designation WHERE ID='".$row_Login_Datauser_information["designation"]."'");
$row_Login_designation_information = $user_Designation_information->fetch();	
}


include("SMS.Doel.php");
include("CommonPagE/ProductUsageFilter.Helper.php");
include("CommonPagE/BackendInsert.DataValue.php");
include("CommonPagE/BackendUPDATE.DataValue.php");
include("CommonPagE/BackendDelete.DataValue.php");?>
<?php /*?><body class="hold-transition sidebar-mini layout-fixed"><?php */?>
<div class="wrapper">

  <!-- Preloader -->
 <!-- <div class="preloader flex-column justify-content-center align-items-center">
    <img class="animation__shake" src="<?php echo $feviconicon; ?>" alt="<?php echo $organization_name; ?>" height="60" width="60">
  </div>-->

  <!-- Navbar -->
 <?php include("Header.Data.php"); ?>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <?php include("LeftSide.Data.php"); ?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
  <?php include("ContentHeader.Data.php"); ?>
    <!-- /.content-header -->

    <!-- Main content -->
	  
	  
    <?php
	  
	 $file_checkPage=file_exists("CommonPagE/$page_title_data");
	
	if($file_checkPage==1){
	 include("CommonPagE/$page_title_data");
	}else{
		include("CommonPagE/Error.Doel.php");
		}
	  ?>
    <!-- /.content -->
  </div>
 
<?php include("Footer.Data.php"); ?>
</div>
<!-- ./wrapper -->

<?php /*?>	
<?php include("FooterScript.Data.php"); ?>	
	
	
	
</body>
</html><?php */?>
