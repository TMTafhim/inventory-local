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
			$completed_distribution_due_filter="NOT EXISTS (SELECT 1 FROM requestion_detail rd WHERE rd.invoice_id=requestion_histiory.invoice_id AND rd.deleted_at IS NULL AND ((requestion_histiory.requistion_type='Fund' AND CAST(COALESCE(NULLIF(rd.due_amount,''),0) AS DECIMAL(18,4))>0) OR ((requestion_histiory.requistion_type IS NULL OR requestion_histiory.requistion_type<>'Fund') AND CAST(COALESCE(NULLIF(rd.due_quantity,''),0) AS DECIMAL(18,4))>0)))";
			if($_SESSION['USER_TYPE']=='Admin'){ 					
			$informationpurchage = $pdo->query("SELECT requestion_histiory.*,project_information.name AS project_name,store_information.name AS store_name FROM `requestion_histiory` INNER JOIN project_information ON requestion_histiory.project_id=project_information.id INNER JOIN store_information ON requestion_histiory.store_id=store_information.id where approval_status='Approve' and requestion_histiory.distribution_by is not NULL and requestion_histiory.distribution_status='Complete' and ".$completed_distribution_due_filter." and requestion_histiory.deleted_at is NULL");
			 }else{	
				
			$informationpurchage = $pdo->query("SELECT requestion_histiory.*,project_information.name AS project_name,store_information.name AS store_name FROM `requestion_histiory` INNER JOIN project_information ON requestion_histiory.project_id=project_information.id INNER JOIN store_information ON requestion_histiory.store_id=store_information.id where approval_status='Approve' and requestion_histiory.distribution_by is not NULL and requestion_histiory.distribution_status='Complete' and ".$completed_distribution_due_filter." and store_id='".$row_Login_Datauser_information["store_id"]."' and requestion_histiory.deleted_at is NULL");
			}
			$i=1;
            while ($rowdatapurchage = $informationpurchage->fetch()){	
									
							
			$total=$i++;
			$db_table='requestion_histiory';	
				
						?>
                        
                        <tr data-product-ids="<?php echo productUsageFilterIds($pdo, 'distribution', $rowdatapurchage["invoice_id"]); ?>">
                    <td><?php echo $total; ?></td>                    
                    <td><a href="?Distribution_History_Detail/<?php echo $rowdatapurchage["invoice_id"]; ?>"><?php echo $rowdatapurchage["invoice_id"]; ?></a></td>
					<td><?php echo date("d-m-Y", strtotime($rowdatapurchage["date"])); ?></td>
					<td><?php echo $rowdatapurchage["project_name"];  ?></td>
							
					<td><?php echo $rowdatapurchage["store_name"]; ?></td>				
				    <td><?php if(!empty($rowdatapurchage["distribution_status"]) && $rowdatapurchage["distribution_status"]=='Pending'){  if(empty($rowdatapurchage["project_coordinator"])){ echo "Coordinator Pending"; }else if(empty($rowdatapurchage["project_director"])){ echo "Project Director Pending"; }else if(empty($rowdatapurchage["managing_director"])){ echo "Managing Director Pending"; }else{ echo "Distribution Pending"; }     }else{  echo "Distribution Complete"; }    ?>	</td>	
										
											
				
                    <td text align="center">
						
						<?php if (stripos($role_permission, "View") !== false) {  ?>	
						 <a class="btn btn-primary btn-sm" href="?Distribution_History_Detail/<?php echo $rowdatapurchage["invoice_id"]; ?>">
                              <i class="fas fa-eye">
                              </i>
                              Summery
                          </a>
						<?php } ?>
						
					<?php if (stripos($role_permission, "Delete") !== false) {  ?>	
						 <a class="btn btn-success btn-sm" href="?Distribution_List_indivisual/<?php echo $rowdatapurchage["invoice_id"]; ?>">
                              <i class="fas fa-eye">
                              </i>
                              Detail
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
