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
			      
			       <?php /* if (!empty($role_permission) && stripos($role_permission, "Create") !== false) { ?> 
                    <a href="?Fund_Requistion_Create/<?php echo $MenuName; ?>" class="btn btn-primary">
                        <i class="fa fa-plus"></i>&nbsp;Fund Create
                    </a>
                  <?php } ?> 
                    
                     <?php if (!empty($role_permission) && stripos($role_permission, "Create") !== false) { ?> 
                    <a href="?<?php echo $page_title; ?>_Create/<?php echo $MenuName; ?>" class="btn btn-secondary">
                        <i class="fa fa-plus"></i>&nbsp;Material Create
                    </a>
                  <?php } */ ?>                     
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
                               Serial No
                                </th>
                               
								<th>
                                 Date
                                </th>
								<th>
                                 Project
                                </th>
								<th>
                                 Store
                                </th>
								
								<th>
                                  Status
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
			if($_SESSION['USER_TYPE']=='Admin'){ 					
			$informationpurchage = $pdo->query("SELECT requestion_histiory.*,project_information.name AS project_name,store_information.name AS store_name FROM `requestion_histiory` INNER JOIN project_information ON requestion_histiory.project_id=project_information.id INNER JOIN store_information ON requestion_histiory.store_id=store_information.id where requestion_histiory.`distribution_status`='Complete' and requestion_histiory.deleted_at is NULL order by id DESC");
			 }else{	
				
			$informationpurchage = $pdo->query("SELECT requestion_histiory.*,project_information.name AS project_name,store_information.name AS store_name FROM `requestion_histiory` INNER JOIN project_information ON requestion_histiory.project_id=project_information.id INNER JOIN store_information ON requestion_histiory.store_id=store_information.id where requestion_histiory.`distribution_status`='Complete' and requestion_histiory.employee_id='".$_SESSION['LoginReGiSterSession']."' and requestion_histiory.deleted_at is NULL order by id DESC");	
			}
			$i=1;
            while ($rowdatapurchage = $informationpurchage->fetch()){	
									
							
			$total=$i++;
			$db_table='requestion_histiory';	
				
						?>
                        
                        <tr data-product-ids="<?php echo productUsageFilterIds($pdo, 'requisition', $rowdatapurchage["invoice_id"]); ?>">
                    <td><?php echo $total; ?></td>                    
                    <td><a href="?Requestion_History_Detail/<?php echo $rowdatapurchage["invoice_id"]; ?>"><?php echo $rowdatapurchage["invoice_id"]; ?></a></td>
					<td><?php echo date("d-m-Y", strtotime($rowdatapurchage["date"])); ?></td>
					<td><?php echo $rowdatapurchage["project_name"];  ?></td>
							
					<td><?php echo $rowdatapurchage["store_name"]; ?></td>				
				    <td><?php if(!empty($rowdatapurchage["approval_status"]) && $rowdatapurchage["approval_status"]=='Pending'){
					$Staff_information = $pdo->query("SELECT employee_information.*  FROM project_material_aproval_status INNER JOIN employee_information ON project_material_aproval_status.employee_id=employee_information.id where  project_material_aproval_status.invoice_id='".$rowdatapurchage["invoice_id"]."' and project_material_aproval_status.project_id='".$rowdatapurchage["project_id"]."' and approval_status='Pending' and project_material_aproval_status.approval_id is NULL");
                    $rowdataStaff_information = $Staff_information->fetch();
					echo $rowdataStaff_information["name_en"]." Pending";		
							
							
					}else if(!empty($rowdatapurchage["distribution_status"]) && $rowdatapurchage["distribution_status"]=='Pending'){
					echo "Distribution Pending";		
					}else{  echo "Distribution Complete"; }    ?>	</td>	
										
											
				
                    <td text align="center">
						
						<?php if (stripos($role_permission, "View") !== false) {  ?>	
						 <a class="btn btn-primary btn-sm" href="?Requestion_History_Detail/<?php echo $rowdatapurchage["invoice_id"]; ?>">
                              <i class="fas fa-eye">
                              </i>
                              View
                          </a>
						<?php } ?>
						
					<?php if (stripos($role_permission, "Delete") !== false) {  ?>	
						 <a class="btn btn-danger btn-sm" href="?<?php echo $page_title; ?>/<?php echo $MenuName; ?>/<?php echo $rowdatapurchage["id"]; ?>/DELETE/<?php echo $db_table; ?>">
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
