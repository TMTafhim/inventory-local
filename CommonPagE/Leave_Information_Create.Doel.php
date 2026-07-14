
<section class="content">
      <div class="container-fluid">
        <!-- SELECT2 EXAMPLE -->
  <script>

function HR_Patient_information(){
		
			var employee_id=document.getElementById("employee_id").value;
			var leave_type=document.getElementById("leave_type").value;
			var dataString = 'employee_id='+employee_id+'&leave_type='+leave_type;
			$.ajax
			({
			type: "POST",
			url: "ajax_HR_Information.php",
			data: dataString,
			cache: false,
			success: function(html)
			{
				$("#total_used_leave").html(html);
				$("#available_leave").html(html);
				
			} 
			});
			
}	

</script>       
        <!-- /.card -->
  <form method="post" action="?<?php echo substr($page_title,0,-7); ?>/Setting/expenditure" enctype="multipart/form-data">
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
      
		<div class="col-md-2">
                <div class="form-group">
					<label for="employee_id">Employee Name:<span style="color:#F00;">*</span></label>
					 <select class="select2"  name="employee_id" id="employee_id" data-placeholder="Select Employee" style="width: 100%;" required>
					<option value="">Select Employee</option>
					<?php
				  
				  $Information = $pdo->query("SELECT employee_information.*,hr_department.name AS department,hr_designation.name AS designation FROM employee_information inner join hr_designation ON employee_information.designation=hr_designation.id INNER JOIN hr_department ON employee_information.department=hr_department.id WHERE  employee_information.DELETED_AT is NULL");
				  $sl=1;
	              while($rowDataInformation= $Information->fetch()){
	            
				  ?>  
                    <option value="<?php echo $rowDataInformation["id"]; ?>"><?php echo $rowDataInformation["name_en"]." - ".$rowDataInformation["designation"]; ?></option>
					<?php }  ?>	 
                  </select>
					
				  </div>
                <!-- /.form-group -->
             
              </div>
              
              
		<div class="col-md-2">
                <div class="form-group">
					<label for="leave_type">Leave Type:<span style="color:#F00;">*</span></label>
					 <select class="select2" data-quick-create="leave_type" name="leave_type" id="leave_type" data-placeholder="Select Leave Type" onchange="HR_Patient_information()" style="width: 100%;" required>
					<option value="">Select Leave Type</option>
					<?php
				  
				  $InformationLeave_Type = $pdo->query("SELECT * FROM hr_leave_type WHERE deleted_at is NULL");
	              while($rowDataInformationLeave_Type= $InformationLeave_Type->fetch()){
	            
				  ?>  
                    <option ><?php echo $rowDataInformationLeave_Type["name"]; ?></option>
					<?php }  ?>	 
                  </select>
					
				  </div>
                <!-- /.form-group -->
             
              </div>
       <div class="col-sm-2">
        <div class="form-group">
            <label for="total_used_leave">Used Leave<span style="color:#F00;">*</span></label>
            <input class="form-control" placeholder="Used Leave Here ...." name="total_used_leave" id="total_used_leave"  type="text" required readonly>
        </div>
    </div>
              
   <div class="col-sm-2">
        <div class="form-group">
            <label for="available_leave">Available Leave<span style="color:#F00;">*</span></label>
            <input class="form-control" placeholder="Available Here ...." name="available_leave" id="available_leave"  type="text" required readonly>
        </div>
    </div>          
              
              
              
   <div class="col-sm-2">
        <div class="form-group">
            <label for="date">Start Date<span style="color:#F00;">*</span></label>
            <input class="form-control" placeholder="Start Date Here ...." name="start_date" id="from_date"  type="date" required >
        </div>
    </div>
    
 <div class="col-sm-2">
        <div class="form-group">
            <label for="date">End Date<span style="color:#F00;">*</span></label>
            <input class="form-control" placeholder="End Date Here ...." name="end_date" id="end_date"  type="date" required >
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
                    <button class="btn btn-primary" type="submit" name="Leave_information_Create"><i class="fa fa-save"></i> Save </button>
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
    
    <script>
         document.getElementById("from_date").valueAsDate = new Date();
</script>
<script>
         document.getElementById("end_date").valueAsDate = new Date();
</script>
