<section class="content">
      <div class="container-fluid">
        <!-- SELECT2 EXAMPLE -->
        
        <!-- /.card -->
  <form method="post" action="?<?php echo substr($page_title,0,-7); ?>/<?php echo $MenuName; ?>" enctype="multipart/form-data">
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
              <div class="col-md-3">
			  <div class="form-group">
			  <label for="office_id">Office ID:<span style="color:#F00;">*</span></label>
				<input type="text" class="form-control" name="office_id" placeholder="Office ID" id="office_id" required>
				  </div>
                <!-- /.form-group -->
              </div>
				<div class="col-md-3">
			  <div class="form-group">
			  <label for="nid">NID:<span style="color:#F00;">*</span></label>
				<input type="text" class="form-control" name="nid" placeholder="NID" id="nid" required>
				  </div>
                <!-- /.form-group -->
              </div>
              <!-- /.col -->
              <div class="col-md-3">
                <div class="form-group">
					<label for="name_bn">Name (Bangla):<span style="color:#F00;">*</span></label>
					<input type="text" class="form-control" name="name_bn" placeholder="Name (Bangla)" id="name_bn" required>
				  </div>
                <!-- /.form-group -->
              </div>
				
			<div class="col-md-3">
                <div class="form-group">
					<label for="name_en">Name (English):<span style="color:#F00;">*</span></label>
					<input type="text" class="form-control" name="name_en" placeholder="Name (English)" id="name_en" required>
				  </div>
                <!-- /.form-group -->
              </div>
				
			<div class="col-md-3">
                <div class="form-group">
					<label for="father_name">Father Name:<span style="color:#F00;">*</span></label>
					<input type="text" class="form-control" name="father_name" placeholder="Father Name" id="father_name" required>
				  </div>
                <!-- /.form-group -->
              </div>
			<div class="col-md-3">
                <div class="form-group">
					<label for="mother_name">Mother Name:<span style="color:#F00;">*</span></label>
					<input type="text" class="form-control" name="mother_name" placeholder="Mother Name" id="mother_name" required>
				  </div>
                <!-- /.form-group -->
              </div>	
				
				
				<div class="col-md-3">
                <div class="form-group">
					<label for="mobile">Mobile No:<span style="color:#F00;">*</span></label>
					<input type="text" class="form-control" name="mobile" placeholder="Mobile No" id="MOBILE" required  pattern="[0]+[1]+[0-9]{9}" title="Must contain at least 01xxxxxxxxx" onkeyup="if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,'')">
				  </div>
                <!-- /.form-group -->
             
              </div>	
				
				<div class="col-md-3">
                <div class="form-group">
					<label for="mobile_secondary">Mobile No(Secondary):</label>
					<input type="text" class="form-control" name="mobile_secondary" placeholder="Mobile No" id="mobile_secondary"   pattern="[0]+[1]+[0-9]{9}" title="Must contain at least 01xxxxxxxxx" onkeyup="if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,'')">
				  </div>
                <!-- /.form-group -->
             
              </div>	
				
				
			 <div class="col-md-3">
                <div class="form-group">
					<label for="email">Email Address:<span style="color:#F00;">*</span></label>
					<input type="email" class="form-control" name="email" placeholder="Email Address" id="EMAIL" required  pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$" title="Please Input your Correct Email Address">
				  </div>
                <!-- /.form-group -->
             
              </div>
				
			 <div class="col-md-3">
                <div class="form-group">
					<label for="date_of_joining">Date of Joining<span style="color:#F00;">*</span></label>
					<div class="input-group date" id="reservationdate1" data-target-input="nearest">
                        <input type="text"  class="form-control datetimepicker-input" id="RegistrationDate" name="date_of_joining" data-target="#reservationdate1" required placeholder="Date of Joining"/>
                        <div class="input-group-append" data-target="#reservationdate1" data-toggle="datetimepicker">
                            <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                        </div>
                    </div>
				  </div>
                <!-- /.form-group -->
             
              </div>	
				
			 <div class="col-md-3">
                <div class="form-group">
					<label for="department">Department:<span style="color:#F00;">*</span></label>
					 <select class="select2" data-quick-create="department" name="department" data-placeholder="Select Department" style="width: 100%;" required>
					<option value="">Select Department</option>
					<?php
				$InformationDepartment = $pdo->query("SELECT * FROM hr_department WHERE deleted_at is NULL");
	              while($rowDataInformationDepartment= $InformationDepartment->fetch()){
						 ?>	 
                    <option value="<?php echo $rowDataInformationDepartment["id"]; ?>"><?php echo $rowDataInformationDepartment["name"]; ?></option>
					<?php } ?>	 
                  </select>
					
				  </div>
                <!-- /.form-group -->
             
              </div>
				
				<div class="col-md-3">
                <div class="form-group">
					<label for="designation">Designation:<span style="color:#F00;">*</span></label>
					 <select class="select2" data-quick-create="designation" name="designation" data-placeholder="Select Designation" style="width: 100%;" required>
					<option value="">Select Designation</option>
					<?php
				$InformationDesignation = $pdo->query("SELECT * FROM hr_designation WHERE deleted_at is NULL");
	              while($rowDataDesignationInformation= $InformationDesignation->fetch()){
						 ?>	 
                    <option value="<?php echo $rowDataDesignationInformation["id"]; ?>"><?php echo $rowDataDesignationInformation["name"]; ?></option>
					<?php } ?>	 
                  </select>
					
				  </div>
                <!-- /.form-group -->
             
              </div>
				 <div class="col-md-3">
                <div class="form-group">
					<label for="P_NAME">Date of Birth<span style="color:#F00;">*</span></label>
					<div class="input-group date" id="reservationdate" data-target-input="nearest">
                        <input type="text"  class="form-control datetimepicker-input" id="RegistrationDate" name="date_of_birth" data-target="#reservationdate" required placeholder="Date of Birth"/>
                        <div class="input-group-append" data-target="#reservationdate" data-toggle="datetimepicker">
                            <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                        </div>
                    </div>
				  </div>
                <!-- /.form-group -->
             
              </div>
				
				<div class="col-md-3">
                <div class="form-group">
					<label for="blood_group">Blood Group<span style="color:#F00;">*</span></label>
					
				  <select name="blood_group" class="form-control select2" style="width: 100%;"  required>
                                <option selected="selected" value="">Select Blood Group</option>
                                 <option value="A+">A+</option>
                                 <option value="A-">A-</option>
                                 <option value="B+">B+</option>
                                 <option value="B-">B-</option>
                                 <option value="AB+">AB+</option>
                                 <option value="AB-">AB-</option>
                                 <option value="O+">O+</option>
                                 <option value="O-">O-</option>
                              </select>
				  </div>
                <!-- /.form-group -->
             
              </div>
				
			<div class="col-md-3">
                <div class="form-group">
					<label for="gender">Gender<span style="color:#F00;">*</span></label>
					 <select class="form-control select2bs4" name="gender" id="gender" style="width: 100%;" required>
					 
                    <option selected="selected" value="">Select Gender</option>
                  
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Others">Others</option>
                  </select>
				  </div>
                <!-- /.form-group -->
             
              </div>
			<div class="col-md-3">
                <div class="form-group">
					<label for="religion">Religion<span style="color:#F00;">*</span></label>
					     <select name="religion" class="form-control select2" style="width: 100%;" required>
                                <option selected="selected" value="">Select Religion</option>
                                 <option>মুসলিম</option>
                                 <option>হিন্দু </option>
                                 <option>বৌদ্ধ</option>
                                 <option>খ্রিস্টান</option>
                                 <option>অন্যান্য</option>

                                 
                              </select>
            </div>
					
				  </div> 
				
				<div class="col-md-3">
                <div class="form-group">
					<label for="marital_status">Maritial Status<span style="color:#F00;">*</span></label>
					     <select name="marital_status" class="form-control select2" style="width: 100%;" required>
                                <option selected="selected" value="">Select Maritial Status</option>
                                 <option>Single</option>
                                 <option>Married</option>
                                 <option>Widowed</option>
                                 <option>Divorced</option>
                                 <option>Separated </option>

                                 
                              </select>
            </div>
					
				  </div>
				
				<div class="col-md-3">
                <div class="form-group">
					<label for="last_education">Last Education:<span style="color:#F00;">*</span></label>
					<input type="text" class="form-control" name="last_education" placeholder="Last Education" id="last_education" required>
				  </div>
                <!-- /.form-group -->
              </div>
				<div class="col-md-3">
                <div class="form-group">
					<label for="day_off">Day Off:<span style="color:#F00;">*</span></label>
					 <select class="select2"  name="off_days" data-placeholder="Select Day Off" style="width: 100%;" required>
					<option value="">Select off Days</option>     
                    <option>Saturday</option>
                    <option>Sunday</option>
                    <option>Monday</option>
                    <option>Tuesday</option>
                    <option>Wednesday</option>
                    <option>Thursday</option>
                    <option>Friday</option>
                  </select>
					
				  </div>
                <!-- /.form-group -->
             
              </div>
				<div class="col-md-12">
                <div class="form-group">
					<label for="present_address">Present Address:<span style="color:#F00;">*</span></label>
					<textarea  class="form-control" name="present_address" placeholder="Present Address" id="present_address" required></textarea>
				  </div>
                <!-- /.form-group -->
              </div>
			<script>

function Division(){
		
			var division=document.getElementById("division").value;
			var dataString = 'division_name_post='+division;
			$.ajax
			({
			type: "POST",
			url: "ajax_areaInformation.php",
			data: dataString,
			cache: false,
			success: function(html)
			{
				$(".district").html(html);
				
			} 
			});
			
}	

function District(){
		
			var division=document.getElementById("division").value;
	var district=document.getElementById("district").value;
			var dataString = 'district_name_post='+district+'&division='+division;
			$.ajax
			({
			type: "POST",
			url: "ajax_areaInformation.php",
			data: dataString,
			cache: false,
			success: function(html)
			{
				$(".thana").html(html);
				
			} 
			});
			
}				
				
				
				</script>	
				<div class="col-md-12">
				<p style="font-size: 18px;font-weight: bold;">Permanent Address</p>
				</div>
			<div class="col-md-3">
                <div class="form-group">
					<label for="division">Division:<span style="color:#F00;">*</span></label>
					 <select class="select2" onChange="Division();"  name="division" data-placeholder="Select Division" id="division" style="width: 100%;">
					<option value="">Select Division</option>
					<?php
				$DivisionInfo = $pdo->query("SELECT distinct division AS division FROM area_information ");
	              while($rowDataDivisionInfo=$DivisionInfo->fetch()){
						 ?>	 
                    <option><?php echo $rowDataDivisionInfo["division"]; ?></option>
					<?php } ?>	 
                  </select>
					
				  </div>
                <!-- /.form-group -->
             
              </div>	
				<div class="col-md-3">
                <div class="form-group">
					<label for="district">District:<span style="color:#F00;">*</span></label>
					 <select class="select2 district" onChange="District();"  name="district" data-placeholder="Select District" id="district" style="width: 100%;">
					<option value="">Select District</option> 
                  </select>
					
				  </div>
                <!-- /.form-group -->
             
              </div>	
				<div class="col-md-3">
                <div class="form-group">
					<label for="thana">Thana:<span style="color:#F00;">*</span></label>
					 <select class="select2 thana"  name="thana" data-placeholder="Select Thana" id="thana" style="width: 100%;">
					<option value="">Select Thana</option> 
                  </select>
					
				  </div>
                <!-- /.form-group -->
             
              </div>
				
				<div class="col-md-3">
                <div class="form-group">
					<label for="post_office">Post Office:<span style="color:#F00;">*</span></label>
					<input type="text" class="form-control" name="post_office" placeholder="Post Office" id="post_office" required>
					
				  </div>
                <!-- /.form-group -->
             
              </div>
				<div class="col-md-12">
                <div class="form-group">
					<label for="village">Village/House/Road/area:<span style="color:#F00;">*</span></label>
					<input type="text" class="form-control" name="village" placeholder="Village/House/Road/area" id="village" required>
					
				  </div>
                <!-- /.form-group -->
             
              </div>
				<div class="col-md-12">
				<p style="font-size: 18px;font-weight: bold;">Emergency Contact</p>
				</div>
				
			<div class="col-md-6">
                <div class="form-group">
					<label for="emergency_name">Name:<span style="color:#F00;">*</span></label>
					<input type="text" class="form-control" name="emergency_name" placeholder="Name" id="emergency_name" required>
					
				  </div>
                <!-- /.form-group -->
             
              </div>
				<div class="col-md-6">
                <div class="form-group">
					<label for="emergency_mobile">Mobile No:<span style="color:#F00;">*</span></label>
					<input type="text" class="form-control" name="emergency_mobile" placeholder="Mobile No" id="emergency_mobile" required  pattern="[0]+[1]+[0-9]{9}" title="Must contain at least 01xxxxxxxxx" onkeyup="if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,'')">
				  </div>
                <!-- /.form-group -->
             
              </div>
				
				
				
				
				
				
				
				<div class="col-md-3">
				  <div class="form-group">
					<label for="PHOTO">Photo (Upload)</label>
					<input type="file" class="form-control" name="photo"  id="photo" id="img" onchange="validateImage()">
				  </div>
                <!-- /.form-group -->
              </div>
			<div class="col-md-3">
				  <div class="form-group">
					<label for="hr_cv">CV (Upload)<span style="color:#F00;">*</span></label>
					<input type="file" class="form-control" name="hr_cv"  id="hr_cv" id="img" onchange="validateImage()" required>
				  </div>
                <!-- /.form-group -->
              </div>
				
				
			<div class="col-md-3">
                <div class="form-group">
				<label for="project_name">Project Name:</label>
				 <select class="select2" data-quick-create="project" name="project_name" data-placeholder="Select Project" style="width: 100%;">
					<option value="">Select Project</option>
					<?php
				$InformationProject = $pdo->query("SELECT * FROM project_information WHERE deleted_at is NULL");
	              while($rowDataInformationProject= $InformationProject->fetch()){
						 ?>	 
                    <option value="<?php echo $rowDataInformationProject["id"]; ?>"><?php echo $rowDataInformationProject["name"]; ?></option>
					<?php } ?>	 
                  </select>
					
				  </div>
                <!-- /.form-group -->
             
              </div>
				
				
				
	
			  </div>
          </div>
          <!-- /.card-body -->
          <div class="card-footer">
          <div class="box-tools pull-right">
       <button type="button" class="btn btn-warning" onclick="history.back(-1)"><i class="fa fa-window-close"></i> Cancel</button>&nbsp;&nbsp;&nbsp;
                    <button class="btn btn-primary" type="submit" name="<?php echo $page_title; ?>"><i class="fa fa-save"></i> Save </button>
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
