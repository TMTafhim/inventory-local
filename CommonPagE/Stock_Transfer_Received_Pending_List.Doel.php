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
                                 From Store
                                </th>
								<th>
                                To Store
                                </th>
								<th>Note</th>
								<th>Photo</th>
									
								<th>
                             
								
                                    Option
								
								</th>
                            </tr>
                            
                        </thead> 
                        <tbody aria-relevant="all" aria-live="polite" role="alert">
            <?php
			if($_SESSION['USER_TYPE']=='Admin'){ 					
			$informationpurchage = $pdo->query("SELECT stock_transfer_summary.*,store_information.name AS from_store_name FROM `stock_transfer_summary` INNER JOIN store_information ON stock_transfer_summary.from_store_id=store_information.id where  stock_transfer_summary.deleted_at is NULL and received_status='Pending' order by stock_transfer_summary.id DESC limit 0,300");
			 }else{	
				
			$informationpurchage = $pdo->query("
			SELECT stock_transfer_summary.*,store_information.name AS from_store_name FROM `stock_transfer_summary` INNER JOIN store_information ON stock_transfer_summary.from_store_id=store_information.id where  stock_transfer_summary.created_by='".$_SESSION['LoginReGiSterSession']."' and  stock_transfer_summary.deleted_at is NULL and received_status='Pending' order by stock_transfer_summary.id DESC limit 0,300");	
			}
			$i=1;
            while ($rowdatapurchage = $informationpurchage->fetch()){	
				
				
			 $OrganizationInfo = $pdo->query("SELECT * FROM store_information WHERE id='".$rowdatapurchage["to_store_id"]."'");
			$rowDataOrganizationInfo= $OrganizationInfo->fetch();						
							
			$total=$i++;
			$db_table='requestion_histiory';	
				
						?>
                        
                        <tr data-product-ids="<?php echo productUsageFilterIds($pdo, 'stock_transfer', $rowdatapurchage["transfer_id"]); ?>">
                    <td><?php echo $total; ?></td>                    
                    <td><a href="?Stock_Transfer_detail_vew/Stock Transfer/<?php echo $rowdatapurchage["transfer_id"]; ?>"><?php echo $rowdatapurchage["transfer_id"]; ?></a></td>
					<td><?php echo date("d-m-Y", strtotime($rowdatapurchage["transfer_date"])); ?></td>
					<td><?php echo $rowdatapurchage["from_store_name"];  ?></td>
							
					<td><?php echo $rowDataOrganizationInfo["name"]; ?></td>		<td><?php echo nl2br($rowdatapurchage["note"]);  ?></td>		
				    <td><?php if(!empty($rowdatapurchage["photo"])){ ?><a href="download.php?path=StockTransfer/&download_file=<?php echo $rowdatapurchage["photo"]; ?>">Download</a><?php } ?>	</td>	
										
											
				
                    <td text align="center">
						
						<?php if (stripos($role_permission, "View") !== false) {  ?>	
						 <a class="btn btn-primary btn-sm" href="?Stock_Transfer_detail_vew/Stock Transfer/<?php echo $rowdatapurchage["transfer_id"]; ?>">
                              <i class="fas fa-eye">
                              </i>
                              View
                          </a>
                          	<a class="btn btn-primary btn-sm" href="?Stock_Transfer_Received_Pending_List_Create/<?php echo $rowdatapurchage["transfer_id"]; ?>/<?php echo $rowdatapurchage["transfer_id"]; ?>">
                              <i class="fas fa-eye">
                              </i>
                              Receive
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
