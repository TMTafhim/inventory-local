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
                             
                                    Option
                               
								</th>
                            </tr>
                            
                        </thead> 
                        <tbody aria-relevant="all" aria-live="polite" role="alert">
            <?php
			if($_SESSION['USER_TYPE']=='Admin'){ 					
			$informationpurchage = $pdo->query("SELECT distribution_summary.*,project_information.name AS project_name,store_information.name AS store_name FROM `distribution_summary` INNER JOIN project_information ON distribution_summary.project_id=project_information.id INNER JOIN store_information ON distribution_summary.store_id=store_information.id WHERE  distribution_summary.deleted_at is NULL order by distribution_summary.id DESC limit 0,300");
			 }else{	
				
			$informationpurchage = $pdo->query("SELECT distribution_summary.*,project_information.name AS project_name,store_information.name AS store_name FROM `distribution_summary` INNER JOIN project_information ON distribution_summary.project_id=project_information.id INNER JOIN store_information ON distribution_summary.store_id=store_information.id WHERE distribution_summary.created_by='".$_SESSION['LoginReGiSterSession']."' and  distribution_summary.deleted_at is NULL order by distribution_summary.id DESC limit 0,300");	
			}
			$i=1;
            while ($rowdatapurchage = $informationpurchage->fetch()){	
									
							
			$total=$i++;
			$db_table='distribution_summary';	
				
						?>
                        
                        <tr data-product-ids="<?php echo productUsageFilterIds($pdo, 'requisition', $rowdatapurchage["invoice_id"]); ?>">
                    <td><?php echo $total; ?></td>                    
                    <td><a href="?Distribution_History_Indivisual/<?php echo $rowdatapurchage["invoice_id"]; ?>/<?php echo $rowdatapurchage["distribution_id"]; ?>"><?php echo $rowdatapurchage["distribution_id"]; ?></a></td>
					<td><?php echo date("d-m-Y", strtotime($rowdatapurchage["date"])); ?></td>
					<td><?php echo $rowdatapurchage["project_name"];  ?></td>
							
					<td><?php echo $rowdatapurchage["store_name"]; ?></td>				
										
											
				
                    <td text align="center">
							
						 <a class="btn btn-primary btn-sm" href="?Distribution_History_Indivisual/<?php echo $rowdatapurchage["invoice_id"]; ?>/<?php echo $rowdatapurchage["distribution_id"]; ?>">
                              <i class="fas fa-eye">
                              </i>
                              View
                          </a>
						
						<a class="btn btn-primary btn-sm" href="?Distribution_Challan_History/<?php echo $rowdatapurchage["invoice_id"]; ?>/<?php echo $rowdatapurchage["distribution_id"]; ?>">
                              <i class="fas fa-eye">
                              </i>
                              Challan
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
