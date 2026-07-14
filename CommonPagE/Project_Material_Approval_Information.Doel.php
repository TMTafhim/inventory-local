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
          <table id="example1" class="table table-bordered table-striped" >
              <thead>
                  <tr>
                      <th >
                          SL
                      </th>
					  <th >
                          Path Name
                      </th>
                      <th >
                         Project Name
                      </th>
					  <th >
                          Approval Information
                      </th>
                      <th >
                          Option
                      </th>
                      
                  </tr>
              </thead>
              <tbody>
				  
				 <?php
				  $ProjectInfo = $pdo->query("SELECT * FROM project_approval_path_name WHERE project_approval_path_name.deleted_at is NULL");
				  $sl=1;
	              while($rowDataProjectInfo= $ProjectInfo->fetch()){
					$db_table='project_approval_path_name'; 
					  
					  
				$Project_Information_data = $pdo->query("SELECT * FROM project_information WHERE id='".$rowDataProjectInfo["project_id"]."'");
				$rowData_Project_Information_data= $Project_Information_data->fetch();	  
					  
				 //$Project_Material_Approval_Info = $pdo->query("SELECT * FROM project_approval_path_inforamtion  WHERE project_id='".$rowDataProjectInfo["id"]."' and project_approval_path_inforamtion.deleted_at is NULL");
				
				$Project_Material_Approval_Info = $pdo->query("SELECT employee_information.*,hr_designation.name AS employee_designation FROM project_approval_path_inforamtion INNER JOIN employee_information ON project_approval_path_inforamtion.employee_id=employee_information.id INNER JOIN hr_designation ON employee_information.designation=hr_designation.id WHERE project_approval_path_inforamtion.approval_path_name='".$rowDataProjectInfo["id"]."' and project_approval_path_inforamtion.project_id='".$rowDataProjectInfo["project_id"]."' and project_approval_path_inforamtion.deleted_at is NULL");	  
				 $rowDataProject_Material_Approval_Info= $Project_Material_Approval_Info->fetch();	  
					  
					  
				if(!empty($rowDataProject_Material_Approval_Info)){	  
				  ?> 
				  
                  <tr>
                      <td>
                          <?php echo $sl; ?>
                      </td>
					  <td>   <?php echo $rowDataProjectInfo["approval_path_name"]; ?></td>
					  
                      <td>   <?php echo $rowData_Project_Information_data["name"]; ?></td>
					  <td><?php
					$data_serial=1;
					$Project_Material_Approval_Info_employee = $pdo->query("SELECT employee_information.*,hr_designation.name AS employee_designation FROM project_approval_path_inforamtion INNER JOIN employee_information ON project_approval_path_inforamtion.employee_id=employee_information.id INNER JOIN hr_designation ON employee_information.designation=hr_designation.id WHERE project_approval_path_inforamtion.approval_path_name='".$rowDataProjectInfo["id"]."' and project_approval_path_inforamtion.project_id='".$rowDataProjectInfo["project_id"]."' and project_approval_path_inforamtion.deleted_at is NULL order by project_approval_path_inforamtion.id ASC");	
						  while($rowDataProject_Material_Approval_Info_data= $Project_Material_Approval_Info_employee->fetch()){
						echo "<p style='margin:0px;'>".$data_serial.". ".$rowDataProject_Material_Approval_Info_data["name_en"]." - ".$rowDataProject_Material_Approval_Info_data["employee_designation"]."</p>";	  
							  
						$data_serial++;	  
						  }
						  
						  ?>   </td>
                      <td class="project-actions text-right">
                       
                          <a class="btn btn-info btn-sm" href="?<?php echo $page_title; ?>_Edit/<?php echo $MenuName; ?>/<?php echo $rowDataProjectInfo["id"]; ?>/<?php echo $db_table; ?>">
                              <i class="fas fa-pencil-alt">
                              </i>
                              Edit
                          </a>
                          <a class="btn btn-danger btn-sm" href="?<?php echo $page_title; ?>/<?php echo $MenuName; ?>/<?php echo $rowDataProjectInfo["id"]; ?>/DELETE_Project_Material_Approval_Path/<?php echo $db_table; ?>">
                              <i class="fas fa-trash">
                              </i>
                              Delete
                          </a>
                      </td>
                  </tr>
				  
				<?php
				 $sl++; 
				}
				  } ?>  
				  
				  
                 
              </tbody>
          </table>
        </div>
        <!-- /.card-body -->
      </div>
      <!-- /.card -->

    </section>
