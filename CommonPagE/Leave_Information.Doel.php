<section class="content">

      <!-- Default box -->
      <div class="card">
        <div class="card-header d-print-none">
          <h3 class="card-title"><?php echo str_replace("_"," ",$page_title); ?></h3>

          <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
              <i class="fas fa-minus"></i>
            </button>
            <button type="button" class="btn btn-tool" data-card-widget="remove" title="Remove">
              <i class="fas fa-times"></i>
            </button>
            
            
            <div class="box-tools pull-right">
                
                
                 <button id="printpagebutton" class="btn btn-success btn-xl" style="margin-left:5px; color:#FFF;" onclick="window.print();return false;" />
                        <i class="fa fa-print"></i>
                       Print
                    </button>  
                    
                    <a href="?<?php echo $page_title; ?>_Create/<?php echo $MenuName; ?>" class="btn btn-warning">
                        <i class="fa fa-plus"></i>&nbsp; Create
                    </a>
                </div>
			  
          </div>
          
     
          
        </div>
        
        <div class="card-body p-0 d-print-none">
          <table id="example1" class="table table-bordered table-striped" >
              <thead>
                  <tr>
                      <th >
                          SL
                      </th>
                      <th >
                          Name 
                      </th>
                      <th >
                          Designation 
                      </th>
                      <th >
                          Department 
                      </th>
					   
					   <th>
                          Leave
                      </th>
                      <th >
                          Start Date
                      </th>
                     
                      <th>
                          End Date
                      </th>
					 <th>
                        Number of Days
                      </th>
                     
                     <?php if($_SESSION['USER_TYPE']=='Admin'){ ?>
                     
                      <th >
                          Option
                      </th>
                      
                      <?php } ?>
                  </tr>
              </thead>
              <tbody>
				  
				 <?php
			/*	 $start_date="2024-02-15";
				 $serial=1;
				echo $date_name=date('Y-m-d', strtotime($start_date. '+ '.$serial.' days'));  */
				  $Information = $pdo->query("SELECT * FROM `hr_leave_information` WHERE  DELETED_AT is NULL order by id DESC limit 0,100");
				  $sl=1;
	              while($rowDataInformation= $Information->fetch()){
	             $table='hr_leave_information'; 

	             
	             $Information_Employee = $pdo->query("SELECT employee_information.*,hr_department.name AS department,hr_designation.name AS designation FROM employee_information inner join hr_designation ON employee_information.designation=hr_designation.id INNER JOIN hr_department ON employee_information.department=hr_department.id WHERE employee_information.id='".$rowDataInformation["employee_id"]."' and   employee_information.DELETED_AT is NULL");
				  $rowDataInformation_Employee= $Information_Employee->fetch();
	             
	           
				  ?> 
				  
                  <tr>
                      <td>
                          <?php echo $sl; ?>
                      </td>
                      <td><?php echo "<b>".$rowDataInformation_Employee["name_bn"]."</b><br>".$rowDataInformation_Employee["name_en"]; ?> </td>
                      <td><?php echo $rowDataInformation_Employee["designation"]; ?></td>
                      <td><?php echo $rowDataInformation_Employee["department"]; ?></td>
                      <td><?php echo $rowDataInformation["leave_type"]; ?></td>
                      <td><?php echo date('d/m/Y', strtotime($rowDataInformation["start_date"])); ?></td>
                      <td><?php echo date('d/m/Y', strtotime($rowDataInformation["end_date"])); ?></td>
                      
                      <td><?php echo $rowDataInformation["leave_days"]; ?></td>
                    
              
              
                    <?php if($_SESSION['USER_TYPE']=='Admin'){ ?>
                      <td class="project-actions text-center">
                   
                          <a class="btn btn-danger btn-sm" href="?<?php echo $page_title; ?>/<?php echo $MenuName; ?>/<?php echo $rowDataInformation["id"]; ?>/DELETE_HR_Leave/<?php echo $table; ?>">
                              <i class="fas fa-trash">
                              </i>
                              Delete
                          </a>
                      </td>
                   <?php } ?>   
                      
                  </tr>
				  
				<?php
				 $sl++; 
				  
				  
				  } ?>  
				  
				  
                 
              </tbody>
          </table>
        </div>
        
        
        
        
        <div class="d-none d-print-block">
            
         <div class="row" > 
            <?php include("PrintTitle.php"); ?>
            <p style="text-align:center;">Date&nbsp;:&nbsp;<?php echo date("d-m-Y", strtotime($current_date));; ?></p>
             </div> 
			
			
			
		<table  class="table table-bordered table-striped" >
               <thead>
                  <tr>
                      <th >
                          SL
                      </th>
                      <th >
                          Name 
                      </th>
                      <th >
                          Designation 
                      </th>
                      <th >
                          Department 
                      </th>
					   
					   <th>
                          Saturday
                      </th>
                      <th >
                          Sunday
                      </th>
                     
                      <th>
                          Monday
                      </th>
					 <th>
                        Tuesday
                      </th>
                      <th>
                        Wednesday
                      </th>
                     
                     <th>
                        Thursday
                      </th>
                     <th>
                        Friday
                      </th>
                  </tr>
              </thead>
              <tbody>
				  
				 <?php
				  
				  $Information = $pdo->query("SELECT * FROM `hr_rostering_information` WHERE  DELETED_AT is NULL");
				  $sl=1;
	              while($rowDataInformation= $Information->fetch()){
	             $table='hr_rostering_information'; 

	             
	             $Information_Employee = $pdo->query("SELECT employee_information.*,hr_department.name AS department,hr_designation.name AS designation FROM employee_information inner join hr_designation ON employee_information.designation=hr_designation.id INNER JOIN hr_department ON employee_information.department=hr_department.id WHERE employee_information.id='".$rowDataInformation["employee_id"]."' and   employee_information.DELETED_AT is NULL");
				  $rowDataInformation_Employee= $Information_Employee->fetch();
	             
	           
				  ?> 
				  
                  <tr>
                      <td>
                          <?php echo $sl; ?>
                      </td>
                      <td><?php echo "<b>".$rowDataInformation_Employee["name_bn"]."</b><br>".$rowDataInformation_Employee["name_en"]; ?> </td>
                      <td><?php echo $rowDataInformation_Employee["designation"]; ?></td>
                      <td><?php echo $rowDataInformation_Employee["department"]; ?></td>
                      <td><?php echo $rowDataInformation["saturday"]; ?></td>
                      <td><?php echo $rowDataInformation["sunday"]; ?></td>
                      <td><?php echo $rowDataInformation["monday"]; ?></td>
                      
                      <td><?php echo $rowDataInformation["tuesday"]; ?></td>
                    <td><?php echo $rowDataInformation["wednesday"]; ?></td>
                    <td><?php echo $rowDataInformation["thursday"]; ?></td>
                    
                   <td><?php echo $rowDataInformation["friday"]; ?></td>
                    
               
                  </tr>
				  
				<?php
				 $sl++; 
				  
				  
				  } ?>  
				  
				  
                 
              </tbody>
          </table>	
			
			
         
        </div>
        
        

        
        
        <!-- /.card-body -->
      </div>
      <!-- /.card -->

    </section>