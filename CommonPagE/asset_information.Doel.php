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
                    
                     <?php if (stripos($role_permission, "Create") !== false) { ?> 
                    <a href="?<?php echo $page_title; ?>_Create/<?php echo $MenuName; ?>" class="btn btn-warning">
                        <i class="fa fa-plus"></i>&nbsp; Create
                    </a>
                  <?php } ?>                     
                </div>
          </div>
        </div>
        <div class="card-body p-0">
			
			<table id="example1" class="table table-bordered table-striped">
                      <thead>
                            <tr>
                                <th>
                                    SL
                                </th>
                                 <th>
                               Product Information
                                </th>
                               
								<th>
                                using Information
                                </th>
								<th>
                                 Description
                                </th>
								
								<th>
                                  Quantity
                                </th>
									
								<th>
                             <?php if (stripos($role_permission, "Update") !== false) { ?>
                                
                                    Option
                                
								<?php }else if (stripos($role_permission, "Delete") !== false) {  ?>
								
                                    Option
								<?php }else if (stripos($role_permission, "View") !== false) {  ?>
                                    Option
                                
								<?php } ?>
								</th>
                            </tr>
                            
                        </thead> 
                        <tbody aria-relevant="all" aria-live="polite" role="alert">
            <?php
						
			$informationpurchage = $pdo->query("SELECT asset_product_detail_history.*,asset_product_information.name AS product_name,asset_product_information.code AS product_code,asset_product_information.description AS product_description FROM `asset_product_detail_history` INNER JOIN asset_product_information ON asset_product_detail_history.product_id=asset_product_information.id WHERE  asset_product_detail_history.deleted_at is NULL order by asset_product_detail_history.id DESC limit 0,300");
		
			$i=1;
            while ($rowdatapurchage = $informationpurchage->fetch()){	
									
							
			$total=$i++;
			$db_table='asset_product_detail_history';	
				
						?>
                        
                        <tr>
                    <td><?php echo $total; ?></td>                    
                   
					<td><?php echo $rowdatapurchage["product_name"]; echo "<br>";echo $rowdatapurchage["product_code"]; echo "<br>";echo $rowdatapurchage["product_description"]; ?></td>
							
					<td><?php echo $rowdatapurchage["using_person_name"]; ?></td>				
				  	
					<td><?php echo $rowdatapurchage["description"]; ?></td>	
					<td><?php echo $rowdatapurchage["quantity"]; ?></td>	
											
					
                    <td text align="center">
						
						
						<?php if (stripos($role_permission, "Update") !== false) { ?>
					<a class="btn btn-info btn-sm" href="?<?php echo $page_title; ?>_Edit/<?php echo $MenuName; ?>/<?php echo $rowdatapurchage["id"]; ?>/<?php echo $db_table; ?>">
                              <i class="fas fa-pencil-alt">
                              </i>
                              Edit
                          </a>
						<?php } ?>
					<?php if (stripos($role_permission, "Delete") !== false) {  ?>	
						 <a class="btn btn-danger btn-sm" href="?<?php echo $page_title; ?>/<?php echo $MenuName; ?>/<?php echo $rowdatapurchage["id"]; ?>/DELETE_Asset_Management/<?php echo $db_table; ?>">
                              <i class="fas fa-trash">
                              </i>
                              Delete
                          </a>
						<?php } ?>
						
						
                    </td>
                    
                        </tr>
                        
                        
                        <?php } ?>
                        </tbody>
                        
                        
                         
                        
                    </table>
			
			
		
			
          
        </div>
        <!-- /.card-body -->
      </div>
      <!-- /.card -->

    </section>