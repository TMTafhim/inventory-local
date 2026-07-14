<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <a href="<?php echo $base_url; ?>" class="nav-link">Home</a>
      </li>
      
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <!-- Navbar Search -->
     <?php /*?> <li class="nav-item">
        <a class="nav-link" data-widget="navbar-search" href="#" role="button">
          <i class="fas fa-search"></i>
        </a>
        <div class="navbar-search-block">
          <form class="form-inline">
            <div class="input-group input-group-sm">
              <input class="form-control form-control-navbar" type="search" placeholder="Search" aria-label="Search">
              <div class="input-group-append">
                <button class="btn btn-navbar" type="submit">
                  <i class="fas fa-search"></i>
                </button>
                <button class="btn btn-navbar" type="button" data-widget="navbar-search">
                  <i class="fas fa-times"></i>
                </button>
              </div>
            </div>
          </form>
        </div>
      </li><?php */?>

      <!-- Messages Dropdown Menu -->
      <?php /*?><li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
          <i class="far fa-comments"></i>
          <span class="badge badge-danger navbar-badge">3</span>
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <a href="#" class="dropdown-item">
            <!-- Message Start -->
            <div class="media">
              <img src="dist/img/user1-128x128.jpg" alt="User Avatar" class="img-size-50 mr-3 img-circle">
              <div class="media-body">
                <h3 class="dropdown-item-title">
                  Brad Diesel
                  <span class="float-right text-sm text-danger"><i class="fas fa-star"></i></span>
                </h3>
                <p class="text-sm">Call me whenever you can...</p>
                <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 4 Hours Ago</p>
              </div>
            </div>
            <!-- Message End -->
          </a>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <!-- Message Start -->
            <div class="media">
              <img src="dist/img/user8-128x128.jpg" alt="User Avatar" class="img-size-50 img-circle mr-3">
              <div class="media-body">
                <h3 class="dropdown-item-title">
                  John Pierce
                  <span class="float-right text-sm text-muted"><i class="fas fa-star"></i></span>
                </h3>
                <p class="text-sm">I got your message bro</p>
                <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 4 Hours Ago</p>
              </div>
            </div>
            <!-- Message End -->
          </a>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <!-- Message Start -->
            <div class="media">
              <img src="dist/img/user3-128x128.jpg" alt="User Avatar" class="img-size-50 img-circle mr-3">
              <div class="media-body">
                <h3 class="dropdown-item-title">
                  Nora Silvester
                  <span class="float-right text-sm text-warning"><i class="fas fa-star"></i></span>
                </h3>
                <p class="text-sm">The subject goes here</p>
                <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 4 Hours Ago</p>
              </div>
            </div>
            <!-- Message End -->
          </a>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item dropdown-footer">See All Messages</a>
        </div>
      </li><?php */?>
      <!-- Notifications Dropdown Menu -->
      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
          <i class="far fa-bell"></i>
			<?php
			
			if(!empty($row_Login_designation_information["name"]) && $row_Login_designation_information["name"]=='Managing Director'){ 					
			$informationpurchage_calculation = $pdo->query("SELECT count(id) AS pending_number_list FROM `requestion_histiory` where requestion_histiory.`distribution_status`='Pending' and managing_director is NULL and project_director is not NULL and requestion_histiory.deleted_at is NULL");
			 }else if(!empty($row_Login_designation_information["name"]) && $row_Login_designation_information["name"]=='Project Director'){ 					
			$informationpurchage_calculation = $pdo->query("SELECT count(id) AS pending_number_list FROM `requestion_histiory`where requestion_histiory.`distribution_status`='Pending' and project_id='".$row_Login_Datauser_information["project_name"]."' and project_coordinator is not NULL and project_director is NULL and requestion_histiory.deleted_at is NULL");
			 }else if(!empty($row_Login_designation_information["name"]) && $row_Login_designation_information["name"]=='Project Coordinator'){ 					
			$informationpurchage_calculation = $pdo->query("SELECT count(id) AS pending_number_list FROM `requestion_histiory` where requestion_histiory.`distribution_status`='Pending' and project_id='".$row_Login_Datauser_information["project_name"]."' and project_coordinator is  NULL and requestion_histiory.deleted_at is NULL");
			 }else if(!empty($row_Login_designation_information["name"]) && $row_Login_designation_information["name"]=='Store Keeper'){ 					
			$informationpurchage_calculation = $pdo->query("SELECT count(id) AS pending_number_list FROM `requestion_histiory` where requestion_histiory.`distribution_status`='Pending' and store_id='".$row_Login_Datauser_information["store_id"]."' and managing_director is not NULL and requestion_histiory.deleted_at is NULL");
			 }else if($_SESSION['USER_TYPE']=='Admin'){ 					
			$informationpurchage_calculation = $pdo->query("SELECT count(id) AS pending_number_list FROM `requestion_histiory` where requestion_histiory.`distribution_status`='Pending' and requestion_histiory.deleted_at is NULL");
			 }else{	
				
			$informationpurchage_calculation = $pdo->query("SELECT count(id) AS pending_number_list FROM `requestion_histiory` where requestion_histiory.`distribution_status`='Pending' and requestion_histiory.employee_id='".$_SESSION['LoginReGiSterSession']."' and requestion_histiory.deleted_at is NULL");	
			}
			$DataVerificationNumber_total_list=$informationpurchage_calculation->fetchColumn();
			?>
			
			
          <span class="badge badge-warning navbar-badge"><?php echo $DataVerificationNumber_total_list; ?></span>
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <span class="dropdown-item dropdown-header"><?php echo $DataVerificationNumber_total_list; ?> Notifications</span>
         
			
			
			<?php
			if(!empty($row_Login_designation_information["name"]) && $row_Login_designation_information["name"]=='Managing Director'){ 					
			$informationpurchage = $pdo->query("SELECT requestion_histiory.*,project_information.name AS project_name,store_information.name AS store_name FROM `requestion_histiory` INNER JOIN project_information ON requestion_histiory.project_id=project_information.id INNER JOIN store_information ON requestion_histiory.store_id=store_information.id where requestion_histiory.`distribution_status`='Pending' and managing_director is NULL and project_director is not NULL and requestion_histiory.deleted_at is NULL");
			 }else if(!empty($row_Login_designation_information["name"]) && $row_Login_designation_information["name"]=='Project Director'){ 					
			$informationpurchage = $pdo->query("SELECT requestion_histiory.*,project_information.name AS project_name,store_information.name AS store_name FROM `requestion_histiory` INNER JOIN project_information ON requestion_histiory.project_id=project_information.id INNER JOIN store_information ON requestion_histiory.store_id=store_information.id where requestion_histiory.`distribution_status`='Pending' and project_id='".$row_Login_Datauser_information["project_name"]."' and project_coordinator is not NULL and project_director is NULL and requestion_histiory.deleted_at is NULL");
			 }else if(!empty($row_Login_designation_information["name"]) && $row_Login_designation_information["name"]=='Project Coordinator'){ 					
			$informationpurchage = $pdo->query("SELECT requestion_histiory.*,project_information.name AS project_name,store_information.name AS store_name FROM `requestion_histiory` INNER JOIN project_information ON requestion_histiory.project_id=project_information.id INNER JOIN store_information ON requestion_histiory.store_id=store_information.id where requestion_histiory.`distribution_status`='Pending' and project_id='".$row_Login_Datauser_information["project_name"]."' and project_coordinator is  NULL and requestion_histiory.deleted_at is NULL");
			 }else if(!empty($row_Login_designation_information["name"]) && $row_Login_designation_information["name"]=='Store Keeper'){ 					
			$informationpurchage = $pdo->query("SELECT requestion_histiory.*,project_information.name AS project_name,store_information.name AS store_name FROM `requestion_histiory` INNER JOIN project_information ON requestion_histiory.project_id=project_information.id INNER JOIN store_information ON requestion_histiory.store_id=store_information.id where requestion_histiory.`distribution_status`='Pending' and store_id='".$row_Login_Datauser_information["store_id"]."' and managing_director is not NULL and requestion_histiory.deleted_at is NULL");
			 }else if($_SESSION['USER_TYPE']=='Admin'){ 					
			$informationpurchage = $pdo->query("SELECT requestion_histiory.*,project_information.name AS project_name,store_information.name AS store_name FROM `requestion_histiory` INNER JOIN project_information ON requestion_histiory.project_id=project_information.id INNER JOIN store_information ON requestion_histiory.store_id=store_information.id where requestion_histiory.`distribution_status`='Pending' and requestion_histiory.deleted_at is NULL");
			 }else{	
				
			$informationpurchage = $pdo->query("SELECT requestion_histiory.*,project_information.name AS project_name,store_information.name AS store_name FROM `requestion_histiory` INNER JOIN project_information ON requestion_histiory.project_id=project_information.id INNER JOIN store_information ON requestion_histiory.store_id=store_information.id where requestion_histiory.`distribution_status`='Pending' and requestion_histiory.employee_id='".$_SESSION['LoginReGiSterSession']."' and requestion_histiory.deleted_at is NULL");	
			}
			
            while ($rowdatapurchage = $informationpurchage->fetch()){ ?>
          <div class="dropdown-divider"></div>
          <a href="?Requestion_History_Detail/<?php echo $rowdatapurchage["invoice_id"]; ?>" class="dropdown-item">
            <i class="fas fa-users mr-2"></i> <?php echo $rowdatapurchage["project_name"];  ?>
            <span class="float-right text-muted text-sm"><?php echo date("d-m-Y", strtotime($rowdatapurchage["date"])); ?></span>
          </a>
			<?php } ?>
         
          <div class="dropdown-divider"></div>
          <a href="?Requestion" class="dropdown-item dropdown-footer">See All Notifications</a>
        </div>
      </li>
		
		
      <li class="nav-item">
        <a class="nav-link" data-widget="fullscreen" href="#" role="button">
          <i class="fas fa-expand-arrows-alt"></i>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" data-widget="control-sidebar" data-controlsidebar-slide="true" href="#" role="button">
          <i class="fas fa-th-large"></i>
        </a>
      </li>
	<style>
li.user-header {
  height: 150px;
  padding: 10px;
  text-align: center;
  
  box-sizing: border-box; 			 
}
.dropdown-menu> .user-header {
  border-top-right-radius: 0;
  border-top-left-radius: 0;
  padding: 1px 0 0 0;
  border-top-width: 0;
  width: 280px;
}		
.dropdown-menu > li.user-header > p {
  z-index: 5;
  color: #000;
  
  font-size: 17px;
  margin-top: 10px;
}	
.dropdown-menu > .user-footer {

  padding: 10px;
}
.pull-left {
  float: left !important;
}
	
.pull-right {
  float: right !important;
}
		
		</style>
		<!--User Login Start-->
		<li class="nav-item dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
			
            <img src="<?php if(!empty($_SESSION['PHOTO'])){ echo "image/".$_SESSION['PHOTO']; }else{ echo $organizationlogo; } ?>" class="img-circle " style="height: auto;width: 2.1rem;" alt="<?php echo $_SESSION['LOGINNAME']; ?> ">
                            <span class="hidden-xs"><?php echo $_SESSION['LOGINNAME']; ?></span>
            </a>
            <ul class="dropdown-menu" >
              <!-- User image -->
              <li class="user-header">
                              <img src="<?php if(!empty($_SESSION['PHOTO'])){ echo "image/".$_SESSION['PHOTO']; }else{ echo $organizationlogo; } ?>" class="img-circle" alt="<?php echo $_SESSION['LOGINNAME']; ?>" style="height: auto;
    width: 3rem;">
                <p>
                  <?php echo $_SESSION['LOGINNAME']; if(!empty($_SESSION['DESIGNATION'])){ echo "-".$_SESSION['DESIGNATION'];} ?>                 
                </p>
              </li>
              <!-- Menu Body -->
              
              <!-- Menu Footer-->
              <li class="user-footer">
                <div class="pull-left">
                  <a href="#" class="btn btn-default btn-flat">Profile</a>
                </div>
                <div class="pull-right">
                  <a href="?<?php echo $page_title; ?>/SessionDistroy" class="btn btn-default btn-flat">Sign out</a>
                </div>
              </li>
            </ul>
          </li>
		<!--User Login End-->	
		
		
    </ul>
  </nav>