
<?php
$editinformation = $pdo->query("SELECT employee_information.*,hr_department.name AS department,hr_designation.name AS designation FROM employee_information inner join hr_designation ON employee_information.designation=hr_designation.id INNER JOIN hr_department ON employee_information.department=hr_department.id WHERE user_status='Active' and employee_information.DELETED_AT is NULL and  employee_information.id='$DocumentData'");
$editrowdata = $editinformation->fetch();
$menupermission=$editrowdata["menu_access"];

if(!empty($menupermission)){
       $distribution_area_dataUser=json_decode($menupermission,true);
         }

$menuGroups=array(
  'Workflow'=>array('Requisition Draft','Emergency Request','Requestion','My Approved Requisition','Distribution','Distribution Pending','Material Received Status','Distribution List','Distribution History'),
  'Inventory'=>array('Purchase History','Stock','Indivisual Stock','Return History','Project Material Used History','Stock Transfer'),
  'Reports'=>array('Report'),
  'Master Data'=>array('Setting','Menu Information','Store Information','Product Unit','Product Category','Product Information','Asset Product Information','Supplier','Project Information','Project Material Approval Information'),
  'Administration'=>array('User Information','Asset Information','Stock Detail Information'),
  'Human Resources'=>array('HR')
);
function roleMenuGroupName($menuName,$menuGroups){
  foreach($menuGroups as $groupName=>$groupMenus){
    if(in_array($menuName,$groupMenus,true)){ return $groupName; }
  }
  return 'Other';
}
?>

<style>
.permission-toolbar { align-items: center; display: flex; gap: 12px; justify-content: space-between; padding: 12px 0; }
.permission-search { max-width: 360px; }
.permission-summary { color: #475569; font-size: 13px; font-weight: 600; white-space: nowrap; }
.permission-group { color: #64748b; font-size: 12px; font-weight: 700; text-transform: uppercase; }
.permission-menu-name { color: #1f2937; font-weight: 600; }
@media screen and (max-width: 575.98px) { .permission-toolbar { align-items: stretch; flex-direction: column; } .permission-search { max-width: none; } }
</style>

<section class="content">

      <!-- Default box -->
      <div class="card">
        <div class="card-header">
          <h3 class="card-title"><?php echo str_replace("_"," ",$page_title); ?></h3>

          <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
              <i class="fas fa-minus"></i>
            </button>
            <button type="button" class="btn btn-tool" data-card-widget="remove" title="Remove">
              <i class="fas fa-times"></i>
            </button>
			
          </div>
        </div>
        <div class="card-body p-0">
          <form method="post" action="?User_information/Setting/employee_information/<?php echo $DocumentData; ?>" enctype="multipart/form-data">
        <!-- SELECT2 EXAMPLE -->
        <div class="card card-default">
        
          <!-- /.card-header -->
          <div class="card-body">
            <div class="row">
				<div class="col-md-12"><p style="text-align:center; color:#F00;">***All * marked fields are required***</p></div>
				
				
				<div class="col-md-12">
					<div class="row">
					<div class="col-md-4">
				  <div class="form-group">
					 
					<label for="NAME">Name&nbsp;:&nbsp;</label> <?php echo $editrowdata["name_bn"]; ?>
				  </div>
              </div>
 
              <div class="col-md-4">
                <div class="form-group">
					<label for="DESIGNATION">Designation&nbsp;:&nbsp;</label><?php echo $editrowdata["designation"]; ?>
				  </div>
              </div>
				<div class="col-md-4">
                <div class="form-group">
					<label for="DESIGNATION">Email&nbsp;:&nbsp;</label><?php echo $editrowdata["email"]; ?>
				  </div>
              </div>	
					
					
					</div>
				
				</div>
				
				<div class="col-md-12">
				<div class="permission-toolbar">
				  <div class="input-group permission-search">
					<div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-search"></i></span></div>
					<input type="search" id="menu-permission-search" class="form-control" placeholder="Search menu">
				  </div>
				  <div class="permission-summary"><span id="selected-menu-count">0</span> menu(s) selected</div>
				</div>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover" >
                      <thead>
                            <tr>
                                <th>
                                 SL
                                </th>
                                <th>
                              <input type="checkbox" id="selectall"/>&nbsp;Menu
                                </th>
                                <th>Category</th>
                                
                
                            </tr>
                            
                        </thead> 
                        <tbody aria-relevant="all" aria-live="polite" role="alert">
              <?php
			 $informationsubhead = $pdo->query("SELECT * FROM menu_information WHERE deleted_at IS NULL ORDER BY name ASC");
						$sub_head_id=1;
			 while ($rowdatasubhead = $informationsubhead->fetch()){
						?>	

                        <tr class="permission-menu-row" data-menu-name="<?php echo htmlspecialchars(strtolower($rowdatasubhead['name'])); ?>">
                    <td text align="left"><?php echo $sub_head_id; ?></td>

                    <td text align="left"><input type="checkbox" class="case" <?php if (!empty($distribution_area_dataUser) && in_array($rowdatasubhead["name"], $distribution_area_dataUser)){echo "checked";}  ?> name="menupermission<?php echo $sub_head_id; ?>" value="<?php echo htmlspecialchars($rowdatasubhead["name"]); ?>"/>&nbsp;&nbsp;&nbsp;<span class="permission-menu-name"><?php echo htmlspecialchars($rowdatasubhead["name"]); ?></span></td>
                    <td><span class="permission-group"><?php echo htmlspecialchars(roleMenuGroupName($rowdatasubhead['name'],$menuGroups)); ?></span></td>

                        </tr>
							
				<?php 
			 $sub_head_id++;
			 } ?>	 

							
                        <SCRIPT language="javascript">
$(function(){
	function updatePermissionSummary(){
		var visibleCases=$('.permission-menu-row:visible .case');
		$('#selected-menu-count').text($('.case:checked').length);
		$('#selectall').prop('checked',visibleCases.length>0 && visibleCases.length===visibleCases.filter(':checked').length);
	}
	$('#selectall').on('change',function(){
		$('.permission-menu-row:visible .case').prop('checked',this.checked);
		updatePermissionSummary();
	});
	$('.case').on('change',updatePermissionSummary);
	$('#menu-permission-search').on('input',function(){
		var term=$(this).val().toLowerCase().trim();
		$('.permission-menu-row').each(function(){
			$(this).toggle(!term || $(this).data('menu-name').indexOf(term)!==-1);
		});
		updatePermissionSummary();
	});
	updatePermissionSummary();
});
</SCRIPT>
                        </tbody>
                        
                   
                         
                        
                    </table>
                </div>
              </div>
				
           
             
             
				
				
              <!-- /.col -->
			  </div>
            <!-- /.row -->
			  
			  
          </div>
          <!-- /.card-body -->
          <div class="card-footer">
          <div class="box-tools pull-right">
       <button type="button" class="btn btn-warning" onclick="history.back(-1)"><i class="fa fa-window-close"></i> Cancel</button>&nbsp;&nbsp;&nbsp;
                    <button class="btn btn-primary" type="submit" name="user_menu_permission_edit"><i class="fa fa-save"></i> Save </button>
                </div>
          </div>
        </div>
        <!-- /.card -->
</form>
        </div>
        <!-- /.card-body -->
      </div>
      <!-- /.card -->

    </section>
