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
			<form method="post" action="#" enctype="multipart/form-data" class="d-print-none">
  <div class="row">
      <div class="col-md-3"> </div>
    <div class="col-sm-2">
    <label for="service_fee">Date </label>
    <input type="date" class="form-control date"  autocomplete="off"  name="from_date" size="20" id="from_date" value="<?php if(!empty($_POST["from_date"])){ echo $_POST["from_date"];}else{ echo date("Y-m-d");} ?>" placeholder="Date (Start)" />
    </div>
   <div class="col-md-3">

                    <div class="form-group">
                      <label for="store_id">Store Name:<span style="color:#F00;">*</span></label>

                      <select name="store_id"  id="store_id"  class="form-control select2" style="width: 100%;" onchange="this.form.submit()">

                        <option selected="selected" value="">Select Store Name</option>
                      
                      <?php
                      $StoreInfo = $pdo->query("SELECT * FROM store_information WHERE DELETED_AT is NULL");
                      $sl=1;
                            while($rowDataStoreInfo= $StoreInfo->fetch()){
                      ?>  
                                <option <?php if(!empty($_POST["store_id"]) && $_POST["store_id"]==$rowDataStoreInfo["id"]){ echo "selected";} ?> value="<?php echo $rowDataStoreInfo["id"]; ?>"><?php echo $rowDataStoreInfo["name"]; ?></option>
                                <?php } ?>
                                
                              </select>
                      
                      </div>

                <!-- /.form-group -->
            </div> 

    
  </div>
      </form> 
          <table id="example1" class="table table-bordered table-striped" >
              <thead>
                  <tr>
                      <th >
                          SL
                      </th>
                      <th >
                          Name
                      </th>
					  <th >
                          Unit
                      </th>
					  <th >
                          Previous
                      </th>
					  <th >
                          New
                      </th>
					  
					  <th >
                          Total
                      </th>
					  <th >
                          Distribution
                      </th>
					  
					  <th >
                          Stock
                      </th>
					  
                      
                  </tr>
              </thead>
              <tbody>
				  
				 <?php
				  if(isset($_POST["store_id"])){
				      
				    if($_POST["from_date"]==$current_date) {
				    $OrganizationInfo = $pdo->query("SELECT product_information.name as product_name,product_information.unit as product_unit,SUM(`previous`) AS previous,SUM(`new`) AS new,SUM(`return`) AS return_qty,  SUM(`total`) AS `total`,SUM(`distribution`) AS `distribution`, SUM(`stock`) AS `stock` FROM `stock_information` INNER JOIN product_information ON stock_information.product_id=product_information.id where stock_information.store_id='".$_POST["store_id"]."' GROUP by stock_information.product_id ");   
				    } else{
				   $OrganizationInfo = $pdo->query("SELECT product_information.name as product_name,product_information.unit as product_unit,SUM(`previous`) AS previous,SUM(`new`) AS new, SUM(`total`) AS `total`,SUM(`distribution`) AS `distribution`, SUM(`stock`) AS `stock` FROM `stock_information_detail` INNER JOIN product_information ON stock_information_detail.product_id=product_information.id where stock_information_detail.store_id='".$_POST["store_id"]."' and date='".$_POST["from_date"]."'   GROUP by stock_information_detail.product_id ");       
				    }
				 
					  
				  }else{
				  if($_SESSION['USER_TYPE']=='Admin'){ 
				  $OrganizationInfo = $pdo->query("SELECT product_information.name as product_name,product_information.unit as product_unit,SUM(`previous`) AS previous,SUM(`new`) AS new, SUM(`total`) AS `total`,SUM(`distribution`) AS `distribution`, SUM(`stock`) AS `stock` FROM `stock_information` INNER JOIN product_information ON stock_information.product_id=product_information.id GROUP by stock_information.product_id");
				  }else{
				   $OrganizationInfo = $pdo->query("SELECT product_information.name as product_name,product_information.unit as product_unit,SUM(`previous`) AS previous,SUM(`new`) AS new, SUM(`total`) AS `total`,SUM(`distribution`) AS `distribution`, SUM(`stock`) AS `stock` FROM `stock_information` INNER JOIN product_information ON stock_information.product_id=product_information.id GROUP by stock_information.product_id where stock_information.store_id='".$login_user_store_id."'");	  
				  }
				  }
				  $sl=1;
	              while($rowDataOrganizationInfo= $OrganizationInfo->fetch()){
				  ?> 
				  
                  <tr>
                      <td>
                          <?php echo $sl; ?>
                      </td>
                      <td>   <?php echo $rowDataOrganizationInfo["product_name"]; ?></td>
					  <td>   <?php echo $rowDataOrganizationInfo["product_unit"]; ?></td>
					  <td>   <?php echo $rowDataOrganizationInfo["previous"]; ?></td>
					  <td>   <?php echo $rowDataOrganizationInfo["new"]; ?></td>
					  <td>   <?php echo $rowDataOrganizationInfo["total"]; ?></td>
					  <td>   <?php echo $rowDataOrganizationInfo["distribution"]; ?></td>
					  <td>   <?php echo $rowDataOrganizationInfo["stock"]; ?></td>
                      
                  </tr>
				  
				<?php
				 $sl++; 
				  } ?>  
				  
				  
                 
              </tbody>
          </table>
        </div>
        <!-- /.card-body -->
      </div>
      <!-- /.card -->

    </section>