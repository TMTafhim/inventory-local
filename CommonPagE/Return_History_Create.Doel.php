<script>
	<?php for($seript_serial=1;$seript_serial<=500;$seript_serial++){ ?>
	  
		
		
	
       function calculate<?php echo $seript_serial; ?>() {
       var return_quantity = 0+document.getElementById('return_quantity<?php echo $seript_serial; ?>').value;
       var used_quantity = 0-document.getElementById('used_quantity<?php echo $seript_serial; ?>').value;
       var damage_quantity = 0+document.getElementById('damage_quantity<?php echo $seript_serial; ?>').value;

       var total_quantity<?php echo $seript_serial; ?> = document.getElementById('total_quantity<?php echo $seript_serial; ?>');
       var i = Math.round(Number(return_quantity)+Number(damage_quantity)) ;

       total_quantity<?php echo $seript_serial; ?>.value = i;


       }
		<?php } ?>
		</script>
<section class="content">
      <div class="container-fluid">
        <!-- SELECT2 EXAMPLE -->
     <?php if(!empty($_POST["store_id"]) && !empty($_POST["project_id"])){
  
  $Store_information = $pdo->query("SELECT * FROM store_information where id='".$_POST["store_id"]."'");
  $rowDataStore_informationy= $Store_information->fetch();
  
  $Project_information = $pdo->query("SELECT * FROM project_information where id='".$_POST["project_id"]."'");
  $rowDataProject_information= $Project_information->fetch();
  
  
  ?>      
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
				<div class="col-sm-3">
        <div class="form-group">
            <label for="photo">Store Name<span style="color:#F00;">*</span></label>
			 
                   
			<p><?php echo $rowDataStore_informationy["name"]; ?></p>
			<input class="form-control " placeholder=" Date Here ...." name="store_id" id="store_id" type="hidden" value="<?php echo $rowDataStore_informationy["id"];  ?>" required>
			
        </div>
    </div>
	  <div class="col-sm-3">
        <div class="form-group"> 
         
         <label for="supplier_id">Project Information<span style="color:#F00;">*</span></label>
		<?php
	        echo "<p>".$rowDataProject_information["name"]."</p>";
			?>
			<input class="form-control " placeholder="Project Name Here ...." name="project_id" id="project_id" type="hidden" required value="<?php echo $rowDataProject_information["id"]; ?>">
		
        </div>
    </div>
    <div class="col-sm-3">
        <div class="form-group">
        <label for="date">Date<span style="color:#F00;">*</span></label>
        <input class="form-control date" placeholder=" Date Here ...." name="date" id="date" type="date" required>
          
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
	<div class="col-sm-3">
        <div class="form-group">
            <label for="photo">Attachment(Upload)</label>
             <input class="form-control" name="photo" id="img" onchange="validateImage()" type="file" >
          
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
                            url: "ajax_Return_Product_name.php",
                            type: 'post',
                            dataType: "json",
                            data: {
                                search: request.term,request:1,project_id:<?php echo $_POST["project_id"]; ?>
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
                            url: 'ajax_Return_Product_name.php',
                            type: 'post',
                            data: {userid:userid,request:3,project_id:<?php echo $_POST["project_id"]; ?>},
                            dataType: 'json',
                            success:function(response){
                                
                                var len = response.length;

                                if(len > 0){
                                
                                  var distribution_quantity = response[0]['distribution_quantity'];
								  var product_id = response[0]['product_id'];	

									document.getElementById('distribution_quantity'+index).value = distribution_quantity;
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
		<div class="row">
  <div class="col-sm-12">
  <p>Return Detail :<span style="color:#FF0000">*</span></p>
    <div style="overflow-x:auto;">
		
	<table id="myTable" class="table order-list">
    <thead>
        <tr>
           
			<th>Name</th>
			<th>Distributed Quantity</th>
            <th>Returned Quantity</th>
            <th>Used Quantity</th>
            <th>Damaged Quantity</th>
            <th>&nbsp;</th>
        </tr>
    </thead>
    <tbody>
        <tr>
          
			   <td style="width: 35%;"><input type="hidden" name="number_count" class="form-control"  value="1"/>
				   <input name="name1" id="username_1" placeholder="Name Here"  class="username form-control" style="width:100%;" required="required">
            </td>
            <td>
                <input name="product_id1" id="product_id1" placeholder="Name Here"  class=" form-control" style="width:100%;" required="required" type="hidden">
                <input type="text" id="distribution_quantity1" name="distribution_quantity1"  class="form-control"  placeholder="Quantity Here .." required readonly/>
            </td>
		
		
            <td>
                <input type="text" id="return_quantity1" name="return_quantity1"  class="form-control" oninput="calculate1()"  placeholder="Return Quantity Here .." required/>
            </td>
            <td>
                <input type="text" id="used_quantity1" name="used_quantity1"  class="form-control" oninput="calculate1()"  placeholder="Used Quantity Here .." required/>
            </td>
		<td>
                <input type="text" id="damage_quantity1" name="damage_quantity1"  class="form-control" oninput="calculate1()"  placeholder="Damage  Quantity Here .." required/>
            </td>
             <td> <input class="form-control cm_cls" placeholder="Total Amount Here ...." id="total_quantity1" name="total_quantity1" type="text" required readonly></td>
            <td ><a class="deleteRow ibtnDel btn btn-md btn-danger">Delete</a>

            </td>
        </tr>
    </tbody>

   
    <tfoot>
        <tr>
            <td colspan="7" style="text-align: left;">
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
        <button class="btn btn-primary" type="submit" name="Insert_Return_History_Create"><i class="fa fa-save"></i>  Return Submit </button>
                </div>
          </div>
        </div>
        <!-- /.card -->
</form>
 <?php }else{ ?> 
 <form method="post" action="?<?php echo $page_title; ?>/<?php echo $MenuName; ?>" enctype="multipart/form-data">
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

	<div class="col-sm-4">
        <div class="form-group">
            <label for="photo">Store Name<span style="color:#F00;">*</span></label>
			
			<?php if($_SESSION['USER_TYPE']=='Admin'){ ?>
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
						 ?>	 
                   
			<p><?php echo $rowDataInformationStore["name"]; ?></p>
			<input class="form-control " placeholder=" Date Here ...." name="store_id" id="store_id" type="hidden" value="<?php echo $row_Login_Datauser_information["store_id"];  ?>" required>
			<?php } ?>
        </div>
    </div>
	  <div class="col-sm-4">
        <div class="form-group"> 
         
         <label for="supplier_id">Project Information<span style="color:#F00;">*</span></label>
			
             <select name="project_id" id="project_id" data-placeholder="Select Project" class="form-control select2" data-quick-create="project" style="width:100%;" required onchange="this.form.submit()">
            <option selected value="">Select Project Information</option>
            <?php
               
             $Project_info = $pdo->query("SELECT * FROM project_information where DELETED_AT is NULL");
                      $sl=1;
                            while($rowDataProject_info= $Project_info->fetch()){   
               
            
             ?>
            <option value="<?php echo $rowDataProject_info["id"]; ?>"><?php echo $rowDataProject_info["name"]; ?></option>
            <?php } ?>
            </select>
         
        </div>
    </div>
  

     
   
    
    </div>
          
    
              
              
              
              
            <!-- /.row -->
          </div>
          <!-- /.card-body -->
        
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
		
		cols += '<td><input type="hidden" name="number_count" class="form-control"  value="' + counter + '"/>  <input name="name' + counter + '" id="username_' + counter + '"     class="username form-control" placeholder="Name Here" style="width:100%;" required="required"></td>';
	cols += '<td><input name="product_id' + counter + '" id="product_id' + counter + '" placeholder="Name Here"  class=" form-control" style="width:100%;" required="required" type="hidden"><input type="text" id="distribution_quantity' + counter + '" name="distribution_quantity' + counter + '"  class="form-control"  placeholder="Quantity Here .." required readonly/></td>';

       cols += '<td><input type="text" id="return_quantity' + counter + '" name="return_quantity' + counter + '"  class="form-control"  placeholder="Return Quantity Here .." oninput="calculate' + counter + '()" required/></td>';
cols += '<td><input type="text" id="used_quantity' + counter + '" name="used_quantity' + counter + '"  class="form-control" oninput="calculate' + counter + '()"  placeholder="Used Quantity Here .." required/></td>';
        cols += '<td><input type="text" id="damage_quantity' + counter + '" name="damage_quantity' + counter + '" oninput="calculate' + counter + '()" class="form-control"  placeholder="Damage  Quantity Here .." required/></td>';
        cols += '<td><input class="form-control cm_cls" placeholder="Total Amount Here ...." id="total_quantity' + counter + '" name="total_quantity' + counter + '" type="text" required readonly></td>';
      
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


