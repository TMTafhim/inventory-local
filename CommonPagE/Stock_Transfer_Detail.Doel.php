

<script>
	<?php for($seript_serial=1;$seript_serial<=500;$seript_serial++){ ?>
	  
		
		
	
       function calculate<?php echo $seript_serial; ?>() {
       var quantity = document.getElementById('quantity<?php echo $seript_serial; ?>').value;
       var rate = document.getElementById('rate<?php echo $seript_serial; ?>').value;

       var total_amount<?php echo $seript_serial; ?> = document.getElementById('total_amount<?php echo $seript_serial; ?>');
       var i = Math.round(Number(quantity) * Number(rate)) ;

       total_amount<?php echo $seript_serial; ?>.value = i;


       }

     function check_available<?php echo $seript_serial; ?>() {
    var have_value = 0+document.getElementById('availablequantity<?php echo $seript_serial; ?>').value;
      var input_value = 0+document.getElementById('transfer_quantity<?php echo $seript_serial; ?>').value;
      if(parseInt(input_value) > parseInt(have_value))
      {
        document.getElementById('transfer_quantity<?php echo $seript_serial; ?>').value="";
      }
      }
  

		<?php } ?>
		</script>
<section class="content">
      <div class="container-fluid">
        <!-- SELECT2 EXAMPLE -->
  <?php if(!empty($_POST["TO_STORE_ID"])){ ?>      
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

<?php
$From_Store_ID = $pdo->query("SELECT * FROM store_information WHERE ID='".$_POST["FROM_STORE_ID"]."'");
 $rowDataFrom_Store_ID= $From_Store_ID->fetch();


 $To_Store_ID = $pdo->query("SELECT * FROM store_information WHERE ID='".$_POST["TO_STORE_ID"]."'");
 $rowDataTo_Store_ID= $To_Store_ID->fetch();
 ?>




  <div class="col-sm-4">
        <div class="form-group">
            <label for="FROM_STORE_ID">From Store</label>
            <input class="form-control"  name="FROM_STORE_ID" id="FROM_STORE_ID" value="<?php echo $rowDataFrom_Store_ID["id"]; ?>" type="hidden" >
          <p><?php echo $rowDataFrom_Store_ID["name"]; ?></p>
        </div>
    </div>

    <div class="col-sm-4">
        <div class="form-group">
            <label for="TO_STORE_ID">To Store</label>
            <input class="form-control"  name="TO_STORE_ID" id="TO_STORE_ID" value="<?php echo $rowDataTo_Store_ID["id"]; ?>" type="hidden" >
          <p><?php echo $rowDataTo_Store_ID["name"]; ?></p>
        </div>
    </div>
    
	
	 
    <div class="col-sm-4">
        <div class="form-group">
        <label for="TRANSFER_DATE">Transfer Date<span style="color:#F00;">*</span></label>
        <input class="form-control date" placeholder="Transfer Date Here ...." name="TRANSFER_DATE" id="TRANSFER_DATE" type="date" required>
          
        </div>
    </div>
	<div class="col-sm-6">
        <div class="form-group">
            <label for="note">Note</label>
            <textarea class="form-control"  placeholder="Note Here ...." name="note" id="note" type="text" ></textarea>
        </div>
    </div>
					<script type="text/javascript">
function validateImage() {
    var formData = new FormData();
 
    var file = document.getElementById("img").files[0];
 
    formData.append("Filedata", file);
    var t = file.type.split('/').pop().toLowerCase();
    if (t != "jpeg" && t != "jpg" && t != "png" && t != "bmp" && t != "gif" && t != "pdf") {
        alert('Please select a valid image/pdf file');
        document.getElementById("img").value = '';
        return false;
    }
    if (file.size > 5002400) {
        alert('Max Upload size is 5MB only');
        document.getElementById("img").value = '';
        return false;
    }
    return true;
}
</script>
	<div class="col-sm-6">
        <div class="form-group">
            <label for="photo">Attachment(Upload)</label>
             <input class="form-control" name="PHOTO" id="img" onchange="validateImage()" type="file" >
          
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
                            url: "Ajax_Stock_Transfer.php",
                            type: 'post',
                            dataType: "json",
                            data: {
                                search: request.term,request:1,store_id:<?php echo $rowDataFrom_Store_ID["id"]; ?>
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
                            url: 'ajax_Stock_available.php',
                            type: 'post',
                            data: {userid:userid,request:2,store_id:<?php echo $rowDataFrom_Store_ID["id"]; ?>},
                            dataType: 'json',
                            success:function(response){
                                
                                var len = response.length;

                                if(len > 0){
                                
                                  var availablequantity = response[0]['availablequantity'];
								  var product_id = response[0]['product_id'];	

									document.getElementById('availablequantity'+index).value = availablequantity;
									document.getElementById('product_id'+index).value = product_id;
                                    
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
		<div class="col-sm-12">
				
				
				<div class="row">
  <div class="col-sm-12">
  <p>Transfer History :<span style="color:#FF0000">*</span></p>
    <div style="overflow-x:auto;">
		
	<table id="myTable" class="table order-list" style="width:100%;">
    <thead>
        <tr>
           
			<th>Name</th>
            <th>Available Quantity</th>
            <th>Transfer Quantity</th>
            <th>&nbsp;</th>
        </tr>
    </thead>
    <tbody>
        <tr>
          
			   <td style="width: 35%;"><input type="hidden" name="number_count" class="form-control"  value="1"/>
				  <input name="name1" id="username_1" placeholder="Name Here"  class="username form-control"  required="required">
				   <input type="hidden" id="product_id1" name="product_id1"  class="form-control" />
            </td>
            </td>
		
            <td>
                <input type="text" id="availablequantity1" name="availablequantity1"  class="form-control" onkeyup="get_total_vaue();" oninput="calculate1()" placeholder="Quantity Here .." required readonly />
            </td>
			<td>
			<input class="form-control " placeholder="Transfer Quantity Here ...." onkeyup='check_available1()'  name="transfer_quantity1" id="transfer_quantity1" type="text" required>
			</td>
           
            <td ><a class="deleteRow ibtnDel btn btn-md btn-danger">Delete</a>

            </td>
        </tr>
    </tbody>

    <script>
    
        function MedicineWorkOrderBudget(){
            
            var BUDGET_CODE_ID=document.getElementById("BUDGET_CODE_ID").value;
            var totalPrice=document.getElementById("totalPrice").value;
        
            var dataStringList = 'BUDGET_CODE_ID='+BUDGET_CODE_ID+'&totalPrice='+totalPrice;
            $.ajax
            ({
            type: "POST",
            url: "ajax_Budget_Amount.php",
            data: dataStringList,
            cache: false,
            success: function(html)
            {
                $("#code_current_amount").html(html);
                $("#after_work_order_code_amount").html(html);   
                
            } 
            });

        }
</script>
    <tfoot>
        <tr>
            <td colspan="4" style="text-align: left;">
                <input type="button" class="btn btn-primary
                 btn-block " id="addrow" value="Add More" />
            </td>
        </tr>
		


       
        
    </tfoot>
</table>	
		
    
 </div>   
</div>



  </div>
				
				</div>	  
			  
			  
			  
			  
            <!-- /.row -->
          </div>
          <!-- /.card-body -->
          <div class="card-footer">
          <div class="box-tools pull-right">
       <button type="button" class="btn btn-warning" onclick="history.back(-1)"><i class="fa fa-window-close"></i> Cancel</button>&nbsp;&nbsp;&nbsp;
                    <button class="btn btn-primary" type="submit" name="Insert_Stock_Transfer"><i class="fa fa-save"></i> Save </button>
                </div>
          </div>
        </div>
        <!-- /.card -->
</form>
  <?php } ?> 
    
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
		
		cols += '<td><input type="hidden" name="number_count" class="form-control"  value="' + counter + '"/>  <input name="name' + counter + '" id="username_' + counter + '"     class="username form-control" placeholder="Product Name ..." style="width:100%;" required="required"><input type="hidden" id="product_id' + counter + '" name="product_id' + counter + '"  class="form-control" /></td>';
	
		
        cols += '<td><input type="text" class="form-control"  oninput="calculate' + counter + '()" id="availablequantity' + counter + '" placeholder="Available Quantity Here .." name="availablequantity' + counter + '" required readonly/></td>';
		cols += '<td><input class="form-control " placeholder="Transfer Quantity Here ...." oninput="calculate' + counter + '()" name="transfer_quantity' + counter + '" onkeyup="check_available' + counter + '()" id="transfer_quantity' + counter + '" type="text" required></td>';
		
      
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
         document.getElementById("TRANSFER_DATE").valueAsDate = new Date();
        </script>

	