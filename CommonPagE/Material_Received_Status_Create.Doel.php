<script>
	<?php for($seript_serial=1;$seript_serial<=500;$seript_serial++){ ?>
	  
     function check_available<?php echo $seript_serial; ?>() {
    var have_value = 0+document.getElementById('due_quantity<?php echo $seript_serial; ?>').value;
      var input_value = 0+document.getElementById('received_quantity<?php echo $seript_serial; ?>').value;
      if(parseInt(input_value) > parseInt(have_value))
      {
        document.getElementById('received_quantity<?php echo $seript_serial; ?>').value="";
      }
      }
  

		<?php } ?>
		</script>

<?php
if(!empty($MenuName)){
	$Detail=$MenuName;

$information = $pdo->query("SELECT employee_information.name_en AS name,project_information.name AS project_name,date,invoice_id,project_id,distribution_summary.store_id AS store_id,distribution_id FROM `distribution_summary` INNER JOIN employee_information ON distribution_summary.employee_id=employee_information.id INNER JOIN project_information ON distribution_summary.project_id=project_information.id where   distribution_summary.invoice_id='$MenuName' and distribution_summary.distribution_id='$DocumentData' and distribution_summary.deleted_at is NULL");
$rowdata = $information->fetch();
		
if(!empty($rowdata)){					
	
$Distribution_Type_information = $pdo->query("SELECT requistion_type  FROM `requestion_histiory` where invoice_id='".$rowdata["invoice_id"]."' and deleted_at is NULL");
$rowdataDistribution_Type_information = $Distribution_Type_information->fetch();

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
.received-status-table {
    table-layout: fixed;
    min-width: 760px;
}
.received-status-table th,
.received-status-table td {
    vertical-align: middle !important;
}
.received-status-table th {
    white-space: normal !important;
    line-height: 1.25;
    text-align: center;
}
.received-status-table .sl-column {
    width: 56px;
    text-align: center;
}
.received-status-table .name-column {
    width: 48%;
    white-space: normal;
    overflow-wrap: anywhere;
}
.received-status-table .qty-column {
    width: 16%;
    text-align: center;
}
.received-status-table .receive-column {
    width: 140px;
    text-align: center;
}
.received-status-table .receive-column .form-control {
    max-width: 110px;
    margin: 0 auto;
    text-align: center;
}

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
							
							<div class="col-xl-12 details-div-area">		
								<table width="100%">
                                
                              								
									<tbody>
									<tr>
										<td width="15%">Project Name</td>
										<td width="85%" class="data-row" colspan="3">: &nbsp;<?php echo $rowdata["project_name"]; ?></td>
										
									</tr>	
										
										
										<tr>
										<td width="15%">Invoice No</td>
										<td width="35%" class="data-row">: &nbsp;<?php echo $rowdata["invoice_id"]; ?></td>
										<td width="18%">Requestion Information</td>
										<td width="32%" class="data-row">: &nbsp;<?php echo $rowdata["name"]; ; ?></td>
									</tr>
                                   
                                    
                                    <tr>
										<td width="15%"> Date</td>
										<td width="35%" class="data-row">: &nbsp;<?php echo date("d-m-Y", strtotime($current_date)); ?></td>
										<td width="18%"></td>
										<td width="32%" class="data-row"></td>
									</tr>
                                  
								</tbody></table>
								
							
                                
                              
							</div>	
							
						
		
			
			<div class="col-sm-12">
								<div class="row">
								
								<form method="post" action="?<?php echo "Requestion/".$MenuName ?>" enctype="multipart/form-data" style="width:100%;">
	
	<input type="hidden" name="invoice_id"  placeholder="Name Here"  class=" form-control" style="width:100%;" required="required" value="<?php echo $Detail; ?>" ><input type="hidden" name="project_id"  placeholder="Name Here"  class=" form-control" style="width:100%;" required="required" value="<?php echo $rowdata["project_id"]; ?>" >
	<input type="hidden" name="store_id"  placeholder="Name Here"  class=" form-control" style="width:100%;" required="required" value="<?php echo $rowdata["store_id"]; ?>" >	
	<input type="hidden" name="distribution_id"  placeholder="Name Here"  class=" form-control" style="width:100%;" required="required" value="<?php echo $rowdata["distribution_id"]; ?>" >	
									
        <!-- SELECT2 EXAMPLE -->
        <div class="">
          <div class="row">
  <div class="col-sm-12">
  <p>Distribution  Detail :<span style="color:#FF0000">*</span></p>
    <div style="overflow-x:auto;">
	<?php if(!empty($rowdataDistribution_Type_information["requistion_type"]) && ($rowdataDistribution_Type_information["requistion_type"])=='Fund'){ ?>
		<table id="myTable" class="table order-list received-status-table">
        <colgroup>
            <col class="sl-column">
            <col class="name-column">
            <col class="qty-column">
            <col class="qty-column">
            <col class="receive-column">
        </colgroup>
	    <thead>
	        <tr>
			<th class="sl-column">SL</th>
			<th class="name-column">Name</th>
			<th class="qty-column">Approval Amount</th>
            <th class="qty-column">Distribution Amount</th>
			<th class="receive-column">Receive Amount</th>
	        </tr>
	    </thead>
    <tbody>
		
		<?php 
		$informationProduct_Detail = $pdo->query("SELECT distribution_history.*,product_information.name AS product_name  FROM `distribution_history` INNER JOIN product_information ON distribution_history.product_id=product_information.id where  distribution_history.invoice_id='$MenuName' and distribution_history.distribution_id='$DocumentData' and distribution_history.deleted_at is NULL");
			$serial=1;									
            while ($rowdataProduct_Detail = $informationProduct_Detail->fetch()){	
				?>
        <tr>
          
			   <td class="sl-column"><?php echo $serial; ?><input type="hidden" name="number_count" class="form-control"  value="<?php echo $serial; ?>"/></td>
			   <td class="name-column">
			   
			<input name="product_id<?php echo $serial; ?>"  type="hidden" placeholder="Name Here"  class="form-control" style="width:100%;" required="required" value="<?php echo $rowdataProduct_Detail["product_id"]; ?>" >   
			   
				   
					   <input name="edit_id<?php echo $serial; ?>"  type="hidden" placeholder="Name Here"  class="username form-control" style="width:100%;" required="required" value="<?php echo $rowdataProduct_Detail["id"]; ?>" readonly>
					   <?php echo $rowdataProduct_Detail["product_name"]; if(!empty($rowdataProduct_Detail["detail"])){ echo " ( ".$rowdataProduct_Detail["detail"]." ) "; } ?>
            </td>
		 <td class="qty-column"><?php echo $rowdataProduct_Detail["requestion_amount"]; ?>
			</td>
         <td class="qty-column"><?php echo $rowdataProduct_Detail["distribution_amount"]; ?>
			</td>
			
            <td class="receive-column"> <input class="form-control " placeholder="Receive Quantity Here ...." id="received_amount<?php echo $serial; ?>"   name="received_amount<?php echo $serial; ?>" onkeyup='check_available<?php echo $serial; ?>()' min="1" max="<?php echo $rowdataProduct_Detail["distribution_amount"]; ?>" value="<?php echo $rowdataProduct_Detail["distribution_amount"]; ?>" type="text" required >
		 </td>
        </tr>
		<?php 
			$serial++;
			} ?>
		
    </tbody>

   
    
</table>
	
	
	<?php }else{ ?>
		<table id="myTable" class="table order-list received-status-table">
        <colgroup>
            <col class="sl-column">
            <col class="name-column">
            <col class="qty-column">
            <col class="qty-column">
            <col class="receive-column">
        </colgroup>
	    <thead>
	        <tr>
			<th class="sl-column">SL</th>
			<th class="name-column">Name</th>
			<th class="qty-column">Approval Quantity</th>
            <th class="qty-column">Distribution Quantity</th>
			<th class="receive-column">Receive Quantity</th>
	        </tr>
	    </thead>
    <tbody>
		
		<?php 
		$informationProduct_Detail = $pdo->query("SELECT distribution_history.*,product_information.name AS product_name  FROM `distribution_history` INNER JOIN product_information ON distribution_history.product_id=product_information.id where  distribution_history.invoice_id='$MenuName' and distribution_history.distribution_id='$DocumentData' and distribution_history.deleted_at is NULL");
			$serial=1;									
            while ($rowdataProduct_Detail = $informationProduct_Detail->fetch()){	
				?>
        <tr>
          
			   <td class="sl-column"><?php echo $serial; ?><input type="hidden" name="number_count" class="form-control"  value="<?php echo $serial; ?>"/></td>
			   <td class="name-column">
			   
			   
					   <input name="product_id<?php echo $serial; ?>"  type="hidden" placeholder="Name Here"  class="form-control" style="width:100%;" required="required" value="<?php echo $rowdataProduct_Detail["product_id"]; ?>" >
				   <input name="edit_id<?php echo $serial; ?>"  type="hidden" placeholder="Name Here"  class="username form-control" style="width:100%;" required="required" value="<?php echo $rowdataProduct_Detail["id"]; ?>" readonly>
					   <?php echo $rowdataProduct_Detail["product_name"]; ?>
            </td>
		 <td class="qty-column"><?php echo $rowdataProduct_Detail["requestion_quantity"]; ?>
			</td>
         <td class="qty-column"><?php echo $rowdataProduct_Detail["distribution_quantity"]; ?>
			</td>
			
            <td class="receive-column"> <input class="form-control " placeholder="Receive Quantity Here ...." id="received_quantity<?php echo $serial; ?>"   name="received_quantity<?php echo $serial; ?>" onkeyup='check_available<?php echo $serial; ?>()' min="1" max="<?php echo $rowdataProduct_Detail["distribution_quantity"]; ?>" value="<?php echo $rowdataProduct_Detail["distribution_quantity"]; ?>" type="text" required >
		 </td>
        </tr>
		<?php 
			$serial++;
			} ?>
		
    </tbody>

   
    
</table>	
		
<?php } ?>    
 </div>   
</div>
								



  </div>
        
          <!-- /.card-body -->
          <div class="card-footer">
          <div class="box-tools pull-right">
      <?php if(!empty($rowdataDistribution_Type_information["requistion_type"]) && ($rowdataDistribution_Type_information["requistion_type"])=='Fund'){ ?>
      <button type="button" class="btn btn-warning" onclick="history.back(-1)"><i class="fa fa-window-close"></i> Cancel</button>&nbsp;&nbsp;&nbsp;
                    <button class="btn btn-primary" type="submit" name="Fund_Material_Received_Status_Create"><i class="fa fa-save"></i> received </button> 
      <?php }else{ ?>
       <button type="button" class="btn btn-warning" onclick="history.back(-1)"><i class="fa fa-window-close"></i> Cancel</button>&nbsp;&nbsp;&nbsp;
                    <button class="btn btn-primary" type="submit" name="Material_Received_Status_Create"><i class="fa fa-save"></i> received </button> <?php } ?>
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
