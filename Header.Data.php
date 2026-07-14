<nav class="main-header navbar navbar-expand navbar-white navbar-light app-topbar">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link app-nav-icon" data-widget="pushmenu" href="#" role="button" aria-label="Toggle sidebar"><i class="fas fa-bars"></i></a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <a href="<?php echo $base_url; ?>" class="nav-link app-home-link">Home</a>
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
      <li class="nav-item dropdown app-notification-dropdown">
        <a class="nav-link app-notification-toggle" data-toggle="dropdown" href="#" aria-label="Notifications">
          <i class="far fa-bell"></i>
			<?php
			$header_distribution_pending_filter="(requestion_histiory.`distribution_status`='Pending' OR EXISTS (SELECT 1 FROM requestion_detail rd WHERE rd.invoice_id=requestion_histiory.invoice_id AND rd.deleted_at IS NULL AND ((requestion_histiory.requistion_type='Fund' AND CAST(COALESCE(NULLIF(rd.due_amount,''),0) AS DECIMAL(18,4))>0) OR ((requestion_histiory.requistion_type IS NULL OR requestion_histiory.requistion_type<>'Fund') AND CAST(COALESCE(NULLIF(rd.due_quantity,''),0) AS DECIMAL(18,4))>0))))";
			if(!empty($row_Login_designation_information["name"]) && $row_Login_designation_information["name"]=='Store Keeper'){ 					
			$informationpurchage_calculation = $pdo->query("SELECT count(id) AS pending_number_list FROM `requestion_histiory` where ".$header_distribution_pending_filter." and store_id='".$row_Login_Datauser_information["store_id"]."' and approval_status='Approve' and requestion_histiory.deleted_at is NULL");
			 }else{
			$informationpurchage_calculation = $pdo->query("SELECT count(id) AS pending_number_list FROM `project_material_aproval_status` where `approval_status`='Pending' and employee_id='".$_SESSION['LoginReGiSterSession']."' and deleted_at is NULL");		
			}
			
			
			
			$DataVerificationNumber_total_list=(int)$informationpurchage_calculation->fetchColumn();
			$receivePendingNotificationStatement=$pdo->prepare("SELECT COUNT(id) FROM distribution_summary WHERE assign_receiver_id=:user_id AND deleted_at IS NULL AND (received_status IS NULL OR received_status='Pending' OR received_status='Partial')");
			$receivePendingNotificationStatement->execute(array(':user_id'=>$_SESSION['LoginReGiSterSession']));
			$receivePendingNotificationCount=(int)$receivePendingNotificationStatement->fetchColumn();
			$DataVerificationNumber_total_list+=$receivePendingNotificationCount;
			$emergencyNotificationStatement=$pdo->prepare("SELECT COUNT(id) FROM emergency_request_notification WHERE recipient_id=:user_id AND actioned_at IS NULL");
			$emergencyNotificationStatement->execute(array(':user_id'=>$_SESSION['LoginReGiSterSession']));
			$DataVerificationNumber_total_list+=(int)$emergencyNotificationStatement->fetchColumn();
			?>
			
			
          <span class="badge badge-warning navbar-badge"><?php echo $DataVerificationNumber_total_list; ?></span>
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right app-notification-menu">
          <span class="dropdown-item dropdown-header app-notification-header">
            <span><?php echo $DataVerificationNumber_total_list; ?> Notifications</span>
          </span>
	          <div class="app-notification-list">
	          <?php
	          try{
	          	$receivePendingNotifications=$pdo->prepare("SELECT distribution_summary.invoice_id,distribution_summary.distribution_id,distribution_summary.date,project_information.name AS project_name FROM distribution_summary INNER JOIN project_information ON project_information.id=distribution_summary.project_id WHERE distribution_summary.assign_receiver_id=:user_id AND distribution_summary.deleted_at IS NULL AND (distribution_summary.received_status IS NULL OR distribution_summary.received_status='Pending' OR distribution_summary.received_status='Partial') ORDER BY distribution_summary.id DESC LIMIT 5");
	          	$receivePendingNotifications->execute(array(':user_id'=>$_SESSION['LoginReGiSterSession']));
	          }catch(Exception $notificationError){
	          	$receivePendingNotifications=false;
	          }
	          while($receivePendingNotifications && $receiveNotification=$receivePendingNotifications->fetch()){ ?>
	          <a href="?Material_Received_Status_Create/<?php echo urlencode($receiveNotification['invoice_id']); ?>/<?php echo urlencode($receiveNotification['distribution_id']); ?>" class="dropdown-item app-notification-item">
	            <span class="app-notification-icon text-success"><i class="fas fa-dolly"></i></span>
	            <span class="app-notification-copy"><span class="app-notification-title"><?php echo htmlspecialchars($receiveNotification['distribution_id'].' - '.$receiveNotification['project_name']); ?></span><span class="app-notification-date">Material receive pending - <?php echo date("d-m-Y", strtotime($receiveNotification['date'])); ?></span></span>
	          </a>
	          <?php }
	          try{
	          	$emergencyNotifications=$pdo->prepare("SELECT emergency_request_notification.stage,emergency_request.id,emergency_request.request_no,emergency_request.date,project_information.name AS project_name FROM emergency_request_notification INNER JOIN emergency_request ON emergency_request.id=emergency_request_notification.emergency_request_id INNER JOIN project_information ON project_information.id=emergency_request.project_id WHERE emergency_request_notification.recipient_id=:user_id AND emergency_request_notification.actioned_at IS NULL AND emergency_request.deleted_at IS NULL ORDER BY emergency_request_notification.id DESC LIMIT 5");
	          	$emergencyNotifications->execute(array(':user_id'=>$_SESSION['LoginReGiSterSession']));
	          }catch(Exception $notificationError){
	          	$emergencyNotifications=false;
	          }
	          while($emergencyNotifications && $emergencyNotification=$emergencyNotifications->fetch()){ ?>
          <a href="?Emergency_Request_Detail/<?php echo (int)$emergencyNotification['id']; ?>" class="dropdown-item app-notification-item">
            <span class="app-notification-icon text-danger"><i class="fas fa-bolt"></i></span>
            <span class="app-notification-copy"><span class="app-notification-title"><?php echo htmlspecialchars($emergencyNotification['request_no'].' - '.$emergencyNotification['project_name']); ?></span><span class="app-notification-date"><?php echo $emergencyNotification['stage']==='receiver_acknowledgement'?'Receipt acknowledgement required':'Reference confirmation required'; ?></span></span>
          </a>
          <?php } ?>
         
			
			
			<?php
			if(!empty($row_Login_designation_information["name"]) && $row_Login_designation_information["name"]=='Store Keeper'){ 					
				$informationpurchage = $pdo->query("SELECT requestion_histiory.invoice_id,requestion_histiory.date,project_information.name AS project_name,store_information.name AS store_name FROM `requestion_histiory` INNER JOIN project_information ON requestion_histiory.project_id=project_information.id INNER JOIN store_information ON requestion_histiory.store_id=store_information.id where ".$header_distribution_pending_filter." and store_id='".$row_Login_Datauser_information["store_id"]."' and approval_status='Approve' and requestion_histiory.deleted_at is NULL ORDER BY requestion_histiory.id DESC LIMIT 8");
				 }else{
				$informationpurchage = $pdo->query("SELECT requestion_histiory.invoice_id,requestion_histiory.date,project_information.name AS project_name,store_information.name AS store_name FROM `project_material_aproval_status` INNER JOIN requestion_histiory ON project_material_aproval_status.invoice_id=requestion_histiory.invoice_id INNER JOIN project_information ON requestion_histiory.project_id=project_information.id INNER JOIN store_information ON requestion_histiory.store_id=store_information.id  where project_material_aproval_status.`approval_status`='Pending' and project_material_aproval_status.employee_id='".$_SESSION['LoginReGiSterSession']."' and project_material_aproval_status.deleted_at is NULL and requestion_histiory.deleted_at is NULL ORDER BY project_material_aproval_status.id DESC LIMIT 8");	
				
			}
				
				
			
			
            while ($rowdatapurchage = $informationpurchage->fetch()){ ?>
          <a href="?Requestion_History_Detail/<?php echo $rowdatapurchage["invoice_id"]; ?>" class="dropdown-item app-notification-item">
            <span class="app-notification-icon"><i class="fas fa-users"></i></span>
            <span class="app-notification-copy">
              <span class="app-notification-title"><?php echo $rowdatapurchage["project_name"];  ?></span>
              <span class="app-notification-date"><?php echo date("d-m-Y", strtotime($rowdatapurchage["date"])); ?></span>
            </span>
          </a>
			<?php } ?>
          </div>

          <a href="?Requestion" class="dropdown-item dropdown-footer app-notification-footer">See All Notifications</a>
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
		<!--User Login Start-->
		<li class="nav-item app-user-inline">
          <span class="app-user-toggle app-user-identity" title="<?php echo htmlspecialchars($_SESSION['LOGINNAME'], ENT_QUOTES, 'UTF-8'); ?><?php if(!empty($_SESSION['DESIGNATION'])){ echo ' - '.htmlspecialchars($_SESSION['DESIGNATION'], ENT_QUOTES, 'UTF-8'); } ?>">
            <img src="<?php if(!empty($_SESSION['PHOTO'])){ echo "HRPhoto/".$_SESSION['PHOTO']; }else{ echo $organizationlogo; } ?>" class="img-circle" alt="<?php echo htmlspecialchars($_SESSION['LOGINNAME'], ENT_QUOTES, 'UTF-8'); ?>">
            <span class="hidden-xs"><?php echo htmlspecialchars($_SESSION['LOGINNAME'], ENT_QUOTES, 'UTF-8'); ?></span>
          </span>
          <a href="?<?php echo urlencode($page_title); ?>/SessionDistroy"
             class="nav-link app-signout-link"
             aria-label="Sign out"
             title="Sign out"
             data-app-confirm="Are you sure you want to sign out of the system?"
             data-confirm-title="Sign out?"
             data-confirm-button="Yes, sign out">
            <i class="fas fa-sign-out-alt" aria-hidden="true"></i>
          </a>
        </li>
		<!--User Login End-->	
		
		
    </ul>
  </nav>
