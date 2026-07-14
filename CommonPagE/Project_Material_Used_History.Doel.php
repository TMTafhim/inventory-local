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
                <a href="?Fund_Used_Distribution_Create/<?php echo $MenuName; ?>" class="btn btn-secondary">
                        <i class="fa fa-plus"></i>&nbsp;Fund Create
                    </a>&nbsp;&nbsp;&nbsp;
                
                    <a href="?Material_Used_Distribution_Create/<?php echo $MenuName; ?>" class="btn btn-primary">
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
                                 Project Name
                                </th>
								
								<th>
                             
                                    Option
                               
								</th>
                            </tr>
                            
                        </thead> 
                        <tbody aria-relevant="all" aria-live="polite" role="alert">
            <?php
			if($_SESSION['USER_TYPE']=='Admin'){ 					
			$informationpurchage = $pdo->query("SELECT material_used_summary.*,project_information.name AS project_name FROM `material_used_summary` INNER JOIN project_information ON material_used_summary.project_id=project_information.id  WHERE  material_used_summary.deleted_at is NULL order by material_used_summary.id DESC limit 0,300");
			 }else{	
				
			$informationpurchage = $pdo->query("SELECT material_used_summary.*,project_information.name AS project_name FROM `material_used_summary` INNER JOIN project_information ON material_used_summary.project_id=project_information.id  WHERE material_used_summary.created_by='".$_SESSION['LoginReGiSterSession']."' and  material_used_summary.deleted_at is NULL order by material_used_summary.id DESC limit 0,300");	
			}
			$i=1;
            while ($rowdatapurchage = $informationpurchage->fetch()){	
									
							
			$total=$i++;
			$db_table='material_used_summary';	
				
						?>
                        
                        <tr data-product-ids="<?php echo productUsageFilterIds($pdo, 'material_used', $rowdatapurchage["invoice_id"]); ?>">
                    <td><?php echo $total; ?></td>                    
                    <td><a href="?Project_Material_Used_History_View/<?php echo $rowdatapurchage["invoice_id"]; ?>"><?php echo $rowdatapurchage["invoice_id"]; ?></a></td>
					<td><?php echo date("d-m-Y", strtotime($rowdatapurchage["date"])); ?></td>
					<td><?php echo $rowdatapurchage["project_name"];  ?></td>
							
											
				
                    <td text align="center">
							
						 <a class="btn btn-primary btn-sm" href="?Project_Material_Used_History_View/<?php echo $rowdatapurchage["invoice_id"]; ?>">
                              <i class="fas fa-eye">
                              </i>
                              View
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
