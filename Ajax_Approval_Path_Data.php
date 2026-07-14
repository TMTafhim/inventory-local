<?php
include("BDB/DBConnEction.php");
if(isset($_POST["approval_path_id"])){
$approval_path_id=$_POST["approval_path_id"];
	?>
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
					
                      
                  </tr>
              </thead>
              <tbody>
				  
				 <?php
			
				$sl=1;
				$Project_Material_Approval_Info = $pdo->query("SELECT employee_information.*,hr_designation.name AS employee_designation FROM project_approval_path_inforamtion INNER JOIN employee_information ON project_approval_path_inforamtion.employee_id=employee_information.id INNER JOIN hr_designation ON employee_information.designation=hr_designation.id WHERE project_approval_path_inforamtion.approval_path_name='".$approval_path_id."'  and project_approval_path_inforamtion.deleted_at is NULL ORDER BY project_approval_path_inforamtion.id ASC");	  
				 $approvalRows=$Project_Material_Approval_Info->fetchAll();
				 $lastIndex=count($approvalRows)-1;
				 foreach($approvalRows as $rowIndex=>$rowDataProject_Material_Approval_Info){	  
					  
					  	  
				  ?> 
				  
                  <tr>
                      <td>
                          <?php echo $sl; ?>
                      </td>
					  <td>   <?php echo $rowDataProject_Material_Approval_Info["name_en"]; ?><?php if((int)$rowDataProject_Material_Approval_Info["id"]===1){ echo " (Final Approver)"; } ?></td>
					  
                      <td>   <?php echo $rowDataProject_Material_Approval_Info["employee_designation"]; ?></td>
					  
                      
                  </tr>
				  
				<?php
				 $sl++; 
				}
				   ?>  
				  
				  
                 
              </tbody>
          </table>	
<?php if($lastIndex<0 || (int)$approvalRows[$lastIndex]["id"]!==1){ ?>
<div class="alert alert-warning">This path cannot be used until employee ID 1 is the last approver.</div>
<?php } ?>
<?php	
}
?>

