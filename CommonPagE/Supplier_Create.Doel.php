
<section class="content">
      <div class="container-fluid">
        <!-- SELECT2 EXAMPLE -->
        
        <!-- /.card -->
  <form method="post" action="?<?php echo substr($page_title,0,-7); ?>/<?php echo $MenuName; ?>/supplier_information" enctype="multipart/form-data">
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
            <input class="form-control" placeholder="Organization" name="organization" id="organization" type="text" required>
        </div>
    </div>
    
    <div class="col-sm-4">
        <div class="form-group">
            <label for="name">Name<span style="color:#F00;">*</span></label>
            <input class="form-control" placeholder="Name" name="name" id="name" type="text" required>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="form-group">
            <label for="email">Email</label>
            <input class="form-control" placeholder="Email Address" name="email" id="email" type="text"  pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,3}$">
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group">
            <label for="mobile">Mobile No<span style="color:#F00;">*</span></label>
            <input class="form-control" placeholder="Mobile Number" pattern="[0-9]{11}" name="mobile" id="mobile" type="text" required>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group">
            <label for="address">Address</label>
            <input class="form-control" placeholder="Address Here"  name="address" id="address" type="text" >
        </div>
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