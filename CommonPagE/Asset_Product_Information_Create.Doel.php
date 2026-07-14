
<section class="content">
      <div class="container-fluid">
        <!-- SELECT2 EXAMPLE -->
        
        <!-- /.card -->
  <form method="post" action="?<?php echo substr($page_title,0,-7); ?>/<?php echo $MenuName; ?>/asset_product_information" enctype="multipart/form-data">
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
              <div class="col-md-6">
				  <div class="form-group">
					<label for="name"> Name:<span style="color:#F00;">*</span></label>
					<input type="text" class="form-control" name="name" placeholder=" Name" id="name" required>
				  </div>
                <!-- /.form-group -->
              </div>
              
              <div class="col-md-3">
				  <div class="form-group">
					<label for="name"> Code:</label>
					<input type="text" class="form-control" name="code" placeholder="Code Here" id="code" >
				  </div>
                <!-- /.form-group -->
              </div>

              <!-- /.col -->
             <div class="col-md-3">
                <div class="form-group">
					<label for="unit">Unit:<span style="color:#F00;">*</span></label>
					 <select class="select2"  name="unit" data-placeholder="Select Unit" style="width: 100%;">
					<option value="">Select Unit</option>
					<?php
				$InformationDepartment = $pdo->query("SELECT * FROM product_unit WHERE deleted_at is NULL");
	              while($rowDataInformationDepartment= $InformationDepartment->fetch()){
						 ?>	 
                    <option  value="<?php echo $rowDataInformationDepartment["name"]; ?>"><?php echo $rowDataInformationDepartment["name"]; ?></option>
					<?php } ?>	 
                  </select>
					
				  </div>
                <!-- /.form-group -->
             
              </div>
              
              
              
            <div class="col-md-12">
                <div class="form-group">
					<label for="unit">Description:</label>
					<textarea  class="form-control" name="description" placeholder="Description Here" id="description"></textarea>
					
					
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