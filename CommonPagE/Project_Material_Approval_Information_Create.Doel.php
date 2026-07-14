
<section class="content">
      <div class="container-fluid">
        <!-- SELECT2 EXAMPLE -->
       
        <!-- /.card -->
  <form method="post" action="?<?php echo substr($page_title,0,-7); ?>/<?php echo $MenuName; ?>/MEDICINE_PURCHASE_HISTORY" enctype="multipart/form-data">
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
            <div class="row">
    <div class="col-md-12"><p style="text-align:center; color:#F00;">***All * marked fields are required***</p></div>


  <div class="col-sm-12"><p id="workordervalidity" style="font-size:30px;font-weight:bold;color: red;"></p></div>


	<style>
     .jodit_wysiwyg_iframe{
        display: none;
     }   
    </style>
		 <div class="col-sm-6">
        <div class="form-group"> 
         
         <label for="project_id">Path Name<span style="color:#F00;">*</span></label>
		  <input type="text" class="form-control" name="approval_path_name" placeholder="Path Name" id="approval_path_name" required>

        </div>
    </div>		
				
	  <div class="col-sm-6">
        <div class="form-group"> 
         
         <label for="project_id">Project Information<span style="color:#F00;">*</span></label>
		  <select name="project_id" id="project_id" class="form-control select2" data-quick-create="project" style="width:100%;" required >
            <option selected value="">Select Project Information</option>
            <?php
				  $ProjectInfo = $pdo->query("SELECT * FROM project_information WHERE project_information.deleted_at is NULL");
	              while($rowDataProjectInfo= $ProjectInfo->fetch()){
					$db_table='project_information'; 
					  
				   
				  ?>
            <option value="<?php echo $rowDataProjectInfo["id"]; ?>"><?php echo $rowDataProjectInfo["name"]; ?></option>
            <?php }  ?>
            </select>

        </div>
    </div>
   
				
				


	
    
    </div>
		  
		<div class="row">
  <div class="col-sm-12">
  <p><b>Approval Person Information&nbsp;:&nbsp;</b><span style="color:#FF0000">*</span></p>
    <div style="overflow-x:auto;">
		
	<table id="myTable" class="table order-list">
    <thead>
        <tr>
           
			<th>Name</th>
           
            <th>&nbsp;</th>
        </tr>
    </thead>
    <tbody>
        <tr>
          
			   <td ><input type="hidden" name="number_count" class="form-control"  value="1"/>
				<select name="employee_id1" id="employee_id1"  class="form-control select2" style="width:100%;" required ><option selected value="">Select Project Information</option><?php
				  $Employee_Info = $pdo->query("SELECT employee_information.*,hr_designation.name AS employee_designation FROM employee_information  INNER JOIN hr_designation ON employee_information.designation=hr_designation.id WHERE employee_information.user_status='Active' and employee_information.deleted_at is NULL");while($rowDataEmployee_Info= $Employee_Info->fetch()){  ?><option value="<?php echo $rowDataEmployee_Info["id"]; ?>"><?php echo $rowDataEmployee_Info["name_en"]." -  ".$rowDataEmployee_Info["employee_designation"]; ?></option>   <?php }  ?></select>
            </td>
		
            <td ><a class="deleteRow ibtnDel btn btn-md btn-danger">Delete</a>

            </td>
        </tr>
    </tbody>

   
    <tfoot>
        <tr>
            <td colspan="2" style="text-align: left;">
                <input type="button" class="btn btn-primary btn-block " id="addrow" value="Add More" />
            </td>
        </tr>
		
	
        
    </tfoot>
</table>	
		
    
 </div>   
</div>

 







  </div>	  
			  
			  
			  
			  
            <!-- /.row -->
          </div>
          <!-- /.card-body -->
          <div class="card-footer">
          <div class="box-tools pull-right">
       <button type="button" class="btn btn-warning" onclick="history.back(-1)"><i class="fa fa-window-close"></i> Cancel</button>&nbsp;&nbsp;&nbsp;
                    <button class="btn btn-primary" type="submit" name="Project_Material_Approval_Information_Create"><i class="fa fa-save"></i> Save </button>
                </div>
          </div>
        </div>
        <!-- /.card -->
</form>
 
    
        <!-- /.card -->

        
        <!-- /.row -->
        
        <!-- /.row -->
        
        <!-- /.row -->
      </div>
      <!-- /.container-fluid -->
    </section>

<script>
  
	 function get_total_vaue() {
		var sum = 0;
		$('.cm_cls').each(function() {
        sum += Number($(this).val());
		$('#totalPrice').val(sum);
		$('#subTotal').val(sum);
		$('#duePrice').val(sum);
    });
	 }

$(document).ready(function () {
    var counter = 2;

    $("#addrow").on("click", function () {
        var newRow = $("<tr>");
        var cols = "";
		
		cols += '<td><input type="hidden" name="number_count" class="form-control"  value="' + counter + '"/><select name="employee_id' + counter + '" id="employee_id' + counter + '"  class="employee_id form-control select2" style="width:100%;" required ><option selected value="">Select Project Information</option><?php  $Employee_Info = $pdo->query("SELECT employee_information.*,hr_designation.name AS employee_designation FROM employee_information  INNER JOIN hr_designation ON employee_information.designation=hr_designation.id WHERE employee_information.user_status='Active' and employee_information.deleted_at is NULL");while($rowDataEmployee_Info= $Employee_Info->fetch()){  ?><option value="<?php echo $rowDataEmployee_Info["id"]; ?>"><?php echo $rowDataEmployee_Info["name_en"]." -  ".$rowDataEmployee_Info["employee_designation"]; ?></option><?php }  ?></select> </td>';
	
      
        cols += '<td><input type="button" class="ibtnDel btn btn-md btn-danger "  value="Delete"></td>';
        newRow.append(cols);
        $("table.order-list").append(newRow);
        counter++;
		$(".employee_id").select2({ allowClear:true, placeholder: "Select Employee Name" });
    });



    $("table.order-list").on("click", ".ibtnDel", function (event) {
        $(this).closest("tr").remove();       
        counter -= 1
    });


});



function calculateRow(row) {
    var price = +row.find('input[name^="price"]').val();

}

function calculateGrandTotal() {
    var grandTotal = 0;
    $("table.order-list").find('input[name^="price"]').each(function () {
        grandTotal += +$(this).val();
    });
    $("#grandtotal").text(grandTotal.toFixed(2));
}



	 
		 
  </script>	
 <script>
         document.getElementById("WORK_ORDER_DATE").valueAsDate = new Date();
        </script>


