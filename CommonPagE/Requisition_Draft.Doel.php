<section class="content">

      <!-- Default box -->
      <div class="card">
        <div class="card-header">
          <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
              <i class="fas fa-minus"></i>
            </button>
            <button type="button" class="btn btn-tool" data-card-widget="remove" title="Remove">
              <i class="fas fa-times"></i>
            </button>
			  <div class="box-tools pull-right">
			      
			       <?php if (!empty($role_permission) && stripos($role_permission, "Create") !== false) { ?> 
                    <a href="?Fund_<?php echo $page_title; ?>_Create/<?php echo $MenuName; ?>" class="btn btn-primary">
                        <i class="fa fa-plus"></i>&nbsp;Fund Create
                    </a>
                  <?php } ?> 
                    
                     <?php if (!empty($role_permission) && stripos($role_permission, "Create") !== false) { ?> 
                    <a href="?<?php echo $page_title; ?>_Create/<?php echo $MenuName; ?>" class="btn btn-secondary">
                        <i class="fa fa-plus"></i>&nbsp;Material Create
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
								
								<th>Attachment</th>	
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
			$informationpurchage = $pdo->query("SELECT requestion_draft_histiory.*,project_information.name AS project_name,store_information.name AS store_name FROM `requestion_draft_histiory` INNER JOIN project_information ON requestion_draft_histiory.project_id=project_information.id INNER JOIN store_information ON requestion_draft_histiory.store_id=store_information.id where requestion_draft_histiory.final_submit_status is NULL and requestion_draft_histiory.deleted_at is NULL");
			 }else{	
				
			$informationpurchage = $pdo->query("SELECT requestion_draft_histiory.*,project_information.name AS project_name,store_information.name AS store_name FROM `requestion_draft_histiory` INNER JOIN project_information ON requestion_draft_histiory.project_id=project_information.id INNER JOIN store_information ON requestion_draft_histiory.store_id=store_information.id where requestion_draft_histiory.final_submit_status is NULL and requestion_draft_histiory.employee_id='".$_SESSION['LoginReGiSterSession']."' and requestion_draft_histiory.deleted_at is NULL");	
			}
			$i=1;
            while ($rowdatapurchage = $informationpurchage->fetch()){	
									
							
			$total=$i++;
			$db_table='requestion_draft_histiory';	
				
						?>
                        
                        <tr data-product-ids="<?php echo productUsageFilterIds($pdo, 'requisition_draft', $rowdatapurchage["invoice_id"]); ?>">
                    <td><?php echo $total; ?></td>                    
                    <td><a href="?Requestion_Draft_History_Detail/<?php echo $rowdatapurchage["invoice_id"]; ?>"><?php echo $rowdatapurchage["invoice_id"]; ?></a></td>
					<td><?php echo date("d-m-Y", strtotime($rowdatapurchage["date"])); ?></td>
					<td><?php echo $rowdatapurchage["project_name"];  ?></td>
							
					<td><?php echo $rowdatapurchage["store_name"]; ?></td>				
				    <td><a class="btn btn-secondary btn-sm" href="?Requisition_Approval_Path_Send/Requisition/<?php echo $rowdatapurchage["id"]; ?>">
                              <i class="fas fa-edit">
                              </i>
                             Send to Approval Path
                          </a>	</td>	
										
						<td>
					    
						<?php
									if(!empty($rowdatapurchage["multiple_photo"])){
									$multiple_photo_data=json_decode($rowdatapurchage["multiple_photo"], true);
									foreach ($multiple_photo_data as $photo_name) {
									?>
									
									<a class="btn btn-success" href="RequistionAttachment/<?php echo $photo_name["name"]; ?> " >Download</a> &nbsp;&nbsp;&nbsp;
							<?php } } ?>			
					    
					</td>						
				
                    <td text align="center">
						
						<?php if (stripos($role_permission, "View") !== false) {  ?>	
						 <a class="btn btn-primary btn-sm" href="?Requestion_Draft_History_Detail/<?php echo $rowdatapurchage["invoice_id"]; ?>">
                              <i class="fas fa-eye">
                              </i>
                              View
                          </a>
						<?php } ?>
						<?php if(!empty($rowdatapurchage["requistion_type"]) && $rowdatapurchage["requistion_type"]=='Fund'){ ?>
						 <a class="btn btn-secondary btn-sm" href="?Fund_Requestion_Draft_History_Edit/Requisition/<?php echo $rowdatapurchage["id"]; ?>">
                              <i class="fas fa-edit">
                              </i>
                              Edit
                          </a>
						<?php }else{ ?>
						<a class="btn btn-secondary btn-sm" href="?Material_Requestion_Draft_History_Edit/Requisition/<?php echo $rowdatapurchage["id"]; ?>">
                              <i class="fas fa-edit">
                              </i>
                              Edit
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
