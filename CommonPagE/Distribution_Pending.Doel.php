<style>
.distribution-partial-stamp{
	display:inline-flex;
	align-items:center;
	gap:6px;
	border:2px solid #d39e00;
	color:#8a6100;
	background:#fff8df;
	font-weight:700;
	letter-spacing:.04em;
	text-transform:uppercase;
	border-radius:4px;
	padding:4px 8px;
	line-height:1;
}
.distribution-status-note{
	display:block;
	margin-top:6px;
	color:#6c757d;
	font-size:12px;
	font-weight:600;
}
</style>

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
			$pending_distribution_due_filter="EXISTS (SELECT 1 FROM requestion_detail rd WHERE rd.invoice_id=requestion_histiory.invoice_id AND rd.deleted_at IS NULL AND ((requestion_histiory.requistion_type='Fund' AND CAST(COALESCE(NULLIF(rd.due_amount,''),0) AS DECIMAL(18,4))>0) OR ((requestion_histiory.requistion_type IS NULL OR requestion_histiory.requistion_type<>'Fund') AND CAST(COALESCE(NULLIF(rd.due_quantity,''),0) AS DECIMAL(18,4))>0)))";
			if($_SESSION['USER_TYPE']=='Admin'){ 					
			$informationpurchage = $pdo->query("SELECT requestion_histiory.*,project_information.name AS project_name,store_information.name AS store_name FROM `requestion_histiory` INNER JOIN project_information ON requestion_histiory.project_id=project_information.id INNER JOIN store_information ON requestion_histiory.store_id=store_information.id where approval_status='Approve' and (requestion_histiory.`distribution_status`='Pending' or ".$pending_distribution_due_filter.") and requestion_histiory.deleted_at is NULL");
			 }else{	
				
			$informationpurchage = $pdo->query("SELECT requestion_histiory.*,project_information.name AS project_name,store_information.name AS store_name FROM `requestion_histiory` INNER JOIN project_information ON requestion_histiory.project_id=project_information.id INNER JOIN store_information ON requestion_histiory.store_id=store_information.id where approval_status='Approve' and (requestion_histiory.`distribution_status`='Pending' or ".$pending_distribution_due_filter.") and store_id='".$row_Login_Datauser_information["store_id"]."' and  requestion_histiory.deleted_at is NULL");
			}
			$i=1;
            while ($rowdatapurchage = $informationpurchage->fetch()){	
									
							
			$total=$i++;
			$db_table='requestion_histiory';	
			$distribution_due_column=(!empty($rowdatapurchage["requistion_type"]) && $rowdatapurchage["requistion_type"]=='Fund') ? 'due_amount' : 'due_quantity';
			$remainingDistributionDue=(float)$pdo->query("SELECT COALESCE(SUM(GREATEST(CAST(COALESCE(NULLIF(".$distribution_due_column.",''),0) AS DECIMAL(18,4)),0)),0) FROM requestion_detail WHERE invoice_id='".$rowdatapurchage["invoice_id"]."' AND deleted_at IS NULL")->fetchColumn();
			$isPartialDistribution=$remainingDistributionDue>0 && !empty($rowdatapurchage["distribution_by"]);
				
						?>
                        
                        <tr data-product-ids="<?php echo productUsageFilterIds($pdo, 'requisition', $rowdatapurchage["invoice_id"]); ?>">
                    <td><?php echo $total; ?></td>                    
                    <td><a href="?Distribution_History_Detail/<?php echo $rowdatapurchage["invoice_id"]; ?>"><?php echo $rowdatapurchage["invoice_id"]; ?></a></td>
					<td><?php echo date("d-m-Y", strtotime($rowdatapurchage["date"])); ?></td>
					<td><?php echo $rowdatapurchage["project_name"];  ?></td>
							
					<td><?php echo $rowdatapurchage["store_name"]; ?></td>				
				    <td><?php if($isPartialDistribution){ ?><span class="distribution-partial-stamp"><i class="fas fa-stamp"></i> Partial</span><span class="distribution-status-note">Distribution pending: <?php echo rtrim(rtrim(number_format($remainingDistributionDue,4,'.',''), '0'), '.'); ?></span><?php }else if(!empty($rowdatapurchage["distribution_status"]) && $rowdatapurchage["distribution_status"]=='Pending'){  if(empty($rowdatapurchage["project_coordinator"])){ echo "Coordinator Pending"; }else if(empty($rowdatapurchage["project_director"])){ echo "Project Director Pending"; }else if(empty($rowdatapurchage["managing_director"])){ echo "Managing Director Pending"; }else{ echo "Distribution Pending"; }     }else{  echo "Distribution Complete"; }    ?>	</td>	
										
											
				
                    <td text align="center">
						
						<?php if (stripos($role_permission, "View") !== false) {  ?>	
						 <a class="btn btn-primary btn-sm" href="?Distribution_History_Detail/<?php echo $rowdatapurchage["invoice_id"]; ?>">
                              <i class="fas fa-eye">
                              </i>
                              View
                          </a>
						<?php } ?>
						
					<?php if (stripos($role_permission, "Distribution") !== false) {  ?>	
						 <a class="btn btn-success btn-sm" href="?Distribution_History_Create/<?php echo $rowdatapurchage["invoice_id"]; ?>">
                              <i class="fas fa-edit">
                              </i>
                              Distribution
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
