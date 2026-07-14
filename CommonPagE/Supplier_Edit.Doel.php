<?php
$EditData = $pdo->query("select * FROM $PageStatusCheck WHERE id='$DocumentData'");
$editrowdata = $EditData->fetch();
?>
<section class="content">
      <div class="container-fluid">
        <!-- SELECT2 EXAMPLE -->
        
        <!-- /.card -->
  <form method="post" action="?<?php echo substr($page_title,0,-5)."/".$MenuName."/".$DocumentData."/".$PageStatusCheck; ?>" enctype="multipart/form-data">
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
              
				
		<div class="col-md-12">
				
				<div class="row">
    
 <div class="col-sm-4">
        <div class="form-group">
            <label for="organization">Organization<span style="color:#F00;">*</span></label>
            <input class="form-control" placeholder="Organization" name="organization" value="<?php echo $editrowdata["organization"]; ?>" id="organization" type="text" required>
        </div>
    </div>
    
    <div class="col-sm-4">
        <div class="form-group">
            <label for="name">Name<span style="color:#F00;">*</span></label>
            <input class="form-control" placeholder="Name" name="name" value="<?php echo $editrowdata["name"]; ?>" id="name" type="text" required>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="form-group">
            <label for="email">Email</label>
            <input class="form-control" placeholder="Email Address" value="<?php echo $editrowdata["email"]; ?>" name="email" id="email" type="text"  pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,3}$">
        </div>
    </div>
    <div class="col-sm-4">
        <div class="form-group">
            <label for="mobile">Mobile No<span style="color:#F00;">*</span></label>
            <input class="form-control" placeholder="Mobile Number" value="<?php echo $editrowdata["mobile"]; ?>" pattern="[0-9]{11}" name="mobile" id="mobile" type="text" required>
        </div>
    </div>
    <div class="col-sm-8">
        <div class="form-group">
            <label for="address">Address</label>
            <input class="form-control" placeholder="Address Here" value="<?php echo $editrowdata["address"]; ?>"  name="address" id="address" type="text" >
        </div>
    </div>
    
    
    </div>
				
				
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