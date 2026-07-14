<?php
$EditData = $pdo->query("select * FROM $PageStatusCheck WHERE id='$DocumentData'");
$rowEditData = $EditData->fetch();
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
  <form method="post" action="?<?php echo substr($page_title,0,-5)."/".$MenuName."/".$DocumentData."/".$PageStatusCheck; ?>" enctype="multipart/form-data">
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
            <label for="purchase_id">Purchase Order No<span style="color:#F00;">*</span></label>
            <input class="form-control" placeholder="Purchase Order NoHere ...." name="purchase_id" id="purchase_id" value="<?php echo $rowEditData["purchase_id"]; ?>" type="text" required >
          
        </div>
    </div>
	<style>
     .jodit_wysiwyg_iframe{
        display: none;
     }   
    </style>
	  <div class="col-sm-4">
        <div class="form-group"> 
         
         <label for="supplier_id">Supplier Information<span style="color:#F00;">*</span></label>
			
             <select name="supplier_id" id="supplier_id" class="form-control select2" data-quick-create="supplier" style="width:100%;" required >
            <option selected value="">Select Supplier Information</option>
            <?php
               
             $Supplier_info = $pdo->query("SELECT * FROM supplier_information where deleted_at is NULL");
                      $sl=1;
                            while($rowDataSupplier_info= $Supplier_info->fetch()){   
               
            
             ?>
            <option <?php if(!empty($rowEditData["supplier_id"]) && $rowEditData["supplier_id"]==$rowDataSupplier_info["id"]){ echo "selected"; } ?> value="<?php echo $rowDataSupplier_info["id"]; ?>"><?php echo $rowDataSupplier_info["organization"]; ?></option>
            <?php } ?>
            </select>

        </div>
    </div>
    <div class="col-sm-4">
        <div class="form-group">
        <label for="date">Date<span style="color:#F00;">*</span></label>
        <input class="form-control date" value="<?php echo $rowEditData["date"]; ?>" placeholder=" Date Here ...." name="date" id="date" type="date" required>
          
        </div>
    </div>
	<div class="col-sm-4">
        <div class="form-group">
            <label for="note">Note</label>
            <textarea class="form-control"  placeholder="Note Here ...." name="note" id="note" type="text" ><?php echo $rowEditData["note"]; ?></textarea>
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
	<div class="col-sm-4">
        <div class="form-group">
            <label for="photo">Attachment(Upload)</label>
             <input class="form-control" name="photo" id="img" onchange="validateImage()" type="file" >
          
        </div>
    </div>
				
	<div class="col-sm-4">
        <div class="form-group">
           
			<input class="form-control date" value="<?php echo $rowEditData["store_id"]; ?>" placeholder=" Store ID Here ...." name="store_id" id="store_id" type="hidden" required>
             
          
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
                            url: "ajax_Product_name.php",
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
  <p>Purchase History :<span style="color:#FF0000">*</span></p>
    <div style="overflow-x:auto;">
		
		<table id="myTable" class="table order-list app-line-item-table purchase-edit-table">
	    <thead>
	        <tr>
	           
				<th>SL</th>
				<th>Name</th>
	            <th>Quantity</th>
	            <th>Rate</th>
				<th>Amount</th>
        </tr>
    </thead>
    <tbody>
		
		<?php 
		$informationProduct_Detail = $pdo->query("SELECT purchase_detail.*,product_information.name AS product_name  FROM `purchase_detail` INNER JOIN product_information ON purchase_detail.product_id=product_information.id where  purchase_detail.invoice_id='".$rowEditData["invoice_id"]."' and purchase_detail.deleted_at is NULL");
			$serial=1;									
            while ($rowdataProduct_Detail = $informationProduct_Detail->fetch()){	
				?>
	        <tr>
	          
				   <td><?php echo $serial; ?></td>
				   <td><input type="hidden" name="number_count" class="form-control"  value="<?php echo $serial; ?>"/>
					   <input name="name<?php echo $serial; ?>" id="username_1" placeholder="Name Here"  class="username form-control" style="width:100%;" required="required" value="<?php echo $rowdataProduct_Detail["product_name"]; ?>" readonly>
	            </td>
		
            <td>
                <input type="text" id="quantity<?php echo $serial; ?>" name="quantity<?php echo $serial; ?>"  class="form-control" onkeyup="get_total_vaue();" oninput="calculate<?php echo $serial; ?>()" placeholder="Quantity Here .." required  value="<?php echo $rowdataProduct_Detail["quantity"]; ?>"/>
            </td>
			<td>
			<input class="form-control " placeholder="Rate Here ...." oninput="calculate<?php echo $serial; ?>()" onkeyup="get_total_vaue();" name="rate<?php echo $serial; ?>" id="rate<?php echo $serial; ?>" type="text" required  value="<?php echo $rowdataProduct_Detail["rate"]; ?>">
			</td>
            <td> <input class="form-control cm_cls" placeholder="Total Amount Here ...." id="total_amount<?php echo $serial; ?>"  value="<?php echo $rowdataProduct_Detail["amount"]; ?>" name="total_amount<?php echo $serial; ?>" type="text" required readonly></td>
           
            </td>
        </tr>
		<?php 
			$serial++;
			} ?>
		
    </tbody>

   
    
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
                    <button class="btn btn-primary" type="submit" name="Edit_Purchase_History"><i class="fa fa-save"></i> Save </button>
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
