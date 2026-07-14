<?php
$information = $pdo->prepare("SELECT employee_information.name_en AS name,project_information.name AS project_name,note,date,invoice_id,project_id,requestion_draft_histiory.store_id AS store_id,requistion_type,previous_cash_in_hand,sub_total_amount FROM requestion_draft_histiory INNER JOIN employee_information ON requestion_draft_histiory.employee_id=employee_information.id INNER JOIN project_information ON requestion_draft_histiory.project_id=project_information.id WHERE requestion_draft_histiory.id=:id AND requestion_draft_histiory.final_submit_status IS NULL AND requestion_draft_histiory.deleted_at IS NULL");
$information->execute(array(':id' => (int) $DocumentData));
$rowdata = $information->fetch();

if (!$rowdata) {
    echo '<div class="alert alert-warning m-3">This requisition draft is no longer editable or has already been submitted.</div>';
    return;
}

$emergencyCountStatement = $pdo->prepare("SELECT COUNT(*) FROM requestion_draft_detail WHERE invoice_id = :invoice_id AND emergency_quantity > 0 AND deleted_at IS NULL");
$emergencyCountStatement->execute(array(':invoice_id' => $rowdata['invoice_id']));
$emergencyItemCount = (int) $emergencyCountStatement->fetchColumn();

?>

<style>
    .emergency-edit-note { border-left: 4px solid #f59e0b; }
    .emergency-linked-row { background: #fffbeb; }
    .emergency-lock { color: #92400e; font-size: 12px; font-weight: 600; white-space: nowrap; }
</style>

<script>
	<?php for($seript_serial=1;$seript_serial<=500;$seript_serial++){ ?>
	  
		
		
	
       function calculate<?php echo $seript_serial; ?>() {
       var quantity = document.getElementById('quantity<?php echo $seript_serial; ?>').value;
       var rate = document.getElementById('rate<?php echo $seript_serial; ?>').value;

       var total_amount<?php echo $seript_serial; ?> = document.getElementById('total_amount<?php echo $seript_serial; ?>');
       var i = Math.round(Number(quantity) * Number(rate)) ;

       total_amount<?php echo $seript_serial; ?>.value = i;


       }
		<?php } ?>
		</script>
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
								<table width="100%">
                                
                              								
									<tbody>
									<tr>
										<td width="15%">Project Name</td>
										<td width="85%" class="data-row" colspan="3">: &nbsp;<?php echo $rowdata["project_name"]; ?></td>
										
									</tr>	
										
										
										<tr>
										<td width="15%">Requisition No</td>
										<td width="35%" class="data-row">: &nbsp;<?php echo date("Y"); ?> / <?php echo $rowdata["invoice_id"]; ?></td>
										<td width="10%">Create by</td>
										<td width="32%" class="data-row">: &nbsp;<?php echo $rowdata["name"]; ; ?></td>
									</tr>
                                   
                                    
                                   
                                  
								</tbody></table>
								
							
                                
                              
							</div> 
			  
		<div class="row">
			<div class="col-sm-4">
        <div class="form-group">
        <label for="date">Date<span style="color:#F00;">*</span></label>
		
		<input class="form-control" value="<?php echo $rowdata["store_id"];  ?>" placeholder=" Date Here ...." name="store_id"  type="hidden" required>	
		<input class="form-control" value="<?php echo $rowdata["project_id"];  ?>" placeholder=" Date Here ...." name="project_id"  type="hidden" required>	
		<input class="form-control" value="<?php echo $rowdata["requistion_type"];  ?>" placeholder=" Date Here ...." name="requistion_type"  type="hidden" required>
		<input class="form-control" value="<?php echo $rowdata["invoice_id"];  ?>" placeholder=" Date Here ...." name="invoice_id"  type="hidden" required>		
			
        <input class="form-control date" value="<?php echo $rowdata["date"];  ?>" placeholder=" Date Here ...." name="date" id="date" type="date" required>
          
        </div>
    </div>	  
	<div class="col-sm-8">
        <div class="form-group">
            <label for="note">Note</label>
            <textarea class="form-control"  placeholder="Note Here ...." name="note" id="note" type="text" ><?php echo $rowdata["note"];  ?></textarea>
        </div>
    </div>	  
			  
			  
			  </div>
		<?php if ($emergencyItemCount > 0) { ?>
		<div class="alert alert-warning emergency-edit-note">
			<strong>Emergency items are protected.</strong>
			These products were already issued from stock. You may increase Total Required, but it cannot be lower than Emergency Issued and the linked product cannot be removed.
		</div>
		<?php } ?>    <link rel="stylesheet" href="plugins/jquery-ui/jquery-ui.min.css">
    <script src="plugins/jquery-ui/jquery-ui.min.js"></script>		  
	<script type="text/javascript">
        $(document).ready(function(){
            function getSelectedStoreId() {
                return $('input[name="store_id"]').val() || $('#store_id').val() || <?php echo (int)$login_user_store_id; ?>;
            }

            function fillProductRow(index, product) {
                if (!product) return;
                $('#available_quantity' + index).val(product.available_quantity || 0);
                $('#product_id' + index).val(product.product_id || '');
                $('#productcode_' + index).val(product.product_code || '');
                $('#unit_' + index).val(product.product_unit || '');
                if (product.product_name) $('#username_' + index).val(product.product_name);
            }

            function lookupProductByName(index, name) {
                if (!name) return;
                $.ajax({
                    url: 'ajax_Requestion_Product_name.php',
                    type: 'post',
                    data: {userid: name, request: 3, store_id: getSelectedStoreId()},
                    dataType: 'json',
                    success: function(response) {
                        if (response && response.length > 0) fillProductRow(index, response[0]);
                    }
                });
            }

            function lookupProductByCode(index, code) {
                if (!code) return;
                $.ajax({
                    url: 'ajax_Requestion_Product_code.php',
                    type: 'post',
                    data: {userid: code, request: 3, store_id: getSelectedStoreId()},
                    dataType: 'json',
                    success: function(response) {
                        if (response && response.length > 0) fillProductRow(index, response[0]);
                    }
                });
            }
            window.materialDraftGetSelectedStoreId = getSelectedStoreId;
            window.materialDraftLookupProductByName = lookupProductByName;
            window.materialDraftLookupProductByCode = lookupProductByCode;

            $(document).on('keydown', '.username', function() {
                
                var id = this.id;
                var splitid = id.split('_');
                var index = splitid[1];

                $( '#'+id ).autocomplete({
                    source: function( request, response ) {
                        $.ajax({
                            url: "ajax_Requestion_Product_name.php",
                            type: 'post',
                            dataType: "json",
                            data: {
                                search: request.term,request:1,store_id:getSelectedStoreId()
                            },
                            success: function( data ) {
                                response( data );
                            }
                        });
                    },
                    select: function (event, ui) {
                        $(this).val(ui.item.label); // display the selected text
                        var userid = ui.item.value; // selected id to input

                        lookupProductByName(index, userid);

                        return false;
                    }
                });
            });
            $(document).on('blur', '.username', function() {
                var index = this.id.split('_')[1];
                if (!$('#product_id' + index).val()) lookupProductByName(index, $(this).val());
            });
            
            // Add more
         
        });

    </script>	
   <script type="text/javascript">
        $(document).ready(function(){

            $(document).on('keydown', '.product_code', function() {
                
                var id = this.id;
                var splitid = id.split('_');
                var index = splitid[1];

                $( '#'+id ).autocomplete({
                    source: function( request, response ) {
                        $.ajax({
                            url: "ajax_Requestion_Product_code.php",
                            type: 'post',
                            dataType: "json",
                            data: {
                                search: request.term,request:1,store_id:window.materialDraftGetSelectedStoreId()
                            },
                            success: function( data ) {
                                response( data );
                            }
                        });
                    },
                    select: function (event, ui) {
                        $(this).val(ui.item.label); // display the selected text
                        var userid = ui.item.value; // selected id to input

                        window.materialDraftLookupProductByCode(index, userid);

                        return false;
                    }
                });
            });
            $(document).on('blur', '.product_code', function() {
                var index = this.id.split('_')[1];
                if (!$('#product_id' + index).val()) window.materialDraftLookupProductByCode(index, $(this).val());
            });
            
            // Add more
         
        });

    </script> 		  
		<div class="row">
  <div class="col-sm-12">
  <p>Requestion Detail :<span style="color:#FF0000">*</span></p>
    <div style="overflow-x:auto;">
		
	<table id="myTable" class="table order-list">
    <thead>
         <tr>
           <th>Name</th>
			<th>Code</th>
			<th>Unit</th>
			<th>Available Qty</th>
			<th>Emergency Issued</th>
			<th>Total Required</th>
            <th>Rate</th>
            <th>Remarks</th>
            <th>&nbsp;</th>
        </tr>
    </thead>
    <tbody>
		
		<?php 
		$informationProduct_Detail = $pdo->query("SELECT requestion_draft_detail.*,product_information.name AS product_name,product_information.unit AS product_unit,product_information.code AS product_code,COALESCE(stock_information.stock,0) AS available_quantity FROM `requestion_draft_detail` INNER JOIN product_information ON requestion_draft_detail.product_id=product_information.id LEFT JOIN stock_information ON stock_information.product_id=requestion_draft_detail.product_id AND stock_information.store_id=requestion_draft_detail.store_id AND stock_information.deleted_at IS NULL where  requestion_draft_detail.invoice_id='".$rowdata["invoice_id"]."' and requestion_draft_detail.deleted_at is NULL");
			$serial=1;		
		$requistion_amount=0;
            while ($rowdataProduct_Detail = $informationProduct_Detail->fetch()){	
				
				?>
		
		 <tr<?php echo ((float) $rowdataProduct_Detail['emergency_quantity'] > 0) ? ' class="emergency-linked-row"' : ''; ?>>
          
			   <td style="width: 20%;">
				   <input type="hidden" name="edit_id<?php echo $serial; ?>" class="form-control"  value="<?php echo $rowdataProduct_Detail["id"]; ?>"/>
				   <input name="name<?php echo $serial; ?>" id="username_<?php echo $serial; ?>" type="text" placeholder="Name Here" value="<?php echo htmlspecialchars($rowdataProduct_Detail["product_name"]); ?>" class="username form-control" data-quick-create-product="1" style="width:100%;" required="required"<?php echo ((float) $rowdataProduct_Detail['emergency_quantity'] > 0) ? ' readonly' : ''; ?>>
				    <input name="product_id<?php echo $serial; ?>" id="product_id<?php echo $serial; ?>" type="hidden" placeholder="Name Here" value="<?php echo $rowdataProduct_Detail["product_id"]; ?>"  class="form-control" style="width:100%;">
            </td>
              <td style="width: 10%;">
				   <input name="product_code<?php echo $serial; ?>" type="text" id="productcode_<?php echo $serial; ?>" value="<?php echo $rowdataProduct_Detail["product_code"]; ?>" placeholder="Code Here"  class="product_code form-control" style="width:100%;" >
            </td>
            <td style="width: 10%;">
				<input type="text" id="unit_<?php echo $serial; ?>" class="form-control" value="<?php echo htmlspecialchars($rowdataProduct_Detail["product_unit"]); ?>" readonly>
			</td>
			<td style="width: 10%;">
				<input type="text" id="available_quantity<?php echo $serial; ?>" name="available_quantity<?php echo $serial; ?>" class="form-control" value="<?php echo htmlspecialchars($rowdataProduct_Detail["available_quantity"]); ?>" readonly>
			</td>
            <td style="width: 10%;">
                <input type="text" class="form-control" value="<?php echo number_format((float) $rowdataProduct_Detail['emergency_quantity'], 4, '.', ''); ?>" readonly>
            </td>

            <td style="width: 10%;">
                <input type="number" min="<?php echo max(0, (float) $rowdataProduct_Detail['emergency_quantity']); ?>" step="0.0001" id="quantity<?php echo $serial; ?>" value="<?php echo $rowdataProduct_Detail["requestion_quantity"]; ?>" name="quantity<?php echo $serial; ?>" class="form-control" onkeyup="get_total_vaue();" oninput="calculate<?php echo $serial; ?>()" placeholder="Quantity Here"/>
            </td>
             <td style="width: 10%;">
                <input type="text" id="requistion_rate<?php echo $serial; ?>" value="<?php echo $rowdataProduct_Detail["requistion_rate"]; ?>" name="requistion_rate<?php echo $serial; ?>"  class="form-control "  placeholder="Rate Here .." />
            </td>
		
		  <td style="width: 20%;">
				   <input name="comment<?php echo $serial; ?>" value="<?php echo $rowdataProduct_Detail["comment"]; ?>" type="text" id="comment" placeholder="Comments Here"  class=" form-control" style="width:100%;" >
            </td>
            <td style="width: 5%;">
				<?php if ((float) $rowdataProduct_Detail['emergency_quantity'] > 0) { ?>
					<span class="emergency-lock"><i class="fas fa-lock"></i> Linked</span>
				<?php } else { ?>
					<a class="deleteRow ibtnDel btn btn-md btn-danger">Delete</a>
				<?php } ?>
            </td>
        </tr>
		
		
		
        
		<?php 
			$serial++;
			} ?>
		
    </tbody>
<tfoot>
        <tr>
			<td colspan="9" style="text-align: left;">
                <input type="button" class="btn btn-primary btn-block " id="addrow" value="Add More" />
            </td>
        </tr>
		    
	

        
    </tfoot>
   
    
</table>
	<input type="hidden" name="number_count" id="number_count" value="<?php echo max(1, $serial - 1); ?>">
		
    
 </div>   
</div>

 







  </div>		  
			  
			  
            <!-- /.row -->
          </div>
          <!-- /.card-body -->
          <div class="card-footer">
          <div class="box-tools pull-right">
       <button type="button" class="btn btn-warning" onclick="history.back(-1)"><i class="fa fa-window-close"></i> Cancel</button>&nbsp;&nbsp;&nbsp;
                    <button class="btn btn-primary" type="submit" name="Material_Requestion_Draft_History_Edit"><i class="fa fa-save"></i> Save </button>
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
    var counter = <?php echo $serial; ?>;

    $("#addrow").on("click", function () {
        var newRow = $("<tr>");
        var cols = "";
		
		cols += '<td><input name="name' + counter + '" id="username_' + counter + '" class="username form-control" data-quick-create-product="1" placeholder="Name Here" style="width:100%;" required="required"><input name="product_id' + counter + '" id="product_id' + counter + '" type="hidden" class="form-control"></td>';

		 cols += '<td> <input name="product_code' + counter + '" type="text" id="productcode_' + counter + '" placeholder="Code Here"  class="product_code form-control" style="width:100%;" ></td>';
		cols += '<td><input type="text" id="unit_' + counter + '" class="form-control" placeholder="Unit" readonly></td>';
		cols += '<td><input type="text" id="available_quantity' + counter + '" name="available_quantity' + counter + '" class="form-control" placeholder="Quantity Here .." readonly></td>';
		cols += '<td><input type="text" class="form-control" value="0.0000" readonly></td>';

        cols += '<td><input type="number" min="0" step="0.0001" class="form-control" onkeyup="get_total_vaue();" oninput="calculate' + counter + '()" id="quantity' + counter + '" placeholder="Quantity Here" name="quantity' + counter + '"></td>';
        
        cols += '<td><input type="text" class="form-control"  id="requistion_rate' + counter + '" placeholder="Rate Here .." name="requistion_rate' + counter + '" /></td>';
        
        cols += '<td> <input name="comment' + counter + '" type="text" id="comment' + counter + '" placeholder="Comment Here"  class=" form-control" style="width:100%;" ></td>';	
		
      
        cols += '<td><input type="button" class="ibtnDel btn btn-md btn-danger "  value="Delete"></td>';
        newRow.append(cols);
        $("table.order-list").append(newRow);
		$('#number_count').val(counter);
        counter++;
		$(".product_category").select2({ allowClear:true, placeholder: "Select Category" });
		$(".model").select2({  allowClear:true,  placeholder: "Select Name"  });
    });



    $("table.order-list").on("click", ".ibtnDel", function (event) {
        $(this).closest("tr").remove();
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
