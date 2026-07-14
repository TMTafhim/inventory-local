<?php
$PageStatusCheck='stock_information';
$EditData = $pdo->query("SELECT product_information.name as product_name,product_information.unit as product_unit,product_information.code as product_code,stock_information.*,store_information.name AS store_name FROM `stock_information` INNER JOIN product_information ON stock_information.product_id=product_information.id INNER JOIN store_information ON stock_information.store_id=store_information.id  WHERE stock_information.id='$DocumentData'");
$rowEditData = $EditData->fetch();
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
          
              <!-- /.col -->
              <div class="col-md-4">
                <div class="form-group">
					<label for="name"> Store Name&nbsp;:&nbsp;</label><?php echo $rowEditData["product_name"]; ?>
					<input type="hidden" class="form-control" name="store_id" placeholder="Name" id="store_id" value="<?php echo $rowEditData["store_id"]; ?>" required>
				  </div>
                <!-- /.form-group -->
             
              </div>
		<div class="col-md-4">
                <div class="form-group">
					<label for="name"> Product Name&nbsp;:&nbsp;</label><?php echo $rowEditData["product_name"]; ?>
					<input type="hidden" class="form-control" name="product_id" placeholder="Name" id="product_id" value="<?php echo $rowEditData["product_id"]; ?>" required>
				  </div>
                <!-- /.form-group -->
             
              </div><div class="col-md-4">
                <div class="form-group">
					<label for="name"> Stock&nbsp;:&nbsp;<span style="color:#F00;">*</span></label>
					<input type="text" class="form-control" name="stock" placeholder="Stock" id="stock" value="<?php echo $rowEditData["stock"]; ?>" required>
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