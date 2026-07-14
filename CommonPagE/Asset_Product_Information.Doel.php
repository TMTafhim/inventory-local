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
                          Description
                      </th>
					  <th >
                          Unit
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
				  $OrganizationInfo = $pdo->query("SELECT * FROM asset_product_information WHERE deleted_at is NULL");
				  $sl=1;
	              while($rowDataOrganizationInfo= $OrganizationInfo->fetch()){
					$db_table='asset_product_information';  
				  ?> 
				  
                  <tr>
                      <td>
                          <?php echo $sl; ?>
                      </td>
                      <td>   <?php echo $rowDataOrganizationInfo["name"]; ?></td>
                      <td>   <?php echo $rowDataOrganizationInfo["code"]; ?></td>
                      <td><?php  if(!empty($rowDataOrganizationInfo["description"])){ echo nl2br($rowDataOrganizationInfo["description"]); }  ?></td>
					  <td>   <?php echo $rowDataOrganizationInfo["unit"]; ?></td>
					  <td>   <?php echo $rowDataOrganizationInfo["stock"]; ?></td>
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