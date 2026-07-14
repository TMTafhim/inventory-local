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
  <form method="post" action="?<?php echo substr($page_title,0,-7); ?>/<?php echo $MenuName; ?>/requestion_histiory" enctype="multipart/form-data">
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
			
    <div class="col-sm-12">
        <div class="form-group">
        <label for="date">Date<span style="color:#F00;">*</span></label>
        <input class="form-control date" placeholder=" Date Here ...." name="date" id="date" type="date" required>
          
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
                            url: "ajax_Asset_Product_name.php",
                            type: 'post',
                            dataType: "json",
                            data: {
                                search: request.term,request:1
                            },
                            success: function( data ) {
                                response( data );
                            }
                        });
                    },
                  
                });
            });
            
            // Add more
         
        });

    </script>	
   <script type="text/javascript">
        $(document).ready(function(){

            $(document).on('keydown', '.usingpersonname', function() {
                
                var id = this.id;
                var splitid = id.split('_');
                var index = splitid[1];

                $( '#'+id ).autocomplete({
                    source: function( request, response ) {
                        $.ajax({
                            url: "ajax_Requestion_Person_Information.php",
                            type: 'post',
                            dataType: "json",
                            data: {
                                search: request.term,request:1
                            },
                            success: function( data ) {
                                response( data );
                            }
                        });
                    },
                   
                });
            });
            
            // Add more
         
        });

    </script> 
    
    
		<div class="row">
  <div class="col-sm-12">
  <p>Requisition Detail :<span style="color:#FF0000">*</span></p>
    <div style="overflow-x:auto;">
		
	<table id="myTable" class="table order-list">
    <thead>
        <tr>
           
			<th>Product Name</th>
			<th>Using Information</th>
			<th>Description</th>
            <th>Quantity</th>
            <th>&nbsp;</th>
        </tr>
    </thead>
    <tbody>
        <tr>
        <td style="width: 35%;">
            <input type="hidden" name="number_count" class="form-control"  value="1"/>
			<input name="name1" id="username_1" type="text" placeholder="Product Name Here"  class="username form-control" style="width:100%;" required="required">  
            </td>
            <td style="width: 20%;">
		     <input name="using_person_name1" type="text" id="usingpersonname_1" placeholder="Using Information Here"  class="usingpersonname form-control" style="width:100%;" required="required">
            </td>
		   <td>
            <input type="text" id="description1" name="description1"  class="form-control"  placeholder="Description Here .." />
            </td>
            <td>
            <input type="text" id="quantity1" name="quantity1"  class="form-control"  placeholder="Quantity Here .." required/>
            </td>
		
            <td ><a class="deleteRow ibtnDel btn btn-md btn-danger">Delete</a></td>
        </tr>
    </tbody>

   
    <tfoot>
        <tr>
            <td colspan="5" style="text-align: left;">
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
        <button class="btn btn-primary" type="submit" name="Insert_asset_information_Create"><i class="fa fa-save"></i> Submit </button>
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
		
		cols += '<td><input type="hidden" name="number_count" class="form-control"  value="' + counter + '"/>  <input name="name' + counter + '" id="username_' + counter + '"     class="username form-control" placeholder="Product Name Here" style="width:100%;" required="required"></td>';
		
		 cols += '<td> <input name="using_person_name' + counter + '" type="text" id="usingpersonname_' + counter + '" placeholder="Using Information Here"  class="usingpersonname form-control" style="width:100%;" required="required"></td>';
		 
		  cols += '<td><input type="text" id="description' + counter + '" name="description' + counter + '"  class="form-control"  placeholder="Description Here .." /></td>';
	
		
        cols += '<td><input type="text" class="form-control"  id="quantity' + counter + '" placeholder="Quantity Here .." name="quantity' + counter + '" required/></td>';
		
      
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

	