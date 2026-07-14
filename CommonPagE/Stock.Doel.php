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
                          Code
                      </th>
                      <th >
                          Description
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
                          Return
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
				  
				  if($_SESSION['USER_TYPE']=='Admin'){ 
				  $OrganizationInfo = $pdo->query("SELECT stock_information.product_id,product_information.name as product_name,product_information.unit as product_unit,product_information.code as product_code,SUM(`previous`) AS previous,SUM(`new`) AS new,SUM(`return`) AS return_qty,  SUM(`total`) AS `total`,SUM(`distribution`) AS `distribution`, SUM(`stock`) AS `stock`,product_information.description AS description FROM `stock_information` INNER JOIN product_information ON stock_information.product_id=product_information.id where product_information.deleted_at is NULL GROUP by stock_information.product_id ");
				  }else{
				   $OrganizationInfo = $pdo->query("SELECT stock_information.product_id,product_information.name as product_name,product_information.unit as product_unit,product_information.code as product_code,SUM(`previous`) AS previous,SUM(`new`) AS new,SUM(`return`) AS return_qty,  SUM(`total`) AS `total`,SUM(`distribution`) AS `distribution`, SUM(`stock`) AS `stock`,product_information.description AS description FROM `stock_information` INNER JOIN product_information ON stock_information.product_id=product_information.id where stock_information.store_id='".$login_user_store_id."' and product_information.deleted_at is NULL GROUP by stock_information.product_id");	  
				  }
				  $sl=1;
	              while($rowDataOrganizationInfo= $OrganizationInfo->fetch()){
				  ?> 
				  
                  <tr data-product-ids="<?php echo $rowDataOrganizationInfo["product_id"]; ?>">
                      <td>
                          <?php echo $sl; ?>
                      </td>
                      <td>   <?php echo $rowDataOrganizationInfo["product_name"]; ?></td>
                      <td>   <?php echo $rowDataOrganizationInfo["product_code"]; ?></td>
                       <td>   <?php if(!empty($rowDataOrganizationInfo["description"])){ echo nl2br($rowDataOrganizationInfo["description"]); } ?></td>
                      
					  <td>   <?php echo $rowDataOrganizationInfo["product_unit"]; ?></td>
					  <td>   <?php echo $rowDataOrganizationInfo["previous"]; ?></td>
					  <td>   <?php echo $rowDataOrganizationInfo["new"]; ?></td>
					  <td>   <?php echo $rowDataOrganizationInfo["return_qty"]; ?></td>
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
