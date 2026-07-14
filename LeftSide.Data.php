<?php
$sidebar_is_admin=!empty($_SESSION['USER_TYPE']) && $_SESSION['USER_TYPE']==='Admin';
if(!function_exists('sidebarCanAccess')){
  function sidebarCanAccess($menuAccess,$menuName,$isAdmin=false){
    return $isAdmin || (is_array($menuAccess) && in_array($menuName,$menuAccess,true));
  }
}
$sidebar_draft_count=0;
$sidebar_requisition_pending_count=0;
$sidebar_emergency_pending_count=0;
$sidebar_receive_pending_count=0;
if(!empty($_SESSION['LoginReGiSterSession'])){
  $sidebar_user_id=$_SESSION['LoginReGiSterSession'];
  $sidebar_cache_key='sidebar_counts_'.$sidebar_user_id.'_'.(!empty($_SESSION['USER_TYPE']) ? $_SESSION['USER_TYPE'] : 'User');
  $sidebar_cache=!empty($_SESSION[$sidebar_cache_key]) && is_array($_SESSION[$sidebar_cache_key]) ? $_SESSION[$sidebar_cache_key] : null;
  if($sidebar_cache && !empty($sidebar_cache['expires_at']) && $sidebar_cache['expires_at']>=time()){
    $sidebar_draft_count=(int)$sidebar_cache['draft'];
    $sidebar_requisition_pending_count=(int)$sidebar_cache['requisition'];
    $sidebar_emergency_pending_count=(int)$sidebar_cache['emergency'];
    $sidebar_receive_pending_count=!empty($sidebar_cache['receive']) ? (int)$sidebar_cache['receive'] : 0;
  }else{
    $sidebar_emergency_statement=$pdo->prepare("SELECT COUNT(id) FROM emergency_request_notification WHERE recipient_id=:user_id AND actioned_at IS NULL");
    $sidebar_emergency_statement->execute(array(':user_id'=>$sidebar_user_id));
    $sidebar_emergency_pending_count=(int)$sidebar_emergency_statement->fetchColumn();
    if(!empty($_SESSION['USER_TYPE']) && $_SESSION['USER_TYPE']=='Admin'){
      $sidebar_draft_count=(int)$pdo->query("SELECT COUNT(id) FROM requestion_draft_histiory WHERE final_submit_status IS NULL AND deleted_at IS NULL")->fetchColumn();
      $sidebar_requisition_pending_count=(int)$pdo->query("SELECT COUNT(id) FROM requestion_histiory WHERE approval_status='Pending' AND deleted_at IS NULL")->fetchColumn();
      $sidebar_receive_pending_count=(int)$pdo->query("SELECT COUNT(id) FROM distribution_summary WHERE deleted_at IS NULL AND (received_status IS NULL OR received_status='Pending' OR received_status='Partial')")->fetchColumn();
    }else{
      $sidebar_draft_count=(int)$pdo->query("SELECT COUNT(id) FROM requestion_draft_histiory WHERE employee_id='".$sidebar_user_id."' AND final_submit_status IS NULL AND deleted_at IS NULL")->fetchColumn();
      $sidebar_requisition_pending_count=(int)$pdo->query("SELECT COUNT(project_material_aproval_status.id) FROM project_material_aproval_status INNER JOIN requestion_histiory ON project_material_aproval_status.invoice_id=requestion_histiory.invoice_id WHERE project_material_aproval_status.employee_id='".$sidebar_user_id."' AND project_material_aproval_status.approval_status='Pending' AND project_material_aproval_status.deleted_at IS NULL AND requestion_histiory.deleted_at IS NULL")->fetchColumn();
      $sidebar_receive_statement=$pdo->prepare("SELECT COUNT(id) FROM distribution_summary WHERE assign_receiver_id=:user_id AND deleted_at IS NULL AND (received_status IS NULL OR received_status='Pending' OR received_status='Partial')");
      $sidebar_receive_statement->execute(array(':user_id'=>$sidebar_user_id));
      $sidebar_receive_pending_count=(int)$sidebar_receive_statement->fetchColumn();
    }
    $_SESSION[$sidebar_cache_key]=array(
      'draft'=>$sidebar_draft_count,
      'requisition'=>$sidebar_requisition_pending_count,
      'emergency'=>$sidebar_emergency_pending_count,
      'receive'=>$sidebar_receive_pending_count,
      'expires_at'=>time()+15
    );
  }
}
?>
<aside class="main-sidebar sidebar-dark-primary elevation-4 app-sidebar">
    <!-- Brand Logo -->
    <a href="<?php echo $base_url; ?>" class="brand-link">
      <img src="<?php echo $feviconicon; ?>" alt="<?php echo $organization_name; ?>" class="brand-image img-circle elevation-3" style="opacity: .8">
      <span class="brand-text font-weight-light"><?php echo $organization_name; ?> </span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="<?php if(!empty($_SESSION['PHOTO'])){ echo "HRPhoto/".$_SESSION['PHOTO']; }else{ echo $organizationlogo; } ?>" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
          <a href="#" class="d-block"><?php echo $_SESSION['LOGINNAME']; ?></a>
        </div>
      </div>

      <!-- SidebarSearch Form -->
      

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
          <li class="nav-item menu-open">
            <a href="<?php echo $base_url; ?>" class="nav-link <?php if(empty($page_title)){ echo "active"; } ?>">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>
                Dashboard
              
              </p>
            </a>
            
          </li>
          
          <?php if (!empty($menu_access) && in_array("Requisition Draft", $menu_access)){ ?>
          <li class="nav-item">
            <a href="?Requisition_Draft" class="nav-link <?php if(!empty($page_title) && ($page_title=='Requisition_Draft' or $page_title=='Requisition_Draft_Create' or $page_title=='Requisition_Draft_Edit')){ echo "active"; } ?>">
              <i class="nav-icon fas fa-list"></i>
              <p>
                Requisition Draft
                <span class="right badge badge-info"><?php echo $sidebar_draft_count; ?></span>
              </p>
            </a>
          </li>
			<?php } ?>
		  <?php if(sidebarCanAccess($menu_access,'Emergency Request',$sidebar_is_admin)){ ?>
		  <li class="nav-item">
            <a href="?Emergency_Request" class="nav-link <?php if(!empty($page_title) && in_array($page_title,array('Emergency_Request','Emergency_Request_Create','Emergency_Request_Detail'))){ echo 'active'; } ?>">
              <i class="nav-icon fas fa-bolt"></i>
              <p>Emergency Request<?php if($sidebar_emergency_pending_count>0){ ?><span class="right badge badge-danger"><?php echo $sidebar_emergency_pending_count; ?></span><?php } ?></p>
            </a>
          </li>
		  <?php } ?>
			<?php if (!empty($menu_access) && in_array("Requestion", $menu_access)){ ?>
          <li class="nav-item">
            <a href="?Requestion" class="nav-link <?php if(!empty($page_title) && ($page_title=='Requestion' or $page_title=='Requestion_Create' or $page_title=='Requestion_Edit')){ echo "active"; } ?>">
              <i class="nav-icon fas fa-th"></i>
              <p>
                Requisition
                <span class="right badge badge-warning"><?php echo $sidebar_requisition_pending_count; ?></span>
              </p>
            </a>
          </li>
			<?php } ?>
		  <?php if(sidebarCanAccess($menu_access,'My Approved Requisition',$sidebar_is_admin)){ ?>
          <li class="nav-item">
            <a href="?My_Approved_Requisition" class="nav-link <?php if(!empty($page_title) && ($page_title=='My_Approved_Requisition')){ echo "active"; } ?>">
              <i class="nav-icon fas fa-check-circle"></i>
              <p>
                My Approved Requisition
              </p>
            </a>
          </li>
		  <?php } ?>
			
			<?php if (!empty($menu_access) && in_array("Distribution Pending", $menu_access)){ ?>
			<li class="nav-item">
            <a href="?Distribution_Pending" class="nav-link <?php if(!empty($page_title) && ($page_title=='Distribution_Pending' or $page_title=='Distribution_Pending_Create' or $page_title=='Distribution_Pending_Edit')){ echo "active"; } ?>">
              <i class="nav-icon fas fa-sitemap"></i>
              <p>
                Distribution Pending
              </p>
            </a>
          </li>
			<?php } ?>
			
			<?php if (!empty($menu_access) && in_array("Material Received Status", $menu_access)){ ?>
			<li class="nav-item">
            <a href="?Material_Received_Status" class="nav-link <?php if(!empty($page_title) && ($page_title=='Material_Received_Status' or $page_title=='Material_Received_Status_Create' or $page_title=='Material_Received_Status_Edit')){ echo "active"; } ?>">
              <i class="nav-icon fas fa-bold"></i>
              <p>
                Material Received Status
                <?php if($sidebar_receive_pending_count>0){ ?><span class="right badge badge-success"><?php echo $sidebar_receive_pending_count; ?></span><?php } ?>
              </p>
            </a>
          </li>
			<?php } ?>
			
			<?php if (!empty($menu_access) && in_array("Distribution List", $menu_access)){ ?>
			<li class="nav-item">
            <a href="?Distribution_List" class="nav-link <?php if(!empty($page_title) && ($page_title=='Distribution_List' or $page_title=='Distribution_List_Detail' or $page_title=='Distribution_List_indivisual')){ echo "active"; } ?>">
              <i class="nav-icon fas fa-book"></i>
              <p>
                Distribution List
              </p>
            </a>
          </li>
			<?php } ?>
			
			<?php if (!empty($menu_access) && in_array("Distribution History", $menu_access)){ ?>
			<li class="nav-item">
            <a href="?Distribution_History" class="nav-link <?php if(!empty($page_title) && ($page_title=='Distribution_History')){ echo "active"; } ?>">
              <i class="nav-icon fas fa-briefcase"></i>
              <p>
                Distribution History
              </p>
            </a>
          </li>
			<?php } ?>
			
			
			<?php if (!empty($menu_access) && in_array("Purchase History", $menu_access)){ ?>
			<li class="nav-item">
            <a href="?Purchase_History" class="nav-link <?php if(!empty($page_title) && ($page_title=='Purchase_History' or $page_title=='Purchase_History_Create' or $page_title=='Purchase_History_Edit')){ echo "active"; } ?>">
              <i class="nav-icon fas fa-shopping-cart"></i>
              <p>Purchase History
              </p>
            </a>
          </li>
			<?php } ?>
			
	<?php if (!empty($menu_access) && in_array("Stock", $menu_access)){ ?>
			<li class="nav-item">
            <a href="?Stock" class="nav-link <?php if(!empty($page_title) && ($page_title=='Stock')){ echo "active"; } ?>">
              <i class="nav-icon fas fa-industry"></i>
              <p>Stock
              </p>
            </a>
          </li>
		<?php } ?>
		<?php if (!empty($menu_access) && in_array("Indivisual Stock", $menu_access)){ ?>
			<li class="nav-item">
            <a href="?Indivisual_Stock" class="nav-link <?php if(!empty($page_title) && ($page_title=='Indivisual_Stock')){ echo "active"; } ?>">
              <i class="nav-icon fas fa-igloo"></i>
              <p>Indivisual Stock
              </p>
            </a>
          </li>
		<?php } ?>	
		
		<?php if (!empty($menu_access) && in_array("Return History", $menu_access)){ ?>
			<li class="nav-item">
            <a href="?Return_History" class="nav-link <?php if(!empty($page_title) && ($page_title=='Return_History' or $page_title=='Return_History_Create' or $page_title=='Return_History_View')){ echo "active"; } ?>">
              <i class="nav-icon fas fa-undo"></i>
              <p>Return History</p>
            </a>
          </li>
		<?php } ?>
		
		<?php if (!empty($menu_access) && in_array("Project Material Used History", $menu_access)){ ?>
			<li class="nav-item">
            <a href="?Project_Material_Used_History" class="nav-link <?php if(!empty($page_title) && ($page_title=='Project_Material_Used_History' or $page_title=='Project_Material_Used_History_Create' or $page_title=='Project_Material_Used_History_View')){ echo "active"; } ?>">
              <i class="nav-icon fas fa-anchor"></i>
              <p>Material Used History</p>
            </a>
          </li>
		<?php } ?>
			
		<?php if (!empty($menu_access) && in_array("Stock Transfer", $menu_access)){ ?>	
		<li class="nav-item <?php if(!empty($MenuName) && $MenuName=='Transfer'){ echo "menu-open"; } ?>">
            <a href="#" class="nav-link <?php if(!empty($MenuName) && $MenuName=='Transfer'){ echo "active"; } ?>">
              <i class="nav-icon fas fa-copy"></i>
              <p>
                Stock Transfer
                <i class="fas fa-angle-left right"></i>
               
              </p>
            </a>
            <ul class="nav nav-treeview">
             
		    <li class="nav-item ">
                <a href="?Stock_Transfer/Transfer" class="nav-link <?php if(!empty($page_title) && ($page_title=='Stock_Transfer' or $page_title=='Stock_Transfer_Create' or $page_title=='Stock_Transfer_Edit')){ echo "active"; } ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Transfer</p>
                </a>
              </li>
              <li class="nav-item ">
                <a href="?Stock_Transfer_Received_Pending_List/Transfer" class="nav-link <?php if(!empty($page_title) && ($page_title=='Stock_Transfer_Received_Pending_List'  or $page_title=='Stock_Transfer_Received_Pending_List_Create' or $page_title=='Stock_Transfer_Received_Pending_List_Edit' )){ echo "active"; } ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Received Pending</p>
                </a>
              </li>

              <li class="nav-item ">
                <a href="?Stock_Transfer_List/Transfer" class="nav-link <?php if(!empty($page_title) && ($page_title=='Stock_Transfer_List' )){ echo "active"; } ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Transfer List</p>
                </a>
              </li>
			<li class="nav-item ">
                <a href="?Stock_Transfer_History/Transfer" class="nav-link <?php if(!empty($page_title) && ($page_title=='Stock_Transfer_History')){ echo "active"; } ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Stock Transfer History</p>
                </a>
              </li>	
		
				
				
				
        
            </ul>
          </li>	
		<?php } ?>	
			
		
			
		<?php if(sidebarCanAccess($menu_access,'Report',$sidebar_is_admin)){ ?>
          <li class="nav-item <?php if(!empty($MenuName) && $MenuName=='Report'){ echo "menu-open"; } ?>">
            <a href="#" class="nav-link <?php if(!empty($MenuName) && $MenuName=='Report'){ echo "active"; } ?>">
              <i class="nav-icon fas fa-comments"></i>
              <p>
                Report
                <i class="fas fa-angle-left right"></i>
               
              </p>
            </a>
            <ul class="nav nav-treeview">
             
		    <li class="nav-item ">
                <a href="?Purchase_Report/Report" class="nav-link <?php if(!empty($page_title) && ($page_title=='Purchase_Report' )){ echo "active"; } ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Purchase Report</p>
                </a>
              </li>

              <li class="nav-item ">
                <a href="?Product_Lifecycle_Report/Report" class="nav-link <?php if(!empty($page_title) && ($page_title=='Product_Lifecycle_Report' )){ echo "active"; } ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Product Lifecycle</p>
                </a>
              </li>

              <li class="nav-item ">
                <a href="?Project_Reconciliation_Report/Report" class="nav-link <?php if(!empty($page_title) && ($page_title=='Project_Reconciliation_Report' )){ echo "active"; } ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Project Reconciliation</p>
                </a>
              </li>

              <li class="nav-item ">
                <a href="?Product_Usage_Search/Report" class="nav-link <?php if(!empty($page_title) && ($page_title=='Product_Usage_Search' )){ echo "active"; } ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Product Usage Search</p>
                </a>
              </li>

              <li class="nav-item ">
                <a href="?Distribution_Report/Report" class="nav-link <?php if(!empty($page_title) && ($page_title=='Distribution_Report')){ echo "active"; } ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Distribution Report</p>
                </a>
              </li>
			 <li class="nav-item ">
                <a href="?Project_Wise_Distribution/Report" class="nav-link <?php if(!empty($page_title) && ($page_title=='Project_Wise_Distribution')){ echo "active"; } ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Project Wise Distribution</p>
                </a>
              </li>
			<li class="nav-item ">
                <a href="?Project_Wise_Used/Report" class="nav-link <?php if(!empty($page_title) && ($page_title=='Project_Wise_Used')){ echo "active"; } ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Project Wise Used</p>
                </a>
              </li>
			<li class="nav-item ">
                <a href="?Store_Wise_Distribution/Report" class="nav-link <?php if(!empty($page_title) && ($page_title=='Store_Wise_Distribution')){ echo "active"; } ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Store Wise Distribution</p>
                </a>
              </li>	
			
				<li class="nav-item ">
                <a href="?Store_Wise_Return_Report/Report" class="nav-link <?php if(!empty($page_title) && ($page_title=='Store_Wise_Return_Report')){ echo "active"; } ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Return Report</p>
                </a>
              </li>		
				
				<li class="nav-item ">
                <a href="?Date_Wise_individual_Stock/Report" class="nav-link <?php if(!empty($page_title) && ($page_title=='Date_Wise_individual_Stock')){ echo "active"; } ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Date Wise individual Stock</p>
                </a>
              </li>	
              <li class="nav-item ">
                <a href="?Requisition_History/Report" class="nav-link <?php if(!empty($page_title) && ($page_title=='Requisition_History')){ echo "active"; } ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Requisition</p>
                </a>
              </li>	
              
				
        
            </ul>
          </li>
		<?php }  ?>		
			
			
			
			
			
			
			
			
			
			
			
			
          
			
		<?php
		$settingMenuKeys=array('Setting','Menu Information','Store Information','Product Unit','Product Category','Product Information','Asset Product Information','Supplier','Project Information','Project Material Approval Information');
		$canOpenSettings=$sidebar_is_admin;
		foreach($settingMenuKeys as $settingMenuKey){
		  if(sidebarCanAccess($menu_access,$settingMenuKey,$sidebar_is_admin)){ $canOpenSettings=true; break; }
		}
		if($canOpenSettings){ ?>
          <li class="nav-item <?php if(!empty($MenuName) && $MenuName=='Setting'){ echo "menu-open"; } ?>">
            <a href="#" class="nav-link <?php if(!empty($MenuName) && $MenuName=='Setting'){ echo "active"; } ?>">
              <i class="nav-icon fas fa-clock"></i>
              <p>
                Setting
                <i class="fas fa-angle-left right"></i>
               
              </p>
            </a>
            <ul class="nav nav-treeview">
             
		    <?php if(sidebarCanAccess($menu_access,'Menu Information',$sidebar_is_admin)){ ?><li class="nav-item ">
                <a href="?Menu_Information/Setting" class="nav-link <?php if(!empty($page_title) && ($page_title=='Menu_Information' or $page_title=='Menu_Information_Create' or $page_title=='Menu_Information_Edit')){ echo "active"; } ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Menu Information</p>
                </a>
              </li><?php } ?>

              <?php if(sidebarCanAccess($menu_access,'Store Information',$sidebar_is_admin)){ ?><li class="nav-item ">
                <a href="?Store_Information/Setting" class="nav-link <?php if(!empty($page_title) && ($page_title=='Store_Information' or $page_title=='Store_Information_Create' or $page_title=='Store_Information_Edit')){ echo "active"; } ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Store Information</p>
                </a>
              </li><?php } ?>
			<?php if(sidebarCanAccess($menu_access,'Product Unit',$sidebar_is_admin)){ ?><li class="nav-item ">
                <a href="?Product_Unit/Setting" class="nav-link <?php if(!empty($page_title) && ($page_title=='Product_Unit' or $page_title=='Product_Unit_Create' or $page_title=='Product_Unit_Edit')){ echo "active"; } ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Product Unit</p>
                </a>
              </li><?php } ?>
              
              
            <?php if(sidebarCanAccess($menu_access,'Product Category',$sidebar_is_admin)){ ?><li class="nav-item ">
                <a href="?Product_Category/Setting" class="nav-link <?php if(!empty($page_title) && ($page_title=='Product_Category' or $page_title=='Product_Category_Create' or $page_title=='Product_Category_Edit')){ echo "active"; } ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Product Category</p>
                </a>
              </li><?php } ?>
              
              
			<?php if(sidebarCanAccess($menu_access,'Product Information',$sidebar_is_admin)){ ?><li class="nav-item ">
                <a href="?Product_Information/Setting" class="nav-link <?php if(!empty($page_title) && ($page_title=='Product_Information' or $page_title=='Product_Information_Create' or $page_title=='Product_Information_Edit')){ echo "active"; } ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Product Information</p>
                </a>
              </li><?php } ?>
				
			<?php if(sidebarCanAccess($menu_access,'Asset Product Information',$sidebar_is_admin)){ ?><li class="nav-item ">
                <a href="?Asset_Product_Information/Setting" class="nav-link <?php if(!empty($page_title) && ($page_title=='Asset_Product_Information' or $page_title=='Asset_Product_Information_Create' or $page_title=='Asset_Product_Information_Edit')){ echo "active"; } ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Asset Product Information</p>
                </a>
              </li><?php } ?>
				
				
			<?php if(sidebarCanAccess($menu_access,'Supplier',$sidebar_is_admin)){ ?><li class="nav-item ">
                <a href="?Supplier/Setting" class="nav-link <?php if(!empty($page_title) && ($page_title=='Supplier' or $page_title=='Supplier_Create' or $page_title=='Supplier_Edit')){ echo "active"; } ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Supplier</p>
                </a>
              </li><?php } ?>
			<?php if(sidebarCanAccess($menu_access,'Project Information',$sidebar_is_admin)){ ?><li class="nav-item ">
                <a href="?Project_Information/Setting" class="nav-link <?php if(!empty($page_title) && ($page_title=='Project_Information' or $page_title=='Project_Information_Create' or $page_title=='Project_Information_Edit')){ echo "active"; } ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Project Information</p>
                </a>
              </li><?php } ?>
				<?php if(sidebarCanAccess($menu_access,'Project Material Approval Information',$sidebar_is_admin)){ ?><li class="nav-item ">
                <a href="?Project_Material_Approval_Information/Setting" class="nav-link <?php if(!empty($page_title) && ($page_title=='Project_Material_Approval_Information' or $page_title=='Project_Material_Approval_Information_Create' or $page_title=='Project_Material_Approval_Information_Edit')){ echo "active"; } ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Project Material Approval Information</p>
                </a>
              </li><?php } ?>
				
				
				
				
        
            </ul>
          </li>
		<?php }  ?>	
			
         <?php if (!empty($menu_access) && in_array("HR", $menu_access)){ ?>	 
        <li class="nav-item <?php if(!empty($MenuName) && $MenuName=='HR'){ echo "menu-open"; } ?>">
        <a href="#" class="nav-link <?php if(!empty($MenuName) && $MenuName=='HR'){ echo "active"; } ?>">
              <i class="nav-icon fas fa-universal-access"></i>
              <p>
               HR
                <i class="fas fa-angle-left right"></i> 
              </p>
            </a>
            <ul class="nav nav-treeview">
            <?php if($_SESSION['USER_TYPE']=='Admin'){ ?>
            <li class="nav-item ">
                <a href="?HR_Department/HR" class="nav-link <?php if(!empty($page_title) && ($page_title=='HR_Department' or $page_title=='HR_Department_Create' or $page_title=='HR_Department_Edit')){ echo "active"; } ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Department</p>
                </a>
              </li>
              <li class="nav-item ">
                <a href="?HR_Designation/HR" class="nav-link <?php if(!empty($page_title) && ($page_title=='HR_Designation' or $page_title=='HR_Designation_Create' or $page_title=='HR_Designation_Edit')){ echo "active"; } ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Designation</p>
                </a>
              </li>
            <?php } ?>
		    <li class="nav-item ">
                <a href="?Employee_Information/HR" class="nav-link <?php if(!empty($page_title) && ($page_title=='Employee_Information' or $page_title=='Employee_Information_Create' or $page_title=='Employee_Information_Edit')){ echo "active"; } ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Employee Information</p>
                </a>
              </li>

              <?php /*?><li class="nav-item ">
                <a href="?Rostering_Information/HR" class="nav-link <?php if(!empty($page_title) && ($page_title=='Rostering_Information' or $page_title=='Rostering_Information_Create' or $page_title=='Rostering_Information_Edit')){ echo "active"; } ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Rostering Information</p>
                </a>
              </li>
              <?php */?>
              <li class="nav-item ">
                <a href="?Leave_Type/HR" class="nav-link <?php if(!empty($page_title) && ($page_title=='Leave_Type' or $page_title=='Leave_Type_Create' or $page_title=='Leave_Type_Edit')){ echo "active"; } ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Leave Type</p>
                </a>
              </li>
              
            
              <li class="nav-item ">
                <a href="?Leave_Information/HR" class="nav-link <?php if(!empty($page_title) && ($page_title=='Leave_Information' or $page_title=='Leave_Information_Create' or $page_title=='Leave_Information_Edit')){ echo "active"; } ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Leave Information</p>
                </a>
              </li>  
              
              <li class="nav-item ">
                <a href="?Leave_History/HR" class="nav-link <?php if(!empty($page_title) && ($page_title=='Leave_History' )){ echo "active"; } ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Leave History</p>
                </a>
              </li>  
              
              <?php /*?><li class="nav-item ">
                <a href="?Roster_History/HR" class="nav-link <?php if(!empty($page_title) && ($page_title=='Roster_History' )){ echo "active"; } ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Roster History</p>
                </a>
              </li> <?php */?> 
			
              
            </ul>
          </li> 
		<?php } ?>	
			
			<?php if(sidebarCanAccess($menu_access,'User Information',$sidebar_is_admin)){ ?>
			<li class="nav-item">
            <a href="?User_information" class="nav-link <?php if(!empty($page_title) && ($page_title=='User_information' or $page_title=='User_information_Edit')){ echo "active"; } ?>">
              <i class="nav-icon fas fa-user"></i>
              <p>User Information
              </p>
            </a>
          </li>
			<?php } ?>

		<?php if((string)$LoginReGiSterSession==='121'){ ?>
		<li class="nav-item">
          <a href="?Access_Control_Studio/Administration" class="nav-link <?php if(!empty($page_title) && $page_title==='Access_Control_Studio'){ echo 'active'; } ?>">
            <i class="nav-icon fas fa-user-shield"></i>
            <p>Access Control Studio</p>
          </a>
        </li>
		<?php } ?>

		<?php if(authIsSuperAdmin($LoginReGiSterSession)){ ?>
		<li class="nav-item">
          <a href="?System_Activity_Audit/Administration" class="nav-link <?php if(!empty($page_title) && $page_title==='System_Activity_Audit'){ echo 'active'; } ?>">
            <i class="nav-icon fas fa-shield-alt"></i>
            <p>System Activity Audit</p>
          </a>
        </li>
		<?php } ?>

		<?php if(sidebarCanAccess($menu_access,'Asset Information',$sidebar_is_admin)){ ?><li class="nav-item">
            <a href="?asset_information" class="nav-link <?php if(!empty($page_title) && ($page_title=='asset_information' or $page_title=='asset_information_Edit' or $page_title=='asset_information_Create')){ echo "active"; } ?>">
              <i class="nav-icon fas fa-th-list"></i>
              <p>Asset Information
              </p>
            </a>
          </li><?php } ?>

          <?php if(sidebarCanAccess($menu_access,'Stock Detail Information',$sidebar_is_admin)){ ?><li class="nav-item ">
                <a href="?Stock_Detail_Information" class="nav-link <?php if(!empty($page_title) && ($page_title=='Stock_Detail_Information' or  $page_title=='Stock_Detail_Information_Edit')){ echo "active"; } ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Stock Detail Information</p>
                </a>
              </li><?php } ?>
          
          
        
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>
