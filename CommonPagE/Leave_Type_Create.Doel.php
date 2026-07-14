
<section class="content">
      <div class="container-fluid">
        <!-- SELECT2 EXAMPLE -->
        
        <!-- /.card -->
  <form method="post" action="?<?php echo substr($page_title,0,-7); ?>/<?php echo $MenuName; ?>/hr_leave_type" enctype="multipart/form-data">
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
              <div class="col-md-6">
                <div class="form-group">
					<label for="name">Name:<span style="color:#F00;">*</span></label>
					<input type="text" class="form-control" name="name" placeholder="Name" id="name" required>
				  </div>
                <!-- /.form-group -->
             
              </div>
              <div class="col-md-6">
                <div class="form-group">
					<label for="number_of_days">Number of Days:<span style="color:#F00;">*</span></label>
					<input type="text" class="form-control" name="number_of_days" placeholder="Number of Days" id="number_of_days" required>
				  </div>
                <!-- /.form-group -->
             
              </div>
              
              
              
		
				
              <!-- /.col -->
            </div>
            <!-- /.row -->
          </div>
          <!-- /.card-body -->
          <div class="card-footer">
          <div class="box-tools pull-right">
       <button type="button" class="btn btn-warning" onclick="history.back(-1)"><i class="fa fa-window-close"></i> Cancel</button>&nbsp;&nbsp;&nbsp;
                    <button class="btn btn-primary" type="submit" name="Insert_all"><i class="fa fa-save"></i> Save </button>
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