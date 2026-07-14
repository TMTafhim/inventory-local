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
          
		<table id="example1" class="table table-bordered table-striped">
                      <thead>
                            <tr>
                                <th>
                                    SL
                                </th>
                              
                                <th>
                                 Company
                                </th>
                                <th>
                                 Name
                                </th>
                               
                                <th>
                                 Email
                                </th>
                                
                                <th>
                                 Mobile
                                </th>
                                <th>
                                 Address
                                </th>
								
								<th>
                                 Due Amount
                                </th>
                               
                                <th>
                                    Option
                                </th>
                            </tr>
                            
                        </thead> 
                        <tbody aria-relevant="all" aria-live="polite" role="alert">
                        <?php
				$information = $pdo->query("SELECT *  FROM `supplier_information` where deleted_at is NULL");
				$i=1;
                 while ($rowdata = $information->fetch()){
					
				$db_table='supplier_information';
				 $total=$i++;
						?>
                        
                        <tr>
                    <td text align="center"><?php echo $total; ?></td>                    
                    <td text align="center"><?php echo $rowdata["organization"]; ?></td>
                    <td text align="center"><?php echo $rowdata["name"]; ?></td>
                    <td text align="center"><?php echo $rowdata["email"]; ?></td>
                     <td text align="center"><?php echo $rowdata["mobile"]; ?></td>
                    <td text align="center"><?php echo $rowdata["address"]; ?></td>
                 <td text align="center"><?php echo $rowdata["amount"]; ?></td>
                   
                    <td class="project-actions text-right">
                       
                          <a class="btn btn-info btn-sm" href="?<?php echo $page_title; ?>_Edit/<?php echo $MenuName; ?>/<?php echo $rowdata["id"]; ?>/<?php echo $db_table; ?>">
                              <i class="fas fa-pencil-alt">
                              </i>
                              Edit
                          </a>
                          <a class="btn btn-danger btn-sm" href="?<?php echo $page_title; ?>/<?php echo $MenuName; ?>/<?php echo $rowdata["id"]; ?>/DELETE/<?php echo $db_table; ?>">
                              <i class="fas fa-trash">
                              </i>
                              Delete
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