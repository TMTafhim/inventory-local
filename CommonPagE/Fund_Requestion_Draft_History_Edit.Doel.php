<?php
$information = $pdo->query("SELECT employee_information.name_en AS name,project_information.name AS project_name,note,date,invoice_id,project_id,requestion_draft_histiory.store_id AS store_id,requistion_type,previous_cash_in_hand,sub_total_amount FROM `requestion_draft_histiory` INNER JOIN employee_information ON requestion_draft_histiory.employee_id=employee_information.id INNER JOIN project_information ON requestion_draft_histiory.project_id=project_information.id where requestion_draft_histiory.id='$DocumentData' and requestion_draft_histiory.deleted_at is NULL");
$rowdata = $information->fetch();

?>

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
			  
			  
			  </div>    <link rel="stylesheet" href="plugins/jquery-ui/jquery-ui.min.css">
    <script src="plugins/jquery-ui/jquery-ui.min.js"></script>		  
	<script type="text/javascript">
        $(document).ready(function(){

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
                                search: request.term,request:1,store_id:<?php echo $login_user_store_id; ?>
                            },
                            success: function( data ) {
                                response( data );
                            }
                        });
                    },
                    select: function (event, ui) {
                        $(this).val(ui.item.label); // display the selected text
                        var userid = ui.item.value; // selected id to input

                        // AJAX
                        $.ajax({
                            url: 'ajax_Requestion_Product_name.php',
                            type: 'post',
                            data: {userid:userid,request:3,store_id:<?php echo $login_user_store_id; ?>},
                            dataType: 'json',
                            success:function(response){
                                
                                var len = response.length;

                                if(len > 0){
                                
                                  var available_quantity = response[0]['available_quantity'];
								  var product_id = response[0]['product_id'];
								  var product_code = response[0]['product_code'];

									document.getElementById('available_quantity'+index).value = available_quantity;
									document.getElementById('product_id'+index).value = product_id;
									document.getElementById('productcode_'+index).value = product_code;
                                    
                                }
                                
                            }
                        });

                        return false;
                    }
                });
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
                            url: "ajax_Requestion_product_code.php",
                            type: 'post',
                            dataType: "json",
                            data: {
                                search: request.term,request:1,store_id:<?php echo $login_user_store_id; ?>
                            },
                            success: function( data ) {
                                response( data );
                            }
                        });
                    },
                    select: function (event, ui) {
                        $(this).val(ui.item.label); // display the selected text
                        var userid = ui.item.value; // selected id to input

                        // AJAX
                        $.ajax({
                            url: 'ajax_Requestion_product_code.php',
                            type: 'post',
                            data: {userid:userid,request:3,store_id:<?php echo $login_user_store_id; ?>},
                            dataType: 'json',
                            success:function(response){
                                
                                var len = response.length;

                                if(len > 0){
                                
                                  var available_quantity = response[0]['available_quantity'];
								  var product_id = response[0]['product_id'];
								  var product_name = response[0]['product_name'];

									document.getElementById('available_quantity'+index).value = available_quantity;
									document.getElementById('product_id'+index).value = product_id;
									document.getElementById('username_'+index).value = product_name;
                                    
                                }
                                
                            }
                        });

                        return false;
                    }
                });
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
			<th>Details</th>
			<th>Qty</th>
			<th>Rate</th>
			<th>Amount (Tk)</th>
			<th>Comments</th>
            <th>&nbsp;</th>
        </tr>
    </thead>
    <tbody>
		
		<?php 
		$informationProduct_Detail = $pdo->query("SELECT requestion_draft_detail.*,product_information.name AS product_name,product_information.unit AS product_unit,product_information.code AS product_code  FROM `requestion_draft_detail` INNER JOIN product_information ON requestion_draft_detail.product_id=product_information.id where  requestion_draft_detail.invoice_id='".$rowdata["invoice_id"]."' and requestion_draft_detail.deleted_at is NULL");
			$serial=1;		
		$requistion_amount=0;
            while ($rowdataProduct_Detail = $informationProduct_Detail->fetch()){
			$requistion_amount+=$rowdataProduct_Detail["requestion_amount"];	
				
				?>
		
		 <tr>
          
			   <td style="width: 20%;">
				   <input type="hidden" name="number_count" class="form-control"  value="<?php echo $serial; ?>"/>
				   <input type="hidden" name="edit_id<?php echo $serial; ?>" class="form-control"  value="<?php echo $rowdataProduct_Detail["id"]; ?>"/>
				   <input name="name<?php echo $serial; ?>" id="username_<?php echo $serial; ?>" type="text" placeholder="Name Here" value="<?php echo $rowdataProduct_Detail["product_name"]; ?>" class="username form-control" data-quick-create-product="1" style="width:100%;" required="required">
				    <input name="product_id<?php echo $serial; ?>" id="product_id<?php echo $serial; ?>" type="hidden" placeholder="Name Here" value="<?php echo $rowdataProduct_Detail["product_id"]; ?>"  class="form-control" style="width:100%;">
            </td>
              <td style="width: 10%;">
				   <input name="product_code<?php echo $serial; ?>" type="text" id="productcode_<?php echo $serial; ?>" value="<?php echo $rowdataProduct_Detail["product_code"]; ?>" placeholder="Code Here"  class="product_code form-control" style="width:100%;" >
            </td>
             <td style="width: 20%;">
				   <input name="detail<?php echo $serial; ?>" value="<?php echo $rowdataProduct_Detail["detail"]; ?>" type="text" id="detail" placeholder="Detail Here"  class=" form-control" style="width:100%;" >
            </td>
            <td style="width: 10%;">
                <input type="text" id="quantity<?php echo $serial; ?>" value="<?php echo $rowdataProduct_Detail["requestion_quantity"]; ?>" name="quantity<?php echo $serial; ?>"  class="form-control " onkeyup="get_total_vaue();" oninput="calculate<?php echo $serial; ?>()" placeholder="Quantity Here .." required/>
            </td>
            <td style="width: 10%;">
                <input type="text" id="rate<?php echo $serial; ?>" value="<?php echo $rowdataProduct_Detail["requistion_rate"]; ?>" name="rate<?php echo $serial; ?>"  class="form-control " onkeyup="get_total_vaue();" oninput="calculate<?php echo $serial; ?>()" placeholder="Rate Here .." required/>
            </td>
		
            <td style="width: 15%;">
                <input type="text" id="total_amount<?php echo $serial; ?>" value="<?php echo $rowdataProduct_Detail["requestion_amount"]; ?>" name="requestion_amount<?php echo $serial; ?>"  class="form-control cm_cls" placeholder="Amount Here .." required/>
            </td>
		  <td style="width: 20%;">
				   <input name="comment<?php echo $serial; ?>" value="<?php echo $rowdataProduct_Detail["comment"]; ?>" type="text" id="comment" placeholder="Comments Here"  class=" form-control" style="width:100%;" >
            </td>
            <td style="width: 5%;"><a class="deleteRow ibtnDel btn btn-md btn-danger">Delete</a>

            </td>
        </tr>
		
		
		
        
		<?php 
			$serial++;
			} ?>
		
    </tbody>
<tfoot>
        <tr>
            <td colspan="8" style="text-align: left;">
                <input type="button" class="btn btn-primary btn-block " id="addrow" value="Add More" />
            </td>
        </tr>
        <tr>
          

              <td colspan="5" style="text-align:right;">
			Total Amount
            </td>
		
            <td>
                <input type="text" value="<?php echo $requistion_amount; ?>" id="totalPrice" oninput="summary_calculate();" class="form-control" placeholder="Amount Here .." required readonly/>
            </td>
		<td colspan="2"></td>
        </tr>
        <script>

       function summary_calculate() {
       var totalPrice = document.getElementById('totalPrice').value;
       var previous_cash_in_hand = document.getElementById('previous_cash_in_hand').value;

       var sub_total_amount = document.getElementById('sub_total_amount');
       var i = Math.round(Number(totalPrice) - Number(previous_cash_in_hand)) ;

       sub_total_amount.value = i;


       }

		</script>
		
       <tr>
          

              <td colspan="5" style="text-align:right;">
			Cash in Hand
            </td>
		
            <td>
                <input type="text" id="previous_cash_in_hand" oninput="summary_calculate();" class="form-control" value="<?php echo $rowdata["previous_cash_in_hand"]; ?>" placeholder="Amount Here .." name="previous_cash_in_hand" required />
            </td>
		<td colspan="2"></td>
        </tr>  
		
		 <tr>
          

              <td colspan="5" style="text-align:right;">
			Subtotal Amount
            </td>
		
            <td>
                <input type="text" id="sub_total_amount" class="form-control" placeholder="Amount Here .." value="<?php echo $rowdata["sub_total_amount"]; ?>" name="sub_total_amount" required readonly />
            </td>
		<td colspan="2"></td>
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
                    <button class="btn btn-primary" type="submit" name="Fund_Requestion_Draft_History_Edit"><i class="fa fa-save"></i> Save </button>
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
		
		cols += '<td><input type="hidden" name="number_count" class="form-control"  value="' + counter + '"/>  <input name="name' + counter + '" id="username_' + counter + '" class="username form-control" data-quick-create-product="1" placeholder="Name Here" style="width:100%;" required="required"><input name="product_id' + counter + '" id="product_id' + counter + '" type="hidden" placeholder="Name Here"  class="form-control" style="width:100%;"></td>';
		
		 cols += '<td> <input name="product_code' + counter + '" type="text" id="productcode_' + counter + '" placeholder="Code Here"  class="product_code form-control" style="width:100%;" ></td>';
		 
	cols += '<td> <input name="detail' + counter + '" type="text" id="detail' + counter + '" placeholder="Detail Here"  class=" form-control" style="width:100%;" ></td>';	
	cols += '<td> <input type="text" id="quantity' + counter + '" name="quantity' + counter + '"  class="form-control " onkeyup="get_total_vaue();" oninput="calculate' + counter + '()" placeholder="Quantity Here .." required/></td>';	
	cols += '<td> <input type="text" id="rate' + counter + '" name="rate' + counter + '"  class="form-control " onkeyup="get_total_vaue();" oninput="calculate' + counter + '()" placeholder="Rate Here .." required/></td>';	
		
        cols += '<td><input type="text" class="form-control cm_cls" onkeyup="get_total_vaue();" oninput="calculate' + counter + '()" id="total_amount' + counter + '" placeholder="Amount Here .." name="requestion_amount' + counter + '" required/></td>';
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
         document.getElementById("WORK_ORDER_DATE").valueAsDate = new Date();
        </script>


