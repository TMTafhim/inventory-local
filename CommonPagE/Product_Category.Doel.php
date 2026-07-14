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
			  <div class="box-tools pull-right">
                    
                    
                    <a href="?<?php echo $page_title; ?>_Create/<?php echo $MenuName; ?>" class="btn btn-warning">
                        <i class="fa fa-plus"></i>&nbsp; Create
                    </a>
                                       
                </div>
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
                          Number of Item
                      </th>
                      <th >
                          Quantity
                      </th>
                      <th >
                          Option
                      </th>
                      
                  </tr>
              </thead>
              <tbody>
				  
				 <?php
				  $OrganizationInfo = $pdo->query("SELECT product_category.*,COUNT(product_information.id) AS number_of_product FROM product_category LEFT JOIN product_information ON product_category.id=product_information.product_category WHERE product_category.deleted_at is NULL GROUP BY product_category.id");
				  $sl=1;
	              while($rowDataOrganizationInfo= $OrganizationInfo->fetch()){
					$db_table='product_category';  
				  ?> 
				  
                  <tr>
                      <td>
                          <?php echo $sl; ?>
                      </td>
                      <td>   <?php echo $rowDataOrganizationInfo["name"]; ?></td>
                      <td>   <?php echo $rowDataOrganizationInfo["code"]; ?></td>
                      <td> <a class="btn btn-success" href="?Category_Wise_Product_information/<?php echo $rowDataOrganizationInfo["name"]; ?>/<?php echo $rowDataOrganizationInfo["id"]; ?>"><?php echo $rowDataOrganizationInfo["number_of_product"]; ?></a>  </td>
                      <td></td>
                      <td class="project-actions text-right">
                       
                          <a class="btn btn-info btn-sm" href="?<?php echo $page_title; ?>_Edit/<?php echo $MenuName; ?>/<?php echo $rowDataOrganizationInfo["id"]; ?>/<?php echo $db_table; ?>">
                              <i class="fas fa-pencil-alt">
                              </i>
                              Edit
                          </a>
                          <a class="btn btn-danger btn-sm" href="?<?php echo $page_title; ?>/<?php echo $MenuName; ?>/<?php echo $rowDataOrganizationInfo["id"]; ?>/DELETE/<?php echo $db_table; ?>">
                              <i class="fas fa-trash">
                              </i>
                              Delete
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