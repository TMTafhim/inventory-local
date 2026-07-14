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
  <form method="post" action="?Requestion/<?php echo $MenuName; ?>/requestion_histiory" enctype="multipart/form-data">
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
				<div class="col-sm-4">
        <div class="form-group">
            <label for="photo">Store Name<span style="color:#F00;">*</span></label>
			
			<?php /* if($_SESSION['USER_TYPE']=='Admin'){ ?>
             <select class="select2"  name="store_id" data-placeholder="Select Store" style="width: 100%;" required>
					<option value="">Select Store</option>
					<?php
				$InformationDepartment = $pdo->query("SELECT * FROM store_information WHERE deleted_at is NULL");
	              while($rowDataInformationDepartment= $InformationDepartment->fetch()){
						 ?>	 
                    <option  value="<?php echo $rowDataInformationDepartment["id"]; ?>"><?php echo $rowDataInformationDepartment["name"]; ?></option>
					<?php } ?>	 
                  </select>
          <?php }else{ 
				$InformationStore = $pdo->query("SELECT * FROM store_information WHERE id='".$row_Login_Datauser_information["store_id"]."' and  deleted_at is NULL");
	             $rowDataInformationStore= $InformationStore->fetch();
	             */
						 ?>	 
                   
			<p>Head Office (FR)</p>
			<input class="form-control " placeholder="Store Name Here ...." name="store_id" id="store_id" type="hidden" value="10" required>
			<?php /* } */ ?>
        </div>
    </div>
	  <div class="col-sm-4">
        <div class="form-group"> 
          <input class="form-control"  name="requistion_type" id="requistion_type" type="hidden" value="Fund" required>
         <label for="supplier_id">Project Information<span style="color:#F00;">*</span></label>
			<?php if($_SESSION['USER_TYPE']=='Admin'){ ?>
             <select name="project_id" id="project_id" data-placeholder="Select Project" class="form-control select2" data-quick-create="project" style="width:100%;" required >
            <option selected value="">Select Project Information</option>
            <?php
               
             $Project_info = $pdo->query("SELECT * FROM project_information where DELETED_AT is NULL");
                      $sl=1;
                            while($rowDataProject_info= $Project_info->fetch()){   
               
            
             ?>
            <option value="<?php echo $rowDataProject_info["id"]; ?>"><?php echo $rowDataProject_info["name"]; ?></option>
            <?php } ?>
            </select>
           <?php }else{
			$Project_info = $pdo->query("SELECT * FROM project_information where id='".$row_Login_Datauser_information["project_name"]."'");
            $rowDataProject_info= $Project_info->fetch();
	        echo "<p>".$rowDataProject_info["name"]."</p>";
			?>
			<input class="form-control " placeholder="Project Name Here ...." name="project_id" id="project_id" type="hidden" required value="<?php echo $row_Login_Datauser_information["project_name"]; ?>">
			<?php } ?>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="form-group">
        <label for="date">Date<span style="color:#F00;">*</span></label>
        <input class="form-control date" placeholder=" Date Here ...." name="date" id="date" type="date" required>
          
        </div>
    </div>
				
	<div class="col-sm-12">
        <div class="form-group">
            <label for="note">Note</label>
            <textarea class="form-control"  placeholder="Note Here ...." name="note" id="note" type="text" ></textarea>
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
        <tr>
          
			   <td style="width: 20%;"><input type="hidden" name="number_count" class="form-control"  value="1"/>
				   <input name="name1" id="username_1" type="text" placeholder="Name Here" class="username form-control" data-quick-create-product="1" style="width:100%;" required="required">
				    <input name="product_id1" id="product_id1" type="hidden" placeholder="Name Here"  class="form-control" style="width:100%;">
            </td>
              <td style="width: 10%;">
				   <input name="product_code1" type="text" id="productcode_1" placeholder="Code Here"  class="product_code form-control" style="width:100%;" >
            </td>
             <td style="width: 20%;">
				   <input name="detail1" type="text" id="detail" placeholder="Detail Here"  class=" form-control" style="width:100%;" >
            </td>
            <td style="width: 10%;">
                <input type="text" id="quantity1" name="quantity1"  class="form-control " onkeyup="get_total_vaue();" oninput="calculate1()" placeholder="Quantity Here .." required/>
            </td>
            <td style="width: 10%;">
                <input type="text" id="rate1" name="rate1"  class="form-control " onkeyup="get_total_vaue();" oninput="calculate1()" placeholder="Rate Here .." required/>
            </td>
		
            <td style="width: 15%;">
                <input type="text" id="total_amount1" name="requestion_amount1"  class="form-control cm_cls" placeholder="Amount Here .." required/>
            </td>
		  <td style="width: 20%;">
				   <input name="comment1" type="text" id="comment" placeholder="Comments Here"  class=" form-control" style="width:100%;" >
            </td>
            <td style="width: 5%;"><a class="deleteRow ibtnDel btn btn-md btn-danger">Delete</a>

            </td>
        </tr>
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
                <input type="text" id="totalPrice" class="form-control" placeholder="Amount Here .." required readonly/>
            </td>
		<td colspan="2"></td>
        </tr>
        
       <tr>
          

              <td colspan="5" style="text-align:right;">
			Cash in Hand
            </td>
		
            <td>
                <input type="text" id="previous_cash_in_hand" class="form-control" placeholder="Amount Here .." name="previous_cash_in_hand" required />
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
        <button class="btn btn-primary" type="submit" name="Insert_Fund_Requestion_History"><i class="fa fa-save"></i> Submit </button>
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


