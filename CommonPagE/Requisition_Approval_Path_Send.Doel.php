<?php
$information = $pdo->query("SELECT employee_information.name_en AS name,project_information.name AS project_name,note,date,invoice_id,project_id,requestion_draft_histiory.store_id AS store_id,requistion_type,requestion_draft_histiory.id AS requestion_draft_histiory_id,sub_total_amount FROM `requestion_draft_histiory` INNER JOIN employee_information ON requestion_draft_histiory.employee_id=employee_information.id INNER JOIN project_information ON requestion_draft_histiory.project_id=project_information.id where requestion_draft_histiory.id='$DocumentData' and requestion_draft_histiory.deleted_at is NULL");
$rowdata = $information->fetch();
$approvalEmployeeOptions='';
$Employee_Info = $pdo->query("SELECT employee_information.*,hr_designation.name AS employee_designation FROM employee_information LEFT JOIN hr_designation ON employee_information.designation=hr_designation.id WHERE employee_information.user_status='Active' and employee_information.deleted_at is NULL ORDER BY employee_information.name_en ASC");
while($rowDataEmployee_Info= $Employee_Info->fetch()){
	$approvalEmployeeOptions.='<option value="'.$rowDataEmployee_Info["id"].'">'.$rowDataEmployee_Info["name_en"].(!empty($rowDataEmployee_Info["employee_designation"]) ? " - ".$rowDataEmployee_Info["employee_designation"] : "").'</option>';
}
$finalApproverName='Zulfiquer Haider';
$FinalApproverInfo=$pdo->query("SELECT name_en FROM employee_information WHERE id='1' AND deleted_at IS NULL LIMIT 1");
$rowFinalApproverInfo=$FinalApproverInfo->fetch();
if(!empty($rowFinalApproverInfo["name_en"])){
	$finalApproverName=$rowFinalApproverInfo["name_en"];
}

?>

<section class="content">
      <div class="container-fluid">
        <!-- SELECT2 EXAMPLE -->
        
        <!-- /.card -->
  <form method="post" action="?<?php echo "Requisition_Draft/".$MenuName."/".$DocumentData; ?>" enctype="multipart/form-data">
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
           <div class="col-xl-12 details-div-area">	
			   <p style="text-align: center;font-weight: bold;"><?php echo nl2br($rowdata["note"]);  ?></p>
								<table width="100%">
                                
                              								
									<tbody>
									<tr>
										<td width="15%"><strong>Project Name</strong></td>
										<td width="85%" class="data-row" colspan="3">: &nbsp;<?php echo $rowdata["project_name"]; ?></td>
										
									</tr>	
										
									
                                    
                                   
                                  
								</tbody></table>
								
							
         
							</div>
			  
		<div class="row">
			<div class="col-sm-12">
				
				<input class="form-control" value="<?php echo $rowdata["store_id"];  ?>" placeholder=" Date Here ...." name="store_id"  type="hidden" required>	
		<input class="form-control" value="<?php echo $rowdata["project_id"];  ?>" placeholder=" Date Here ...." name="project_id"  type="hidden" required>	
		<input class="form-control" value="<?php echo $rowdata["requistion_type"];  ?>" placeholder=" Date Here ...." name="requistion_type"  type="hidden" required>
		<input class="form-control" value="<?php echo $rowdata["invoice_id"];  ?>" placeholder=" Date Here ...." name="draft_invoice_id"  type="hidden" required>	
		<input class="form-control" value="<?php echo $rowdata["requestion_draft_histiory_id"];  ?>" placeholder=" Date Here ...." name="draft_requistion_id"  type="hidden" required>		
			
        <input class="form-control date" value="<?php echo $rowdata["date"];  ?>" placeholder=" Date Here ...." name="date" id="date" type="hidden" required>
				
		<label>Approval Path<span style="color:#F00;">*</span></label>
		<input type="hidden" name="approval_path_mode" value="instant">

		<div id="instantApprovalPathBox" class="border rounded p-3 mb-3">
			<input type="hidden" name="instant_approval_path_name" value="Instant Approval Path - <?php echo $rowdata["invoice_id"]; ?>">
			<p class="mb-2"><strong>Select approvers in order</strong></p>
			<table class="table table-bordered table-sm mb-2 instant-approval-table">
				<thead>
					<tr>
						<th style="width:70px;">SL</th>
						<th>Employee</th>
						<th style="width:90px;">Action</th>
					</tr>
				</thead>
				<tbody id="instantApprovalRows">
					<?php for($instantRow=1;$instantRow<=4;$instantRow++){ ?>
					<tr>
						<td class="instant-sl"><?php echo $instantRow; ?><input type="hidden" name="instant_number_count" value="<?php echo $instantRow; ?>"></td>
						<td>
							<select name="instant_employee_id<?php echo $instantRow; ?>" class="form-control instant-employee select2" style="width:100%;">
								<option value="">Select Employee</option>
								<?php echo $approvalEmployeeOptions; ?>
							</select>
						</td>
						<td><button type="button" class="btn btn-danger btn-sm instant-remove"><i class="fa fa-trash"></i></button></td>
					</tr>
					<?php } ?>
				</tbody>
			</table>
			<button type="button" class="btn btn-sm btn-outline-primary" id="addInstantApprover"><i class="fa fa-plus"></i> Add Approver</button>
			<div class="alert alert-info mt-3 mb-0"><?php echo htmlspecialchars($finalApproverName, ENT_QUOTES, 'UTF-8'); ?> must be selected as the last approver.</div>
        </div>
    </div>
	<div class="col-md-12">
		<div class="alert alert-info mb-0">
			Select who should approve this requisition. Forward and return will stay inside this instant path.
		</div>
	</div>
			
	  
			  
			  
			  </div>	  
	  
			  
	
			  
			  
            <!-- /.row -->
          </div>
          <!-- /.card-body -->
          <div class="card-footer">
          <div class="box-tools pull-right">
       <button type="button" class="btn btn-warning" onclick="history.back(-1)"><i class="fa fa-window-close"></i> Cancel</button>&nbsp;&nbsp;&nbsp;
                    <button class="btn btn-primary" type="submit" name="Requisition_Approval_Path_Send_Data_Final"><i class="fa fa-paper-plane"></i> Send for Approval </button>
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
    var counter = 5;
	var employeeOptions = <?php echo json_encode($approvalEmployeeOptions); ?>;
	var finalApproverName = <?php echo json_encode($finalApproverName); ?>;

	function refreshInstantRows(){
		$('#instantApprovalRows tr').each(function(index){
			var rowNumber = index + 1;
			$(this).find('.instant-sl').html(rowNumber + '<input type="hidden" name="instant_number_count" value="' + rowNumber + '">');
			$(this).find('select.instant-employee').attr('name', 'instant_employee_id' + rowNumber);
		});
		counter = $('#instantApprovalRows tr').length + 1;
		refreshInstantEmployeeOptions();
	}

	function refreshInstantEmployeeOptions(){
		var selectedByRow = [];
		var seenEmployees = {};
		$('#instantApprovalRows select.instant-employee').each(function(){
			var selectedValue = $(this).val();
			if(selectedValue && seenEmployees[selectedValue]){
				$(this).val('').trigger('change.select2');
				selectedValue = '';
			}
			if(selectedValue){
				seenEmployees[selectedValue] = true;
			}
			selectedByRow.push(selectedValue);
		});

		$('#instantApprovalRows select.instant-employee').each(function(rowIndex){
			var currentValue = selectedByRow[rowIndex];
			$(this).find('option').each(function(){
				var optionValue = $(this).attr('value');
				if(!optionValue){
					$(this).prop('disabled', false);
					return;
				}
				$(this).prop('disabled', optionValue !== currentValue && selectedByRow.indexOf(optionValue) !== -1);
			});
			$(this).trigger('change.select2');
		});
	}

	$('#addInstantApprover').on('click', function(){
		var newRow = $('<tr>');
		newRow.append('<td class="instant-sl"></td>');
		newRow.append('<td><select name="instant_employee_id' + counter + '" class="form-control instant-employee select2" style="width:100%;"><option value="">Select Employee</option>' + employeeOptions + '</select></td>');
		newRow.append('<td><button type="button" class="btn btn-danger btn-sm instant-remove"><i class="fa fa-trash"></i></button></td>');
		$('#instantApprovalRows').append(newRow);
		newRow.find('.select2').select2({ allowClear:true, placeholder: "Select Employee" });
		refreshInstantRows();
	});

	$(document).on('change', '#instantApprovalRows select.instant-employee', function(){
		refreshInstantEmployeeOptions();
	});

	$(document).on('click', '.instant-remove', function(){
		if($('#instantApprovalRows tr').length <= 1){
			alert('At least one approver is required.');
			return;
		}
		$(this).closest('tr').remove();
		refreshInstantRows();
	});

	refreshInstantEmployeeOptions();

	$('form').on('submit', function(e){
		var selectedEmployees = [];
		$('#instantApprovalRows select.instant-employee').each(function(){
			if($(this).val()){
				selectedEmployees.push($(this).val());
			}
		});
		if(selectedEmployees.length === 0){
			alert('Please select approval employees.');
			e.preventDefault();
			return false;
		}
		if(selectedEmployees[selectedEmployees.length - 1] !== '1'){
			alert(finalApproverName + ' must be the last approver.');
			e.preventDefault();
			return false;
		}
		if((new Set(selectedEmployees)).size !== selectedEmployees.length){
			alert('Please select each approval employee only once.');
			e.preventDefault();
			return false;
		}
		return true;
	});

    $("#addrow").on("click", function () {
        var newRow = $("<tr>");
        var cols = "";
		
		cols += '<td><input type="hidden" name="number_count" class="form-control"  value="' + counter + '"/>  <input name="name' + counter + '" id="username_' + counter + '"     class="username form-control" placeholder="Name Here" style="width:100%;" required="required"><input name="product_id' + counter + '" id="product_id' + counter + '" type="hidden" placeholder="Name Here"  class="form-control" style="width:100%;"></td>';
		
		 cols += '<td> <input name="product_code' + counter + '" type="text" id="productcode_' + counter + '" placeholder="Code Here"  class="product_code form-control" style="width:100%;" ></td>';
	
		
        cols += '<td><input type="text" class="form-control" onkeyup="get_total_vaue();" oninput="calculate' + counter + '()" id="quantity' + counter + '" placeholder="Quantity Here .." name="quantity' + counter + '" required/></td>';
        
        cols += '<td> <input name="comment' + counter + '" type="text" id="comment' + counter + '" placeholder="Comment Here"  class=" form-control" style="width:100%;" ></td>';	
		
      
        cols += '<td><input type="button" class="ibtnDel btn btn-md btn-danger "  value="Delete"></td>';
        newRow.append(cols);
        $("table.order-list").append(newRow);
        counter++;
		$(".product_category").select2({ allowClear:true, placeholder: "Select Category" });
		$(".model").select2({  allowClear:true,  placeholder: "Select Name"  });
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
         if(document.getElementById("WORK_ORDER_DATE")){
         	document.getElementById("WORK_ORDER_DATE").valueAsDate = new Date();
         }
        </script>
