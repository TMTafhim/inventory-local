
<style>
@media print
{    
    .no-print, .no-print *
    {
        display: none !important;
    }
		
}
.table td, .table th {
    padding: 10px 4px;
    vertical-align: top;
    border-top: 1px solid #dee2e6;
}
</style>
<section class="content no-print">
      <div class="container-fluid">
        <!-- SELECT2 EXAMPLE -->
        
        <!-- /.card -->

        <!-- SELECT2 EXAMPLE -->
        <div class="card card-default">
          <div class="card-header">
            <h3 class="card-title"> <button type="button" class="btn btn-warning" onclick="history.back(-1)"><i class="fa fa-reply"></i> back</button></h3>

            <div class="card-tools">
              <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
              </button>
              <button type="button" class="btn btn-tool" data-card-widget="remove">
                <i class="fas fa-times"></i>
              </button>
            </div>
          </div>
          <!-- /.card-header -->
          <div class="card-body">
		    <form method="post" action="?<?php echo $page_title; ?>" enctype="multipart/form-data" class="no-print"> 
            <div class="row">
                <div class="col-md-12"><p style="text-align:center; color:#F00;">***All * marked fields are required***</p></div>
       <div class="col-md-2">  </div>          
       <div class="col-md-4">
                <div class="form-group">
                
                    <div class="input-group date" id="reservationdate" data-target-input="nearest">
                        <input type="text" class="form-control datetimepicker-input" id="RegistrationDate" name="tokendate" data-target="#reservationdate" value="<?php  if(!empty($_POST["tokendate"])){ echo $_POST["tokendate"]; }else{  echo date("m/d/Y"); } ?>" style="border:1px solid #ced4da;" />
                        <div class="input-group-append" data-target="#reservationdate" data-toggle="datetimepicker">
                            <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                        </div>
                    </div>
                  </div>
                <!-- /.form-group -->
           
              </div>    
                    
   <div class="col-sm-2">
        <div class="form-group">
         
            <input class="btn btn-primary "    name="Token_Serach" type="submit" value="Search"  >
          
        </div>
    </div>
        
                
           
</div>
</form>
	  
			<div class="row">
			 
			  <div class="col-sm-12">
			
				  
			<div class="row" > 
            
            <div class="col-md-12">  
	<p style="text-align: center;font-size: 20px;margin: 0px;">Date&nbsp;:&nbsp;<?php if(!empty($_POST["tokendate"])){ 
    $current_date=date("Y-m-d", strtotime($_POST["tokendate"]));	    
	echo date("d/m/Y", strtotime($_POST["tokendate"])); }else{ 
	
	echo date("d/m/Y", strtotime($current_date)) ; }?></p>
	
 
  <table id="example1" class="table table-bordered table-striped">
	 
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
                      <th >
                           Date
                      </th>	
    </tr>
</thead>



 
<tbody>
<?php

$Information = $pdo->query("SELECT * FROM `hr_leave_detial_information` WHERE  DELETED_AT is NULL and leave_date='".$current_date."' ");
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
                      <td><?php echo date('d/m/Y', strtotime($rowDataInformation["leave_date"])); ?></td>
                     
                      
                  </tr>
				  
				<?php
				 $sl++; 
				  
				  
				  } ?> 
	</tbody>
  </table>
  
  </div></div>	  
				
				  
				  
				  
				  
				  
				
				  
				</div>
				
		
				
				
					 <!-- /.card-body -->
   
			  
			  </div>  
	
            
            <!-- /.row -->
          </div>
          
        </div>

        
        <!-- /.card -->

        
        <!-- /.row -->
        
        <!-- /.row -->
        
        <!-- /.row -->
      </div>
      <!-- /.container-fluid -->
    </section>
