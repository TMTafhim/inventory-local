<?php
$EditData = $pdo->query("select * FROM user_login WHERE id='$DocumentData'");
$rowEditData = $EditData->fetch();
?>
<section class="content">
      <div class="container-fluid">
        <!-- SELECT2 EXAMPLE -->
        
        <!-- /.card -->
  <form method="post" action="?<?php echo substr($page_title,0,-5)."/".$MenuName."/".$DocumentData."/user_login"; ?>" enctype="multipart/form-data">
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
					 <input type="hidden" class="form-control" name="USER_TYPE" placeholder="Name" id="USER_TYPE" required value="Doctor">
					<label for="name">Name:<span style="color:#F00;">*</span></label>
					<input type="text" class="form-control" name="name" value="<?php echo $rowEditData["name"]; ?>" placeholder="Name" id="name" required>
				  </div>
                <!-- /.form-group -->
              </div>
              <!-- /.col -->
              <div class="col-md-3">
                <div class="form-group">
					<label for="designation">Designation:<span style="color:#F00;">*</span></label>
					<input type="text" class="form-control" name="designation" value="<?php echo $rowEditData["designation"]; ?>" placeholder="Designation" id="designation" required>
				  </div>
                <!-- /.form-group -->
              </div>
				 <div class="col-md-3">
                <div class="form-group">
					<label for="department">Department:<span style="color:#F00;">*</span></label>
					<input type="text" class="form-control" name="department" value="<?php echo $rowEditData["department"]; ?>" placeholder="Department" id="department" required>
				  </div>
                <!-- /.form-group -->
              </div>
				
			 <div class="col-md-3">
                <div class="form-group">
					<label for="email">Email Address:<span style="color:#F00;">*</span></label>
					<input type="email" class="form-control" name="email" value="<?php echo $rowEditData["email"]; ?>" placeholder="Email Address" id="EMAIL" required  pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$" title="Please Input your Correct Email Address">
				  </div>
                <!-- /.form-group -->
             
              </div>	
			 <div class="col-md-3">
                <div class="form-group">
					<label for="mobile">Mobile No:<span style="color:#F00;">*</span></label>
					<input type="text" class="form-control" name="mobile" value="<?php echo $rowEditData["mobile"]; ?>" placeholder="Mobile No" id="MOBILE" required  pattern="[0]+[1]+[0-9]{9}" title="Must contain at least 01xxxxxxxxx" onkeyup="if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,'')">
				  </div>
                <!-- /.form-group -->
             
              </div>	
				<div class="col-sm-3">
               <div class="form-group">
				<label for="user_type">Role<span style="color:#F00;">*</span></label>

				<select name="user_type" id="user_type" required class="form-control select2" style="width:100%;">
				<option selected><?php echo $rowEditData["user_type"]; ?></option>
				<option>Admin</option>
				<option>Operator</option>
				<option>Account</option>
			    <option>HR</option>		
				</select>
               </div>
            </div>
				
				
				<div class="col-md-3">
				  <div class="form-group">
					<label for="PHOTO">Photo (Upload)</label>
					<input type="file" class="form-control" name="photo" value="<?php echo $rowEditData["name"]; ?>"  id="photo" id="img" onchange="validateImage()">
				  </div>
                <!-- /.form-group -->
              </div>
				
			
				
				
			
				  
				  
				  
			  </div>
          </div>
          <!-- /.card-body -->
          <div class="card-footer">
          <div class="box-tools pull-right">
       <button type="button" class="btn btn-warning" onclick="history.back(-1)"><i class="fa fa-window-close"></i> Cancel</button>&nbsp;&nbsp;&nbsp;
                    <button class="btn btn-primary" type="submit" name="Edit_all_Doc"><i class="fa fa-save"></i> Save </button>
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