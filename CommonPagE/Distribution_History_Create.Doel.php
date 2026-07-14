<script>
		<?php for($seript_serial=1;$seript_serial<=500;$seript_serial++){ ?>
		  
	     function check_available<?php echo $seript_serial; ?>() {
	    var distribution_input = document.getElementById('distribution_quantity<?php echo $seript_serial; ?>');
	    var due_input = document.getElementById('due_quantity<?php echo $seript_serial; ?>');
	    if(!distribution_input || !due_input){
	      return;
	    }
	    var have_value = Number(distribution_input.max || due_input.value || 0);
	      var input_value = Number(distribution_input.value || 0);
	      if(input_value > have_value)
	      {
	        distribution_input.value="";
	      }
	      }
  

		<?php } ?>
		</script>

<?php
if(!empty($MenuName)){
	$Detail=$MenuName;

$information = $pdo->query("SELECT employee_information.name_en AS name,project_information.name AS project_name,note,date,invoice_id,project_coordinator,project_coordinator_note,project_coordinator_time,project_director,project_director_note,project_director_time,managing_director,managing_director_note,managing_director_time,project_id,requestion_histiory.store_id AS store_id,requistion_type FROM `requestion_histiory` INNER JOIN employee_information ON requestion_histiory.employee_id=employee_information.id INNER JOIN project_information ON requestion_histiory.project_id=project_information.id where approval_status='Approve' and  requestion_histiory.invoice_id='$Detail' and requestion_histiory.deleted_at is NULL");
$rowdata = $information->fetch();
		
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

	.distribution-table-wrap{
		width: 100%;
		overflow-x: auto;
	}

	.distribution-create-table .form-control{
		min-width: 96px;
	}

	.my-info-area table.table.distribution-create-table{
		min-width: 820px !important;
		table-layout: fixed !important;
	}

	.my-info-area table.table.distribution-create-table th,
	.my-info-area table.table.distribution-create-table td{
		padding: 9px 10px !important;
		white-space: normal !important;
		word-break: normal !important;
		overflow-wrap: break-word !important;
	}

	.my-info-area table.table.distribution-create-table th:first-child,
	.my-info-area table.table.distribution-create-table td:first-child{
		width: 52px !important;
		min-width: 52px !important;
		max-width: 52px !important;
		text-align: center !important;
	}

	.my-info-area table.table.distribution-create-table th:nth-child(2),
	.my-info-area table.table.distribution-create-table td:nth-child(2){
		width: 38% !important;
		min-width: 38% !important;
		max-width: none !important;
		text-align: left !important;
	}

	.my-info-area table.table.distribution-create-table th:nth-child(3),
	.my-info-area table.table.distribution-create-table td:nth-child(3){
		width: 15% !important;
		min-width: 15% !important;
		text-align: center !important;
	}

	.my-info-area table.table.distribution-create-table th:nth-child(4),
	.my-info-area table.table.distribution-create-table td:nth-child(4){
		width: 12% !important;
		min-width: 12% !important;
		text-align: center !important;
	}

	.my-info-area table.table.distribution-create-table th:nth-child(5),
	.my-info-area table.table.distribution-create-table td:nth-child(5){
		width: 15% !important;
		min-width: 15% !important;
		text-align: center !important;
	}

	.my-info-area table.table.distribution-create-table th:nth-child(6),
	.my-info-area table.table.distribution-create-table td:nth-child(6){
		width: 20% !important;
		min-width: 20% !important;
		text-align: center !important;
	}

	.my-info-area table.table.distribution-create-table.fund-distribution-table th:first-child,
	.my-info-area table.table.distribution-create-table.fund-distribution-table td:first-child{
		width: 52px !important;
		min-width: 52px !important;
		max-width: 52px !important;
		text-align: center !important;
	}

	.my-info-area table.table.distribution-create-table.fund-distribution-table th:nth-child(2),
	.my-info-area table.table.distribution-create-table.fund-distribution-table td:nth-child(2){
		width: 46% !important;
		min-width: 46% !important;
		text-align: left !important;
	}

	.my-info-area table.table.distribution-create-table.fund-distribution-table th:nth-child(3),
	.my-info-area table.table.distribution-create-table.fund-distribution-table td:nth-child(3){
		width: 17% !important;
		min-width: 17% !important;
		text-align: center !important;
	}

	.my-info-area table.table.distribution-create-table.fund-distribution-table th:nth-child(4),
	.my-info-area table.table.distribution-create-table.fund-distribution-table td:nth-child(4){
		width: 17% !important;
		min-width: 17% !important;
		text-align: center !important;
	}

	.my-info-area table.table.distribution-create-table.fund-distribution-table th:nth-child(5),
	.my-info-area table.table.distribution-create-table.fund-distribution-table td:nth-child(5){
		width: 20% !important;
		min-width: 20% !important;
		text-align: center !important;
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
	.distribution-partial-stamp{
		display:inline-flex;
		align-items:center;
		gap:6px;
		border:2px solid #d39e00;
		color:#8a6100;
		background:#fff8df;
		font-weight:700;
		letter-spacing:.04em;
		text-transform:uppercase;
		border-radius:4px;
		padding:5px 10px;
		line-height:1;
	}
	.distribution-track-title{
		display:flex;
		align-items:center;
		justify-content:space-between;
		gap:12px;
		margin:18px 0 8px;
		font-weight:700;
		color:#2f3b4a;
	}
	.distribution-batch-actions{
		display:flex;
		flex-wrap:wrap;
		gap:8px;
		margin:8px 0 16px;
	}
	.distribution-batch-actions .btn{
		font-weight:700;
	}
	.distribution-track-table{
		width:100%;
		margin:0 0 12px;
		background:#fff;
	}
	.distribution-track-table th{
		background:#edf3fa;
		color:#334155;
		font-weight:700;
		text-align:center;
	}
	.distribution-track-modal .modal-header{
		background:#f8fafc;
		border-bottom:1px solid #dbe5f0;
	}
	.distribution-track-modal .modal-title{
		font-weight:700;
		color:#253245;
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
										<td width="18%">Requisition Information</td>
										<td width="32%" class="data-row">: &nbsp;<?php echo $rowdata["name"]; ; ?></td>
									</tr>
                                   
                                    
                                    <tr>
										<td width="15%"> Date</td>
										<td width="35%" class="data-row">: &nbsp;<?php echo date("d-m-Y", strtotime($current_date)); ?></td>
										<td width="18%">Note</td>
										<td width="32%" class="data-row">: &nbsp;<?php echo nl2br($rowdata["note"]);  ?></td>
									</tr>
                                  
								</tbody></table>
								
							
                                
                              
							</div>	
							
						
		
			
			<div class="col-sm-12">
								<div class="row">
								
								<form method="post" action="?<?php echo "Requestion/".$MenuName ?>" enctype="multipart/form-data" style="width:100%;">
	
	<input type="hidden" name="invoice_id"  placeholder="Name Here"  class=" form-control" style="width:100%;" required="required" value="<?php echo $Detail; ?>" ><input type="hidden" name="project_id"  placeholder="Name Here"  class=" form-control" style="width:100%;" required="required" value="<?php echo $rowdata["project_id"]; ?>" >
	<input type="hidden" name="store_id"  placeholder="Name Here"  class=" form-control" style="width:100%;" required="required" value="<?php echo $rowdata["store_id"]; ?>" >								
									
        <!-- SELECT2 EXAMPLE -->
        <div class="">
          <div class="row">
  <div class="col-sm-12">
	  <p>Distribution  Detail :<span style="color:#FF0000">*</span></p>
	    <div class="distribution-table-wrap">
        
  	<?php if(!empty($rowdata["requistion_type"]) && $rowdata["requistion_type"]=='Fund'){ ?>
	  		<table id="myTable" class="table order-list app-line-item-table distribution-create-table fund-distribution-table">
	    <thead>
	        <tr>
	           
				<th>SL</th>
				<th>Name</th>
	            <th>Requisition Amount</th>
				<th>Due amount</th>
			<th>Distribution Amount</th>
        </tr>
    </thead>
    <tbody>
		
		<?php 
		
		
		$informationProduct_Detail = $pdo->query("SELECT requestion_detail.*,product_information.name AS product_name  FROM `requestion_detail` INNER JOIN product_information ON requestion_detail.product_id=product_information.id where  requestion_detail.invoice_id='".$Detail."' and due_amount!=0 and requestion_detail.deleted_at is NULL");
			$serial=1;									
            while ($rowdataProduct_Detail = $informationProduct_Detail->fetch()){	
				
		
				
				
				
				?>
	        <tr>
	          
				   <td><?php echo $serial; ?></td>
				   <td><input type="hidden" name="number_count" class="form-control"  value="<?php echo $serial; ?>"/>
					   
					   <input name="edit_id<?php echo $serial; ?>"  type="hidden" placeholder="Name Here"  class="username form-control" style="width:100%;" required="required" value="<?php echo $rowdataProduct_Detail["id"]; ?>" readonly>
					   <?php echo $rowdataProduct_Detail["product_name"]; if(!empty($rowdataProduct_Detail["detail"])){ echo " ( ".$rowdataProduct_Detail["detail"]." ) "; } ?>
            </td>
		 <td><?php echo $rowdataProduct_Detail["final_amount"]; ?>
			</td>
         
			<td>
			<input class="form-control " placeholder="Quantity Here ...."   name="due_quantity<?php echo $serial; ?>" id="due_quantity<?php echo $serial; ?>" type="hidden" required onkeyup='check_available<?php echo $serial; ?>()' value="<?php echo $rowdataProduct_Detail["due_amount"]; ?>" readonly><?php echo $rowdataProduct_Detail["due_amount"]; ?>
			</td>
			
		
            <td><input class="form-control " placeholder="Distribution Amount Here ...." id="distribution_amount<?php echo $serial; ?>"   name="distribution_amount<?php echo $serial; ?>"  min="1" value="<?php echo $rowdataProduct_Detail["due_amount"]; ?>" max="<?php echo $rowdataProduct_Detail["due_amount"]; ?>" type="text"  >
			 </td>
           
            </td>
        </tr>
		<?php 
			$serial++;
			} ?>
		
    </tbody>

   
    
</table>
  	
  	<?php }else{ ?>
        
        
		
		<table id="myTable" class="table order-list app-line-item-table distribution-create-table">
	    <thead>
	        <tr>
	           
				<th>SL</th>
				<th>Name</th>
	            <th>Requisition Quantity</th>
				<th>Due Quantity</th>
			<th>Stock Quantity</th>
			<th>Distribution Quantity</th>
        </tr>
    </thead>
    <tbody>
		
		<?php 
		$informationProduct_Detail = $pdo->query("SELECT requestion_detail.*,product_information.name AS product_name  FROM `requestion_detail` INNER JOIN product_information ON requestion_detail.product_id=product_information.id where  requestion_detail.invoice_id='".$Detail."' and due_quantity!=0 and requestion_detail.deleted_at is NULL");
			$serial=1;									
		    while ($rowdataProduct_Detail = $informationProduct_Detail->fetch()){	
					
			$stock_store_id = !empty($rowdata["store_id"]) ? $rowdata["store_id"] : $rowdataProduct_Detail["store_id"];
			$Stock_Detail = $pdo->query("SELECT COALESCE(SUM(CAST(NULLIF(stock, '') AS DECIMAL(18,4))),0) AS stock FROM `stock_information` where store_id='".$stock_store_id."' and product_id='".$rowdataProduct_Detail["product_id"]."' and deleted_at is NULL");
			$rowdataStock_Detail = $Stock_Detail->fetch();
			$available_stock = !empty($rowdataStock_Detail["stock"]) ? (float)$rowdataStock_Detail["stock"] : 0;
			$max_distribution_quantity = min((float)$rowdataProduct_Detail["due_quantity"], $available_stock);
					
					
					
				?>
	        <tr>
	          
				   <td><?php echo $serial; ?></td>
				   <td><input type="hidden" name="number_count" class="form-control"  value="<?php echo $serial; ?>"/>
					   
					   <input name="edit_id<?php echo $serial; ?>"  type="hidden" placeholder="Name Here"  class="username form-control" style="width:100%;" required="required" value="<?php echo $rowdataProduct_Detail["id"]; ?>" readonly>
					   <?php echo $rowdataProduct_Detail["product_name"]; ?>
            </td>
		 <td><?php echo $rowdataProduct_Detail["final_quantity"]; ?>
			</td>
         
			<td>
			<input class="form-control " placeholder="Quantity Here ...."   name="due_quantity<?php echo $serial; ?>" id="due_quantity<?php echo $serial; ?>" type="hidden" required onkeyup='check_available<?php echo $serial; ?>()' value="<?php echo $rowdataProduct_Detail["due_quantity"]; ?>" readonly><?php echo $rowdataProduct_Detail["due_quantity"]; ?>
			</td>
				
				<td>
				<?php if($available_stock > 0){ echo $available_stock; }else{ echo "Stock is not Available"; }  ?>
				</td>
	            <td><?php if($available_stock > 0){ ?> <input class="form-control " placeholder="Distribution Quantity Here ...." id="distribution_quantity<?php echo $serial; ?>"   name="distribution_quantity<?php echo $serial; ?>" onkeyup='check_available<?php echo $serial; ?>()' min="1" max="<?php echo $max_distribution_quantity; ?>" type="text"  >
				<?php }else{ } ?> </td>
           
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
      <?php if(!empty($rowdata["requistion_type"]) && $rowdata["requistion_type"]=='Fund'){ ?>
      <button type="button" class="btn btn-warning" onclick="history.back(-1)"><i class="fa fa-window-close"></i> Cancel</button>&nbsp;&nbsp;&nbsp;
                    <button class="btn btn-primary" type="submit" name="Distribution_Fund_Start"><i class="fa fa-save"></i> Distribution </button>
      <?php }else{ ?>
       <button type="button" class="btn btn-warning" onclick="history.back(-1)"><i class="fa fa-window-close"></i> Cancel</button>&nbsp;&nbsp;&nbsp;
                    <button class="btn btn-primary" type="submit" name="Distribution_Start"><i class="fa fa-save"></i> Distribution </button>
                    <?php } ?>
                </div>
          </div>
        </div>
        <!-- /.card -->
</form>
<?php
$distributionTrackType=(!empty($rowdata["requistion_type"]) && $rowdata["requistion_type"]=='Fund') ? 'Fund' : 'Material';
$distributionTrackQuantityColumn=$distributionTrackType=='Fund' ? 'distribution_amount' : 'distribution_quantity';
$distributionTrackDueColumn=$distributionTrackType=='Fund' ? 'due_amount' : 'due_quantity';
$distributionTrackLabel=$distributionTrackType=='Fund' ? 'Amount' : 'Quantity';
$distributionRemainingDueStatement=$pdo->prepare("SELECT COALESCE(SUM(GREATEST(CAST(COALESCE(NULLIF(requestion_detail.".$distributionTrackDueColumn.",''),0) AS DECIMAL(18,4)),0)),0)
	FROM requestion_detail
	INNER JOIN product_information ON requestion_detail.product_id=product_information.id
	WHERE requestion_detail.invoice_id=:invoice_id
	  AND requestion_detail.deleted_at IS NULL");
$distributionRemainingDueStatement->execute(array(':invoice_id'=>$Detail));
$distributionRemainingDue=(float)$distributionRemainingDueStatement->fetchColumn();
$distributionBatchStatement=$pdo->prepare("SELECT distribution_id,date,created_at FROM distribution_summary WHERE invoice_id=:invoice_id AND deleted_at IS NULL ORDER BY id ASC");
$distributionBatchStatement->execute(array(':invoice_id'=>$Detail));
$distributionBatches=$distributionBatchStatement->fetchAll();
if(!empty($distributionBatches)){
?>
<div class="col-xl-12 table-div-area">
	<div class="distribution-track-title">
		<span><i class="fas fa-history"></i> Previous Distribution</span>
		<?php if($distributionRemainingDue>0){ ?><span class="distribution-partial-stamp"><i class="fas fa-stamp"></i> Partial</span><?php } ?>
	</div>
	<div class="distribution-batch-actions d-print-none">
		<?php foreach($distributionBatches as $distributionBatch){
			$distributionModalId='distributionCreateTrackModal'.preg_replace('/[^A-Za-z0-9_]/','',(string)$distributionBatch["distribution_id"]);
			$distributionBatchLabel=date("d-m-Y", strtotime($distributionBatch["date"]));
			if(!empty($distributionBatch["created_at"])){
				$distributionBatchLabel.=" ".date("h:i A", strtotime($distributionBatch["created_at"]));
			}
		?>
		<button type="button" class="btn btn-outline-primary btn-sm" data-toggle="modal" data-target="#<?php echo $distributionModalId; ?>">
			<i class="fas fa-clock"></i> <?php echo $distributionBatchLabel; ?>
		</button>
		<?php } ?>
	</div>
</div>
<?php } ?>
								
								
								
								
								</div>			
							
							
							
							
							</div>
			
			
			
			
			
				
							
						</div>
						
						

				
					
			</div>	
			
			
		
			
          
        </div>
        <!-- /.card-body -->
      </div>
      <!-- /.card -->

    </section>

<?php if(!empty($distributionBatches)){ ?>
	<?php foreach($distributionBatches as $distributionBatch){
		$distributionModalId='distributionCreateTrackModal'.preg_replace('/[^A-Za-z0-9_]/','',(string)$distributionBatch["distribution_id"]);
		$distributionBatchLabel=date("d-m-Y", strtotime($distributionBatch["date"]));
		if(!empty($distributionBatch["created_at"])){
			$distributionBatchLabel.=" ".date("h:i A", strtotime($distributionBatch["created_at"]));
		}
		$distributionBatchDetailStatement=$pdo->prepare("SELECT distribution_history.distribution_id,distribution_history.date,distribution_history.created_at,distribution_history.".$distributionTrackQuantityColumn." AS distributed_value,distribution_history.".$distributionTrackDueColumn." AS due_value,product_information.name AS product_name FROM distribution_history INNER JOIN product_information ON distribution_history.product_id=product_information.id WHERE distribution_history.invoice_id=:invoice_id AND distribution_history.distribution_id=:distribution_id AND distribution_history.deleted_at IS NULL ORDER BY distribution_history.id ASC");
		$distributionBatchDetailStatement->execute(array(':invoice_id'=>$Detail,':distribution_id'=>$distributionBatch["distribution_id"]));
		$distributionBatchRows=$distributionBatchDetailStatement->fetchAll();
		$distributionBatchTotal=0;
	?>
	<div class="modal fade distribution-track-modal d-print-none" id="<?php echo $distributionModalId; ?>" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title"><i class="fas fa-history"></i> Previous Distribution - <?php echo $distributionBatchLabel; ?></h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="mb-2">
						<strong>Invoice:</strong> <?php echo $Detail; ?> &nbsp; | &nbsp;
						<strong>Distribution ID:</strong> <?php echo $distributionBatch["distribution_id"]; ?>
					</div>
					<table class="table table-bordered table-striped distribution-track-table">
						<thead>
							<tr>
								<th style="width:60px;">SL</th>
								<th>Product</th>
								<th style="width:180px;">Distributed <?php echo $distributionTrackLabel; ?></th>
								<th style="width:150px;">Due After</th>
							</tr>
						</thead>
						<tbody>
							<?php if(!empty($distributionBatchRows)){ $distributionBatchSerial=1; foreach($distributionBatchRows as $distributionBatchRow){ $distributionBatchTotal+=(float)$distributionBatchRow["distributed_value"]; ?>
							<tr>
								<td text align="center"><?php echo $distributionBatchSerial; ?></td>
								<td><?php echo $distributionBatchRow["product_name"]; ?></td>
								<td text align="right"><?php echo rtrim(rtrim(number_format((float)$distributionBatchRow["distributed_value"],4,'.',''), '0'), '.'); ?></td>
								<td text align="right"><?php echo rtrim(rtrim(number_format((float)$distributionBatchRow["due_value"],4,'.',''), '0'), '.'); ?></td>
							</tr>
							<?php $distributionBatchSerial++; } }else{ ?>
							<tr>
								<td colspan="4" text align="center">No distribution details found for this date.</td>
							</tr>
							<?php } ?>
						</tbody>
						<tfoot>
							<tr>
								<th colspan="2" style="text-align:right;">Total Distributed&nbsp;:&nbsp;</th>
								<th style="text-align:right;"><?php echo rtrim(rtrim(number_format($distributionBatchTotal,4,'.',''), '0'), '.'); ?></th>
								<th></th>
							</tr>
						</tfoot>
					</table>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>
	<?php } ?>
<?php } ?>

<?php } } ?>
