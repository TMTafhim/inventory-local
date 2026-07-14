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
                                 Project Name
                                </th>
								<th>
                                 Store
                                </th>
								<th>
                                 Receiver
                                </th>
								<th>
                                 Distributed By
                                </th>
								<th>
                                 Receive Status
                                </th>
								
								
								<th>
                             
                                    Option
                               
								</th>
                            </tr>
                            
                        </thead> 
                        <tbody aria-relevant="all" aria-live="polite" role="alert">
            <?php
			$pendingReceiveSql="SELECT distribution_summary.*,project_information.name AS project_name,store_information.name AS store_name,receiver.name_en AS receiver_name,distributor.name_en AS distributor_name FROM `distribution_summary` INNER JOIN project_information ON distribution_summary.project_id=project_information.id INNER JOIN store_information ON distribution_summary.store_id=store_information.id LEFT JOIN employee_information receiver ON receiver.id=distribution_summary.assign_receiver_id LEFT JOIN employee_information distributor ON distributor.id=distribution_summary.created_by WHERE distribution_summary.deleted_at is NULL and (distribution_summary.received_status IS NULL OR distribution_summary.received_status='Pending' OR distribution_summary.received_status='Partial')";
			if($_SESSION['USER_TYPE']=='Admin'){
			$informationpurchage = $pdo->query($pendingReceiveSql." order by distribution_summary.id DESC limit 0,500");
			 }else{
			$informationpurchage = $pdo->prepare($pendingReceiveSql." and distribution_summary.assign_receiver_id=:receiver_id order by distribution_summary.id DESC limit 0,300");
			$informationpurchage->execute(array(':receiver_id'=>$_SESSION['LoginReGiSterSession']));
			}
			$i=1;
            while ($rowdatapurchage = $informationpurchage->fetch()){	
									
							
			$total=$i++;
			$db_table='distribution_summary';	
				
						?>
                        
                        <tr data-product-ids="<?php echo productUsageFilterIds($pdo, 'distribution_id', $rowdatapurchage["distribution_id"]); ?>">
                    <td><?php echo $total; ?></td>                    
                    <td><a href="?Distribution_History_Indivisual/<?php echo $rowdatapurchage["invoice_id"]; ?>/<?php echo $rowdatapurchage["distribution_id"]; ?>"><?php echo $rowdatapurchage["distribution_id"]; ?></a></td>
					<td><?php echo date("d-m-Y", strtotime($rowdatapurchage["date"])); ?></td>
					<td><?php echo htmlspecialchars($rowdatapurchage["project_name"]);  ?></td>
							
					<td><?php echo htmlspecialchars($rowdatapurchage["store_name"]); ?></td>
					<td><?php echo !empty($rowdatapurchage["receiver_name"]) ? htmlspecialchars($rowdatapurchage["receiver_name"]) : '<span class="text-danger">Receiver not assigned</span>'; ?></td>
					<td><?php echo !empty($rowdatapurchage["distributor_name"]) ? htmlspecialchars($rowdatapurchage["distributor_name"]) : 'Unknown'; ?></td>
					<td><span class="badge badge-warning"><?php echo !empty($rowdatapurchage["received_status"]) ? htmlspecialchars($rowdatapurchage["received_status"]) : 'Pending'; ?></span></td>
										
											
				
                    <td text align="center">
							
						 <a class="btn btn-primary btn-sm" href="?Distribution_History_Indivisual/<?php echo $rowdatapurchage["invoice_id"]; ?>/<?php echo $rowdatapurchage["distribution_id"]; ?>">
                              <i class="fas fa-eye">
                              </i>
                              View
                          </a>
						
						<a class="btn btn-primary btn-sm" href="?Material_Received_Status_Create/<?php echo $rowdatapurchage["invoice_id"]; ?>/<?php echo $rowdatapurchage["distribution_id"]; ?>">
                              <i class="fas fa-eye">
                              </i>
                              Receive
                          </a>
						
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
