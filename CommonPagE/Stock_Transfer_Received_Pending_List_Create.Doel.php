<script>
		<?php for($seript_serial=1;$seript_serial<=500;$seript_serial++){ ?>
		  
	     function check_available<?php echo $seript_serial; ?>() {
	    var receiveInput = document.getElementById('received_quantity<?php echo $seript_serial; ?>');
	    if(!receiveInput){
	      return;
	    }
	    var have_value = Number(receiveInput.max || 0);
	      var input_value = Number(receiveInput.value || 0);
	      if(input_value > have_value)
	      {
	        receiveInput.value="";
	      }
	      }
  

		<?php } ?>
		</script>

<?php
if(!empty($MenuName)){
	$Detail=$MenuName;

$Stock_transfer_information= $pdo->query("select * FROM stock_transfer_summary WHERE transfer_id='".$DocumentData."' ");
$rowdata = $Stock_transfer_information->fetch();
		
if(!empty($rowdata)){					
	


 ?>
<style type="text/css" media='print'>
@media print{
    .printable{
        font-size: 11px;
    }
    .logo{
        margin-bottom: 20pt;
    }
    .break-before{
        page-break-before: always !important;
    }
    .break-after{
        page-break-after: always !important;
    }
    .no-break{
        page-break-inside: avoid !important;
    }
    .with-border{
        border: none !important;
    }
    .box{
        border: none !important;
        box-shadow: none !important;
    }
    .table-data {
        overflow: visible !important;
    }
    #printPageButton {
    display: none;
  }

}

@media print
{    
    .no-print, .no-print *
    {
        display: none !important;
    }
}th{
		text-align:center;
		}
		.table-responsive{
			
			}
</style>

<style>
.div-design-print{
	background: white;
}
body{
	background: #fff;
}
	


@media all {
.page-break { display: none; }
}

@media print {
.page-break { display: block; page-break-before: always; }
}
</style>

<style>

@media all {
    .page-break { display: none; }
}

@media print {
    .page-break { display: block; page-break-before: always; }
}

.header-area{
	padding: 0px 10px;	
	color: #112233;
	text-align: center;
	border-bottom: 1px solid #5499C7;
	
}
.header-area .logo-image{
	height: 100px;
	width: 100px;
	float: left;
}
.header-area .student-image{
	height: 100px;
	width: 100px;
	float: right;
	background-image: url(../images/profile.png);
}
.header-area h1{
	margin: 0px;
	font-size: 28px;
}


 .my-info-area{
	-moz-box-shadow:    inset 0 0 10px  #9CCAF4;
	-webkit-box-shadow: inset 0 0 10px #9CCAF4;
	box-shadow:         inset 0 0 10px #9CCAF4; 
	border: 1px solid #5499C7;
	 padding: 20px;
 }
 
 
 
.details-div-area{
	padding: 15px 20px;
	//border-bottom: 0.5px solid #5499C7;
	font-style: italic;
}
.details-div-area tr{
	height: 30px;
}
.data-row{
	border-bottom: 1px dotted #5499C7;
}
.present-address{
	padding-left: 20px;
	vertical-align: top;
}
.verify-div .signature-1{
	border-top: 1px dotted #000;
	padding: 3px;	
	float: left;
}
.verify-div .signature-2{
	border-top: 1px dotted #000;
	padding: 3px;	
	float: right;
}	 



/*----------------------------Title Area--------------------------------------*/
.title-name {
	text-align: center;
	margin-left: auto;
	margin-right: auto;
	margin-top: 10px;
	border: 0.5px solid #5499C7;
	width: 300px;
	padding: 5px;
	//border-radius: 10px;	
	color: #112233;	
	font-size: 15px;
}
/*----------------------------Information Area--------------------------------------*/
.info-div-area{
	//border-bottom: 0.5px solid #5499C7;	
	font-style: italic;
	padding: 15px 18px;
}
.info-div-area table{
	color: #112233;
	font-size: 15px;
}

.info-div-area td{
	border: 0.5px solid #fff;
	padding: 5px 0px;
}
.info-div-area tr{
	background-color: #EAECEE;
	height: 25px;
}
.info-div-area tr:nth-child(even){
	background-color: #F7F7F7;
}
.table-div-area th{
	padding: 5px 0px;
	border: 0.5px solid #AED6F1;
	font-size: 13px;
	font-style: italic;
	text-align: center;
}


.table-div-area td{
	padding: 5px 0px;
	border: 0.5px solid #AED6F1;
	font-size: 14px;
	font-style: italic;
}


/*----------------------------Verify / Signature Area--------------------------------------*/
.verify-div{
	border-bottom: 0.5px solid #5499C7;
	padding: 100px 60px 20px 60px;
	font-size: 14px;
	font-style: italic;
}
.leave_info_table{
	text-align: center;
}
.leave_info_table th{
	font-weight: normal;
	background: #F7FCFF;
}
</style>

<section class="content">

      <!-- Default box -->
      <div class="card">
        <div class="card-header d-print-none">
          <h3 class="card-title"><?php echo str_replace("_"," ",$page_title); ?></h3>

          <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
              <i class="fas fa-minus"></i>
            </button>
            <button type="button" class="btn btn-tool" data-card-widget="remove" title="Remove">
              <i class="fas fa-times"></i>
            </button>
			  <div class="box-tools pull-right">
                
                        <button id="printpagebutton" class="btn btn-success btn-xl" style="margin-left:5px; color:#FFF;" onclick="window.print();return false;" />
                        <i class="fa fa-print"></i>
                       Print
                    </button>    
                                          
                </div>
          </div>
        </div>
        <div class="card-body p-0">
			
			
			
			
			
		<div class="container">
					    <div class="row my-info-area">
							<?php include("PrintTitle.php"); ?>
							<div class="col-xl-12 title-div">		
								<br><div class="title-name">Basic Information</div>						
							</div>
							
						<div class="col-md-12">
                 
				<div class="row">

				<?php
$From_Store_ID = $pdo->query("SELECT * FROM store_information WHERE ID='".$rowdata["from_store_id"]."'");
 $rowDataFrom_Store_ID= $From_Store_ID->fetch();


 $To_Store_ID = $pdo->query("SELECT * FROM store_information WHERE ID='".$rowdata["to_store_id"]."'");
 $rowDataTo_Store_ID= $To_Store_ID->fetch();
 ?>




  <div class="col-sm-4">
        <div class="form-group">
            <label for="FROM_STORE_ID">From Store</label>
          
          <p><?php echo $rowDataFrom_Store_ID["name"]; ?></p>
        </div>
    </div>

    <div class="col-sm-4">
        <div class="form-group">
            <label for="TO_STORE_ID">To Store</label>
            
          <p><?php echo $rowDataTo_Store_ID["name"]; ?></p>
        </div>
    </div>

    <div class="col-sm-4">
        <div class="form-group">
            <label for="TO_STORE_ID">Date</label>
           
          <p><?php echo date("d-m-Y", strtotime($rowdata["transfer_date"])); ; ?></p>
        </div>
    </div>

<?php if(!empty($rowdata["note"])){ ?>
    <div class="col-sm-12">
        <div class="form-group">
            <label for="TO_STORE_ID">Note</label>
           
          <p><?php echo nl2br($rowdata["note"]);  ?></p>
        </div>
    </div>

<?php } ?>
    	</div>
					
					
					
                   </div>
						
		
			
			<div class="col-sm-12">
								<div class="row">
								
								<form method="post" action="?<?php echo "Requestion/".$MenuName ?>" enctype="multipart/form-data" style="width:100%;">
	
	<input type="hidden" name="invoice_id"  placeholder="Name Here"  class=" form-control" style="width:100%;" required="required" value="<?php echo $Detail; ?>" ><input type="hidden" name="from_store_id"  placeholder="Name Here"  class=" form-control" style="width:100%;" required="required" value="<?php echo $rowdata["from_store_id"]; ?>" >
	<input type="hidden" name="to_store_id"  placeholder="Name Here"  class=" form-control" style="width:100%;" required="required" value="<?php echo $rowdata["to_store_id"]; ?>" >	
	<input type="hidden" name="transfer_id"  placeholder="Name Here"  class=" form-control" style="width:100%;" required="required" value="<?php echo $rowdata["transfer_id"]; ?>" >	
									
        <!-- SELECT2 EXAMPLE -->
        <div class="">
          <div class="row">
  <div class="col-sm-12">
	  <p>Transfer  Detail :<span style="color:#FF0000">*</span></p>
	    <div style="overflow-x:auto;">
			
		<table id="myTable" class="table order-list app-line-item-table stock-transfer-receive-table">
	    <thead>
	        <tr>
	           
				<th>SL</th>
				<th>Name</th>
				<th>Transfer Quantity</th>
				<th>Receive Quantity</th>
        </tr>
    </thead>
    <tbody>
		
		<?php 
		$informationProduct_Detail = $pdo->query("SELECT stock_transfer_information.*,product_information.name AS product_name  FROM `stock_transfer_information` INNER JOIN product_information ON stock_transfer_information.product_id=product_information.id where  stock_transfer_information.transfer_id='$MenuName' and stock_transfer_information.deleted_at is NULL");
			$serial=1;									
            while ($rowdataProduct_Detail = $informationProduct_Detail->fetch()){	
				?>
	        <tr>
	          
				   <td><?php echo $serial; ?></td>
				   <td><input type="hidden" name="number_count" class="form-control"  value="<?php echo $serial; ?>"/>
					   
					   <input name="edit_id<?php echo $serial; ?>"  type="hidden" placeholder="Name Here"  class="username form-control" style="width:100%;" required="required" value="<?php echo $rowdataProduct_Detail["id"]; ?>" readonly>
					   <?php echo $rowdataProduct_Detail["product_name"]; ?>
            </td>
		 <td><?php echo $rowdataProduct_Detail["quantity"]; ?>
			</td>
     
            <td> <input class="form-control " placeholder="Receive Quantity Here ...." id="received_quantity<?php echo $serial; ?>"   name="received_quantity<?php echo $serial; ?>" onkeyup='check_available<?php echo $serial; ?>()' min="1" max="<?php echo $rowdataProduct_Detail["quantity"]; ?>" value="<?php echo $rowdataProduct_Detail["quantity"]; ?>" type="text" required >
		 </td>
           
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
        
          <!-- /.card-body -->
          <div class="card-footer">
          <div class="box-tools pull-right">
       <button type="button" class="btn btn-warning" onclick="history.back(-1)"><i class="fa fa-window-close"></i> Cancel</button>&nbsp;&nbsp;&nbsp;
                    <button class="btn btn-primary" type="submit" name="Stock_Transfer_Received_Pending_List_Create"><i class="fa fa-save"></i> received </button>
                </div>
          </div>
        </div>
        <!-- /.card -->
</form>
								
								
								
								
								</div>			
							
							
							
							
							</div>
			
			
			
			
			
				
							
						</div>
						
						

				
					
			</div>	
			
			
		
			
          
        </div>
        <!-- /.card-body -->
      </div>
      <!-- /.card -->

    </section>

<?php } } ?>
