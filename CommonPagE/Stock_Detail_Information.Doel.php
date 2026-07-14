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
                          Store Name
                      </th>
                       <th >
                          Code
                      </th>
                      <th >
                          Location
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
					  <th >
                          Option
                      </th>
					  
                      
                  </tr>
              </thead>
              <tbody>
				  
				 <?php
				 
				  $OrganizationInfo = $pdo->query("SELECT product_information.name as product_name,product_information.unit as product_unit,product_information.description as product_description,product_information.code as product_code,stock_information.*,store_information.name AS store_name FROM `stock_information` INNER JOIN product_information ON stock_information.product_id=product_information.id INNER JOIN store_information ON stock_information.store_id=store_information.id");
				
				  $sl=1;
	              while($rowDataOrganizationInfo= $OrganizationInfo->fetch()){
				  ?> 
				  
                  <tr>
                      <td>
                          <?php echo $sl; ?>
                      </td>
                      <td>   <?php echo $rowDataOrganizationInfo["product_name"]; ?></td>
                      <td>   <?php echo $rowDataOrganizationInfo["store_name"]; ?></td>
                      <td>   <?php echo $rowDataOrganizationInfo["product_code"]; ?></td>
                       <td>   <?php echo $rowDataOrganizationInfo["product_description"]; ?></td>
					  <td>   <?php echo $rowDataOrganizationInfo["product_unit"]; ?></td>
					  <td>   <?php echo $rowDataOrganizationInfo["previous"]; ?></td>
					  <td>   <?php echo $rowDataOrganizationInfo["new"]; ?></td>
					  <td>   <?php echo $rowDataOrganizationInfo["return"]; ?></td>
					  <td>   <?php echo $rowDataOrganizationInfo["total"]; ?></td>
					  <td>   <?php echo $rowDataOrganizationInfo["distribution"]; ?></td>
					  <td>   <?php echo $rowDataOrganizationInfo["stock"]; ?></td>
					  <td class="project-actions text-right">
                       
                          <a class="btn btn-info btn-sm" href="?<?php echo $page_title; ?>_Edit/<?php echo $MenuName; ?>/<?php echo $rowDataOrganizationInfo["id"]; ?>">
                              <i class="fas fa-pencil-alt">
                              </i>
                              Edit
                          </a>
                        
                      </td>
                      
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