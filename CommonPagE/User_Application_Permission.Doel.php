<?php
$EditData = $pdo->query("SELECT employee_information.*,hr_department.name AS department,hr_designation.name AS designation FROM employee_information inner join hr_designation ON employee_information.designation=hr_designation.id INNER JOIN hr_department ON employee_information.department=hr_department.id WHERE  employee_information.DELETED_AT is NULL and employee_information.id='$DocumentData'");
$rowEditData = $EditData->fetch();
?>
<section class="content">
      <div class="container-fluid">
        <!-- SELECT2 EXAMPLE -->
        
        <!-- /.card -->
  <form method="post" action="?<?php echo  "User_information/".$MenuName."/".$DocumentData."/".$PageStatusCheck; ?>" enctype="multipart/form-data">
        <!-- SELECT2 EXAMPLE -->
        <div class="card card-default">
          <div class="card-header">
            <h3 class="card-title"> <button type="button" class="btn btn-warning" onclick="history.back(-1)"><i class="fa fa-reply"></i> back</button></h3>

            <div class="card-tools">
              <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
              </button>
              <button type="button" class="btn btn-tool" data-card-widget="remove">
                <i class="fas fa-times"></i>
              </button>
            </div>
          </div>
          <!-- /.card-header -->
          <div class="card-body">
            <div class="row">
				<div class="col-md-12"><p style="text-align:center; color:#F00;">***All * marked fields are required***</p></div>
            
				
              <!-- /.col -->
              <div class="col-md-4">
                <div class="form-group">
					<label for="employee_id">Employee Name&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label><?php echo $rowEditData["name_bn"];  ?>
				  </div>
                <!-- /.form-group -->
             
              </div>
				
			<!-- /.col -->
              <div class="col-md-4">
                <div class="form-group">
					<label for="employee_id">Designation&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label><?php echo $rowEditData["designation"];  ?>
				  </div>
                <!-- /.form-group -->
             
              </div>		
			<!-- /.col -->
              <div class="col-md-4">
                <div class="form-group">
					<label for="employee_id">Department&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label><?php echo $rowEditData["department"];  ?>
				  </div>
                <!-- /.form-group -->
             
              </div>	
				<style>
					.permission_checkbox{
					height: 20px;
						width: 20px;
					}
					.checkbox-inline{
						font-size:20px;
					}
				
				</style>
				
				
				<div class="col-md-4">
                <div class="form-group">
					<label for="store_id">Store Name:<span style="color:#F00;">*</span></label>
					 <select class="select2"  name="store_id" data-placeholder="Select Store" style="width: 100%;">
					<option value="">Select Store</option>
					<?php
				$InformationDepartment = $pdo->query("SELECT * FROM store_information WHERE deleted_at is NULL");
	              while($rowDataInformationDepartment= $InformationDepartment->fetch()){
						 ?>	 
                    <option <?php if($rowEditData["store_id"]==$rowDataInformationDepartment["id"]){ echo "selected"; } ?> value="<?php echo $rowDataInformationDepartment["id"]; ?>"><?php echo $rowDataInformationDepartment["name"]; ?></option>
					<?php } ?>	 
                  </select>
					
				  </div>
                <!-- /.form-group -->
             
              </div>
				
				<div class="col-md-6">
			  <div class="form-group">
			  <label for="nid">Permission:<span style="color:#F00;">*</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
				<div class="form-group">  
				  
				 <label class="checkbox-inline">
				  <input type="checkbox" <?php if ( !empty($rowEditData["role_permission"]) && stripos($rowEditData["role_permission"], "Create") !== false) { echo "checked"; } ?> class="permission_checkbox" name="name_create" value="Create">&nbsp;&nbsp;&nbsp;Create&nbsp;&nbsp;&nbsp;
				</label>
				<label class="checkbox-inline">
				  <input type="checkbox" <?php if (!empty($rowEditData["role_permission"]) && stripos($rowEditData["role_permission"], "Update") !== false) { echo "checked"; } ?> class="permission_checkbox" name="name_update" value="Update">&nbsp;&nbsp;&nbsp;&nbsp;Update&nbsp;&nbsp;&nbsp;
				</label>
				<label class="checkbox-inline">
				  <input type="checkbox" <?php if (!empty($rowEditData["role_permission"]) && stripos($rowEditData["role_permission"], "Delete") !== false) { echo "checked"; } ?> class="permission_checkbox" name="name_delete" value="Delete">&nbsp;&nbsp;&nbsp;Delete
				</label>
					
				<label class="checkbox-inline">
				  <input type="checkbox" <?php if (!empty($rowEditData["role_permission"]) && stripos($rowEditData["role_permission"], "View") !== false) { echo "checked"; } ?> class="permission_checkbox" name="name_View" value="View">&nbsp;&nbsp;&nbsp;View
				</label>
				<label class="checkbox-inline">
				  <input type="checkbox" <?php if (!empty($rowEditData["role_permission"]) && stripos($rowEditData["role_permission"], "Distribution") !== false) { echo "checked"; } ?> class="permission_checkbox" name="name_Distribution" value="Distribution">&nbsp;&nbsp;&nbsp;Distribution
				</label>	
					
					
				  </div>	
				  </div>
                <!-- /.form-group -->
              </div>
              <!-- /.col -->
              
			
				
				
		
				
              <!-- /.col -->
            </div>
            <!-- /.row -->
          </div>
          <!-- /.card-body -->
          <div class="card-footer">
          <div class="box-tools pull-right">
       <button type="button" class="btn btn-warning" onclick="history.back(-1)"><i class="fa fa-window-close"></i> Cancel</button>&nbsp;&nbsp;&nbsp;
                    <button class="btn btn-primary" type="submit" name="User_Application_Permission_Update"><i class="fa fa-save"></i> Save </button>
                </div>
          </div>
        </div>
        <!-- /.card -->
</form>
        
        <!-- /.card -->

        
        <!-- /.row -->
        
        <!-- /.row -->
        
        <!-- /.row -->
      </div>
      <!-- /.container-fluid -->
    </section>