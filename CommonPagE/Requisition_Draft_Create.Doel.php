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
     .requisition-detail-table-wrap {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
     }
     .requisition-detail-table {
        width: 100%;
        min-width: 1220px;
     }
     .requisition-detail-table th,
     .requisition-detail-table td {
        white-space: nowrap;
        vertical-align: middle;
     }
     .requisition-detail-table th:nth-child(1),
     .requisition-detail-table td:nth-child(1) {
        min-width: 240px;
     }
     .requisition-detail-table th:nth-child(2),
     .requisition-detail-table td:nth-child(2) {
        min-width: 130px;
     }
     .requisition-detail-table th:nth-child(3),
     .requisition-detail-table td:nth-child(3) {
        min-width: 80px;
     }
     .requisition-detail-table th:nth-child(4),
     .requisition-detail-table td:nth-child(4),
     .requisition-detail-table th:nth-child(5),
     .requisition-detail-table td:nth-child(5),
     .requisition-detail-table th:nth-child(6),
     .requisition-detail-table td:nth-child(6) {
        min-width: 120px;
     }
     .requisition-detail-table th:nth-child(7),
     .requisition-detail-table td:nth-child(7) {
        min-width: 100px;
     }
     .requisition-detail-table th:nth-child(8),
     .requisition-detail-table td:nth-child(8) {
        min-width: 220px;
     }
     .requisition-detail-table th:nth-child(9),
     .requisition-detail-table td:nth-child(9) {
        min-width: 110px;
     }
     .requisition-detail-table .form-control {
        min-width: 100%;
     }
     @media screen and (max-width: 991.98px) {
        .requisition-detail-table {
            min-width: 1080px;
        }
     }
    </style>
				<div class="col-sm-3">
        <div class="form-group">
            <label for="photo">Store Name<span style="color:#F00;">*</span></label>
			
			<?php if($_SESSION['USER_TYPE']=='Admin'){ ?>
             <select class="select2" id="store_id" name="store_id" data-placeholder="Select Store" style="width: 100%;" required>
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
  <?php /* ?>  
	  <div class="col-sm-4">
        <div class="form-group"> 
          <input class="form-control"  name="requistion_type" id="requistion_type" type="hidden" value="Material" required>
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
    <?php */ ?>
    	  <div class="col-sm-3">
        <div class="form-group"> 
          <input class="form-control"  name="requistion_type" id="requistion_type" type="hidden" value="Material" required>
         <label for="supplier_id">Project Information<span style="color:#F00;">*</span></label>
		
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
         
        </div>
    </div>
    
    
    <div class="col-sm-3">
        <div class="form-group">
        <label for="date">Date<span style="color:#F00;">*</span></label>
        <input class="form-control date" placeholder=" Date Here ...." name="date" id="date" type="date" required>
          
        </div>
    </div>
	<script>
 function ImagefileValidation2(){
 var fileInput =document.getElementById('ImagefileValidationData');
 var filePath = fileInput.value;
 // Allowing file type
//var allowedExtensions =/(\.doc|\.docx|\.odt|\.pdf|\.tex|\.txt|\.rtf|\.wps|\.wks|\.wpd)$/i;	 
 var allowedExtensions = 
/(\.pdf|\.PDF)$/i;
 if (!allowedExtensions.exec(filePath)) {
    alert('Please upload valid Image File');
    fileInput.value = '';
      return false;
            } 
        }
    </script>
	<div class="col-md-3">
				  <div class="form-group">
					<label for="PHOTO">Multiple Photo (Upload)</label>
					<input type="file" class="form-control form-control-file" name="multiple_photo[]" multiple  id="ImagefileValidationData" onchange="return ImagefileValidation2()">
				  </div>
                <!-- /.form-group -->
              </div>			
	<div class="col-sm-12">
        <div class="form-group">
            <label for="note">Note<span style="color:#F00;">*</span></label>
            <textarea class="form-control"  placeholder="Detail Here ...." name="note" id="note" type="text" required></textarea>
        </div>
    </div>
	<div class="col-sm-12"><div id="emergency-reconciliation-note" class="alert" style="display:none" aria-live="polite"></div></div>
	<style>
	.order-list .emergency-linked-row{background:#fffaf0}
	.order-list .emergency-linked-row td:first-child{border-left:3px solid #f59e0b}
	.emergency-required-badge{display:inline-flex;align-items:center;gap:5px;margin-top:5px;color:#92400e;font-size:11px;font-weight:700}
	</style>
				
	
				
				


	
    
    </div>    <link rel="stylesheet" href="plugins/jquery-ui/jquery-ui.min.css">
    <script src="plugins/jquery-ui/jquery-ui.min.js"></script>		  
	<script type="text/javascript">
        $(document).ready(function(){
            function getSelectedStoreId() {
                return $('#store_id').val() || $('select[name="store_id"]').val() || <?php echo (int)$login_user_store_id; ?>;
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
            window.requisitionDraftGetSelectedStoreId = getSelectedStoreId;
            window.requisitionDraftLookupProductByName = lookupProductByName;
            window.requisitionDraftLookupProductByCode = lookupProductByCode;

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
                                search: request.term,request:1,store_id:window.requisitionDraftGetSelectedStoreId()
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
                                search: request.term,request:1,store_id:window.requisitionDraftGetSelectedStoreId()
                            },
                            success: function( data ) {
                                response( data );
                            }
                        });
                    },
                    select: function (event, ui) {
                        $(this).val(ui.item.label); // display the selected text
                        var userid = ui.item.value; // selected id to input

                        window.requisitionDraftLookupProductByCode(index, userid);

                        return false;
                    }
                });
            });
            $(document).on('blur', '.product_code', function() {
                var index = this.id.split('_')[1];
                if (!$('#product_id' + index).val()) window.requisitionDraftLookupProductByCode(index, $(this).val());
            });
            
            // Add more
         
        });

    </script> 
    
    
		<div class="row">
  <div class="col-sm-12">
  <p>Requestion Detail :<span style="color:#FF0000">*</span></p>
    <div class="requisition-detail-table-wrap">
		
	<table id="myTable" class="table order-list requisition-detail-table">
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
        <tr>
          
			   <td style="width: 30%;">
				   <input name="name1" id="username_1" type="text" placeholder="Name Here" class="username form-control" data-quick-create-product="1" style="width:100%;" required="required">
				    <input name="product_id1" id="product_id1" type="hidden" placeholder="Name Here"  class="form-control" style="width:100%;">
            </td>
              <td style="width: 10%;">
				   <input name="product_code1" type="text" id="productcode_1" placeholder="Code Here"  class="product_code form-control" style="width:100%;" >
            </td>
            <td>
    <input type="text" id="unit_1" class="form-control" placeholder="Unit" readonly/>
</td>
		   <td>
                <input type="text" id="available_quantity1" name="available_quantity1"  class="form-control"  placeholder="Quantity Here .." readonly/>
            </td>
			<td><input type="hidden" id="emergency_detail_ids1" name="emergency_detail_ids1"><input type="number" id="emergency_quantity1" name="emergency_quantity1" class="form-control emergency-quantity" value="0" readonly></td>
            <td>
                <input type="number" min="0" step="0.0001" id="quantity1" name="quantity1"  class="form-control total-required" onkeyup="get_total_vaue();" oninput="calculate1()" placeholder="Total quantity"/>
            </td>
             <td>
                <input type="text" id="requistion_rate1" name="requistion_rate1"  class="form-control"  placeholder="Rate Here .." />
            </td>
            <td style="width: 20%;">
				   <input name="comment1" type="text" id="comment" placeholder="Comments Here"  class=" form-control" style="width:100%;" >
            </td>
		
            <td ><a class="deleteRow ibtnDel btn btn-md btn-danger">Delete</a>

            </td>
        </tr>
    </tbody>

   
    <tfoot>
        <tr>
            <td colspan="9" style="text-align: left;">
                <input type="button" class="btn btn-primary btn-block " id="addrow" value="Add More" />
            </td>
        </tr>
		    
	

        
    </tfoot>
</table>
	<input type="hidden" name="number_count" id="number_count" value="1">
		
    
 </div>   
</div>

 







  </div>	  
			  
			  
			  
			  
            <!-- /.row -->
          </div>
          <!-- /.card-body -->
          <div class="card-footer">
          <div class="box-tools pull-right">
       <button type="button" class="btn btn-warning" onclick="history.back(-1)"><i class="fa fa-window-close"></i> Cancel</button>&nbsp;&nbsp;&nbsp;
        <button class="btn btn-primary" type="submit" name="Insert_Requestion_History_Draft"><i class="fa fa-save"></i> Save </button>
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
    window.requisitionRowCounter = 2;

    $("#addrow").on("click", function () {
        var newRow = $("<tr>");
        var cols = "";
		
		var counter=window.requisitionRowCounter;
		cols += '<td><input name="name' + counter + '" id="username_' + counter + '" class="username form-control" data-quick-create-product="1" placeholder="Name Here" style="width:100%;" required="required"><input name="product_id' + counter + '" id="product_id' + counter + '" type="hidden" placeholder="Name Here"  class="form-control" style="width:100%;"></td>';
		
		 cols += '<td> <input name="product_code' + counter + '" type="text" id="productcode_' + counter + '" placeholder="Code Here"  class="product_code form-control" style="width:100%;" ></td>';
		 cols += '<td><input type="text" id="unit_' + counter + '" class="form-control" placeholder="Unit" readonly/></td>';
		 
		  cols += '<td><input type="text" id="available_quantity' + counter + '" name="available_quantity' + counter + '"  class="form-control"  placeholder="Quantity Here .." readonly/></td>';
		  cols += '<td><input type="hidden" id="emergency_detail_ids' + counter + '" name="emergency_detail_ids' + counter + '"><input type="number" id="emergency_quantity' + counter + '" name="emergency_quantity' + counter + '" class="form-control emergency-quantity" value="0" readonly></td>';
        cols += '<td><input type="number" min="0" step="0.0001" class="form-control total-required" onkeyup="get_total_vaue();" oninput="calculate' + counter + '()" id="quantity' + counter + '" placeholder="Total quantity" name="quantity' + counter + '"/></td>';
        
        cols += '<td><input type="text" class="form-control"  id="requistion_rate' + counter + '" placeholder="Rate Here .." name="requistion_rate' + counter + '" /></td>';
        
        cols += '<td> <input name="comment' + counter + '" type="text" id="comment' + counter + '" placeholder="Comment Here"  class=" form-control" style="width:100%;" ></td>';	
		
      
        cols += '<td><input type="button" class="ibtnDel btn btn-md btn-danger "  value="Delete"></td>';
        newRow.append(cols);
        $("table.order-list").append(newRow);
		window.requisitionRowCounter++;
		$('#number_count').val(counter);
		$(".product_category").select2({ allowClear:true, placeholder: "Select Category" });
		$(".model").select2({  allowClear:true,  placeholder: "Select Name"  });
    });



    $("table.order-list").on("click", ".ibtnDel", function (event) {
        $(this).closest("tr").remove();
    });
});

(function($){
  var lastEmergencyContext='';
	  function resetRows(){
	    var first=$('table.order-list tbody tr').first();
	    $('table.order-list tbody tr').not(first).remove();
	    first.removeClass('emergency-linked-row');
	    first.find('input').val('');
	    first.find('.emergency-quantity').val('0');
	    first.find('.username').prop('readonly',false);
	    first.find('.ibtnDel').removeClass('disabled').attr('aria-disabled','false').html('Delete');
	    first.find('.emergency-required-badge').remove();
	    $('#number_count').val('1');
    window.requisitionRowCounter=2;
  }
  function fillRow(row,index,item){
    row.find('[name="name'+index+'"]').val(item.name).prop('readonly',true);
    row.find('[name="product_id'+index+'"]').val(item.product_id);
    row.find('[name="product_code'+index+'"]').val(item.code);
    row.find('#unit_'+index).val(item.unit);
    row.find('[name="available_quantity'+index+'"]').val(item.available_quantity || 0);
    row.find('[name="emergency_detail_ids'+index+'"]').val(item.emergency_detail_ids);
    row.find('[name="emergency_quantity'+index+'"]').val(item.emergency_quantity);
	    row.find('[name="quantity'+index+'"]').val(item.emergency_quantity).attr('min',item.emergency_quantity);
	    row.find('[name="comment'+index+'"]').val('Emergency issue reconciliation');
	    row.addClass('emergency-linked-row');
	    row.find('.ibtnDel').addClass('disabled').attr('aria-disabled','true').html('<i class="fas fa-lock"></i> Required');
	    row.find('td:first').append('<span class="emergency-required-badge"><i class="fas fa-bolt"></i> Required for reconciliation</span>');
  }
  function loadEmergencyItems(){
    var project=$('#project_id').val(),store=$('#store_id').val()||$('select[name="store_id"]').val();
    if(!project||!store){$('#emergency-reconciliation-note').hide();return;}
    $('#emergency-reconciliation-note').removeClass('alert-success alert-warning alert-info alert-danger').addClass('alert-info').html('<i class="fas fa-spinner fa-spin"></i> Checking emergency request history for this project and store...').show();
    $.getJSON('ajax_Emergency_Request.php',{action:'outstanding',project_id:project,store_id:store}).done(function(data){
      resetRows();
	      var requests=data.requests||[],reservedDrafts=data.reserved_drafts||[],items=data.items||[];
      var links=requests.map(function(request){return '<a href="?Emergency_Request_Detail/'+request.id+'" target="_blank">'+$('<div>').text(request.request_no).html()+'</a>';}).join(', ');
      if(!requests.length){
        $('#emergency-reconciliation-note').removeClass('alert-info').addClass('alert-success').html('<i class="fas fa-check-circle"></i> <strong>No emergency request found.</strong> This project and store have no emergency issue history.');
        return;
      }
      if(!items.length){
        var waiting=requests.some(function(request){return request.status==='receiver_pending'||request.status==='reference_pending';});
	        var reservedLinks=reservedDrafts.map(function(draft){return '<a href="?Requestion_Draft_History_Detail/'+draft.invoice_id+'" target="_blank">Draft '+draft.invoice_id+'</a>';}).join(', ');
	        var message=reservedDrafts.length?'Emergency products are already attached to '+reservedLinks+'. They will not be added to another draft.':(waiting?'Acknowledgements are still pending, so products cannot be finalized in this draft yet.':'All emergency quantities are already finalized in a requisition.');
	        $('#emergency-reconciliation-note').removeClass('alert-info').addClass(waiting||reservedDrafts.length?'alert-warning':'alert-success').html('<i class="fas '+(waiting?'fa-clock':(reservedDrafts.length?'fa-lock':'fa-check-circle'))+'"></i> <strong>Emergency history found:</strong> '+links+'. '+message);
        return;
      }
      data.items.forEach(function(item,i){if(i>0)$('#addrow').trigger('click');var row=$('table.order-list tbody tr').eq(i);fillRow(row,i+1,item);});
      var total=items.reduce(function(sum,item){return sum+Number(item.emergency_quantity||0);},0);
      $('#emergency-reconciliation-note').removeClass('alert-info').addClass('alert-warning').html('<i class="fas fa-bolt"></i> <strong>Outstanding emergency products added automatically.</strong> '+items.length+' product(s), total quantity '+total.toLocaleString()+'. Requests: '+links+'. Total Required may be increased, but cannot be lower than Emergency Issued.');
    }).fail(function(){ $('#emergency-reconciliation-note').removeClass('alert-info').addClass('alert-danger').html('<i class="fas fa-exclamation-circle"></i> Emergency history could not be checked. Please reselect Project or Store.'); });
  }
  window.loadEmergencyItems=loadEmergencyItems;
  $('#project_id,#store_id,select[name="store_id"]').on('change',loadEmergencyItems);
  $('#project_id,#store_id').on('select2:select',loadEmergencyItems);
  window.setTimeout(loadEmergencyItems,250);
  window.setInterval(function(){var context=String($('#project_id').val()||'')+'|'+String($('#store_id').val()||$('select[name="store_id"]').val()||'');if(context!=='|'&&context!==lastEmergencyContext){lastEmergencyContext=context;loadEmergencyItems();}},500);
	  $(document).on('input','.total-required',function(){var row=$(this).closest('tr'),minimum=Number(row.find('.emergency-quantity').val());if(Number(this.value)<minimum)this.setCustomValidity('Total Required cannot be less than Emergency Issued quantity.');else this.setCustomValidity('');});
	  $(document).on('click','.emergency-linked-row .ibtnDel',function(event){event.preventDefault();return false;});
})(jQuery);



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

