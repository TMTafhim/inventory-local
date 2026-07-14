<?php
if(!empty($MenuName)){
	$Detail=$MenuName;
	
$information = $pdo->query("SELECT employee_information.name_en AS name,project_information.name AS project_name,note,date,invoice_id,project_coordinator,project_coordinator_note,project_coordinator_time,project_director,project_director_note,project_director_time,managing_director,managing_director_note,managing_director_time,project_id,requestion_histiory.store_id AS store_id,approval_status,requistion_type FROM `requestion_histiory` INNER JOIN employee_information ON requestion_histiory.employee_id=employee_information.id INNER JOIN project_information ON requestion_histiory.project_id=project_information.id where requestion_histiory.invoice_id='$Detail' and requestion_histiory.deleted_at is NULL");
$rowdata = $information->fetch();
		
$informationApproval_check = $pdo->query("SELECT * FROM `project_material_aproval_status` WHERE `invoice_id`='$Detail' and `approval_status`='Pending' and `deleted_at` is NULL");
$rowdataApproval_check = $informationApproval_check->fetch();					
	


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
										<td width="35%" class="data-row">: &nbsp;<?php echo date("d-m-Y", strtotime($rowdata["date"])); ?></td>
										<td width="18%">Note</td>
										<td width="32%" class="data-row">: &nbsp;<?php echo nl2br($rowdata["note"]);  ?></td>
									</tr>
                                  
								</tbody></table>
								
							
                                
                              
							</div>	
							
			<?php if(!empty($rowdataApproval_check["employee_id"]) && $rowdataApproval_check["employee_id"]==$LoginReGiSterSession){ ?>
											
			<div class="col-sm-12">
								<div class="row">
								
								<form method="post" action="" enctype="multipart/form-data" style="width:100%;">
	
	<input type="hidden" name="invoice_id"  placeholder="Name Here"  class=" form-control" style="width:100%;" required="required" value="<?php echo $Detail; ?>" ><input type="hidden" name="project_id"  placeholder="Name Here"  class=" form-control" style="width:100%;" required="required" value="<?php echo $rowdata["project_id"]; ?>" >
									
<input type="hidden" name="store_id"  placeholder="Name Here"  class=" form-control" style="width:100%;" required="required" value="<?php echo $rowdata["store_id"]; ?>" >	
<input type="hidden" name="requistion_type"  placeholder="Name Here"  class=" form-control" style="width:100%;" required="required" value="<?php echo $rowdata["requistion_type"]; ?>" >
        <!-- SELECT2 EXAMPLE -->
        <div class="">
          <div class="row">
  <div class="col-sm-12">
  <p>Requestion Detail :<span style="color:#FF0000">*</span></p>
    <div style="overflow-x:auto;">
	<?php if(!empty($rowdata["requistion_type"]) && $rowdata["requistion_type"]=='Fund'){ ?>
	
<table id="myTable" class="table order-list">
    <thead>
        <tr>
           
			<th>Name</th>
			<th>Unit</th>
            <th>Requestion Amount</th>
			<th>Approval Amount</th>
        </tr>
    </thead>
    <tbody>
		
		<?php 
		$informationProduct_Detail = $pdo->query("SELECT requestion_detail.*,product_information.name AS product_name,product_information.unit AS product_unit   FROM `requestion_detail` INNER JOIN product_information ON requestion_detail.product_id=product_information.id where  requestion_detail.invoice_id='".$Detail."' and requestion_detail.deleted_at is NULL");
			$serial=1;	
		$total_requestion_amount=0;
		$total_final_amount=0;
            while ($rowdataProduct_Detail = $informationProduct_Detail->fetch()){
         $total_requestion_amount+=$rowdataProduct_Detail["requestion_amount"];
		$total_final_amount+=$rowdataProduct_Detail["final_amount"];       
                
				?>
        <tr>
          
			   <td style="width: 35%;"><input type="hidden" name="number_count" class="form-control"  value="<?php echo $serial; ?>"/>
				   
				    <input name="product_id<?php echo $serial; ?>"  type="hidden" placeholder="Name Here"  class=" form-control" style="width:100%;" required="required" value="<?php echo $rowdataProduct_Detail["product_id"]; ?>" >
				   
				   <input name="edit_id<?php echo $serial; ?>"  type="hidden" placeholder="Name Here"  class="username form-control" style="width:100%;" required="required" value="<?php echo $rowdataProduct_Detail["id"]; ?>" readonly>
				   
				   <input name="name<?php echo $serial; ?>" type="hidden" id="username_1" placeholder="Name Here"  class="username form-control" style="width:100%;" required="required" value="<?php echo $rowdataProduct_Detail["product_name"]; ?>" readonly><?php echo $rowdataProduct_Detail["product_name"]; ?>
            </td>
		
         <td><?php echo $rowdataProduct_Detail["product_unit"]; ?></td>
			<td>
			<input class="form-control " placeholder="Amount Here ...."   name="requestion_amount<?php echo $serial; ?>" id="requestion_amount<?php echo $serial; ?>" type="text" required  value="<?php echo $rowdataProduct_Detail["requestion_amount"]; ?>" readonly>
			</td>
            <td> <input class="form-control cm_cls" onkeyup="get_total_vaue();" placeholder="Approval Amount Here ...." id="final_amount<?php echo $serial; ?>"  value="<?php echo $rowdataProduct_Detail["final_amount"]; ?>" name="final_amount<?php echo $serial; ?>" type="text" required ></td>
           
            </td>
        </tr>
		<?php 
			$serial++;
			} ?>
		
    </tbody>

   <tfoot>
       
        <tr>
          

              <td colspan="2" style="text-align:right;">
			Total Amount
            </td>
		   <td><strong><?php echo $total_requestion_amount; ?></strong></td>
            <td>
                <input type="text" id="totalPrice" class="form-control" placeholder="Amount Here .." value="<?php echo $total_final_amount; ?>" required readonly/>
            </td>
		
          
        </tr>  
        
    </tfoot>
    
</table>	
	
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
</script>	
	<?php }else{ ?>
	<table id="myTable" class="table order-list">
    <thead>
        <tr>
           
			<th>Name</th>
			<th>Unit</th>
            <th>Requestion Quantity</th>
			<th>Approval Quantity</th>
        </tr>
    </thead>
    <tbody>
		
		<?php 
		$informationProduct_Detail = $pdo->query("SELECT requestion_detail.*,product_information.name AS product_name,product_information.unit AS product_unit   FROM `requestion_detail` INNER JOIN product_information ON requestion_detail.product_id=product_information.id where  requestion_detail.invoice_id='".$Detail."' and requestion_detail.deleted_at is NULL");
			$serial=1;									
        while ($rowdataProduct_Detail = $informationProduct_Detail->fetch()){	
				?>
        <tr>
          
			   <td style="width: 35%;"><input type="hidden" name="number_count" class="form-control"  value="<?php echo $serial; ?>"/>
				   
				    <input name="product_id<?php echo $serial; ?>"  type="hidden" placeholder="Name Here"  class=" form-control" style="width:100%;" required="required" value="<?php echo $rowdataProduct_Detail["product_id"]; ?>" >
				   
				   <input name="edit_id<?php echo $serial; ?>"  type="hidden" placeholder="Name Here"  class="username form-control" style="width:100%;" required="required" value="<?php echo $rowdataProduct_Detail["id"]; ?>" readonly>
				   
				   <input name="name<?php echo $serial; ?>" type="hidden" id="username_1" placeholder="Name Here"  class="username form-control" style="width:100%;" required="required" value="<?php echo $rowdataProduct_Detail["product_name"]; ?>" readonly><?php echo $rowdataProduct_Detail["product_name"]; ?>
            </td>
		
         <td><?php echo $rowdataProduct_Detail["product_unit"]; ?></td>
			<td>
			<input class="form-control " placeholder="Quantity Here ...."   name="requestion_quantity<?php echo $serial; ?>" id="requestion_quantity<?php echo $serial; ?>" type="text" required  value="<?php echo $rowdataProduct_Detail["requestion_quantity"]; ?>" readonly>
			</td>
            <td> <input class="form-control " placeholder="Approval Quantity Here ...." id="final_quantity<?php echo $serial; ?>"  value="<?php echo $rowdataProduct_Detail["final_quantity"]; ?>" name="final_quantity<?php echo $serial; ?>" type="text" required ></td>
           
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

 <div class="col-sm-12">
        <div class="form-group">
            <label for="note">Note (if Need)</label>
            <textarea class="form-control"  placeholder="Note Here ...." name="note" id="note" type="text" ></textarea>
        </div>
    </div>
			







  </div>
        
          <!-- /.card-body -->
          <div class="card-footer">
          <div class="box-tools pull-right">
       <button type="button" class="btn btn-warning" onclick="history.back(-1)"><i class="fa fa-window-close"></i> Cancel</button>&nbsp;&nbsp;&nbsp;
       <?php if(!empty($rowdata["requistion_type"]) && $rowdata["requistion_type"]=='Fund'){ ?>
           <button class="btn btn-primary" type="submit" name="Product_requistion_Fund_approval_Start"> <i class="fa fa-save"></i> Approve </button>
            <?php }else{ ?> 
            <button class="btn btn-primary" type="submit" name="Product_requistion_approval_Start"> <i class="fa fa-save"></i> Approve </button>
            <?php } ?>
                    
                </div>
          </div>
        </div>
        <!-- /.card -->
</form>
								
								
								
								
								</div>			
							
							
							
							
							</div>

							
					<?php }else{ ?>		
                              
                         <div class="col-xl-12 table-div-area"><br>	
        <?php if(!empty($rowdata["requistion_type"]) && $rowdata["requistion_type"]=='Fund'){ ?>                 
                         
			<table style="width: 100%;">
					<tr>
						<th>SL</th>	
						 <th>Name</th>
						 <th>Amount</th>
						<?php if(!empty($rowdata["approval_status"]) && $rowdata["approval_status"]=='Approve'){ ?>
						<th>Distribution Amount</th>
						<th>Due Amount</th>
						<?php } ?>
						</tr>	
					   <?php
	
			$informationProduct_Detail = $pdo->query("SELECT requestion_detail.*,product_information.name AS product_name,product_information.unit AS product_unit  FROM `requestion_detail` INNER JOIN product_information ON requestion_detail.product_id=product_information.id where  requestion_detail.invoice_id='$Detail' and requestion_detail.deleted_at is NULL");
			$serial=1;
			$amount_sum=0;
			$received_amount_total=0;
			$due_amount_total=0;
            while ($rowdataProduct_Detail = $informationProduct_Detail->fetch()){	
				
			$amount_sum+=$rowdataProduct_Detail["final_amount"];
			
			if(!empty($rowdata["approval_status"]) && $rowdata["approval_status"]=='Approve'){
			$received_amount_total+=$rowdataProduct_Detail["received_amount"];
			$due_amount_total+=$rowdataProduct_Detail["due_amount"];
			    
			}
			
			
			
						?>
                        
                        <tr>
                    <td text align="center"><?php echo $serial; ?></td>        
					<td text align="left" style="padding-left: 10px;"><?php if(!empty($rowdataProduct_Detail["product_name"])){ echo $rowdataProduct_Detail["product_name"]; }  ?></td>	
					
				
					<td text align="center"><?php echo $rowdataProduct_Detail["final_amount"]; ?></td>
							
					<?php if(!empty($rowdata["approval_status"]) && $rowdata["approval_status"]=='Approve'){ ?>
						<td text align="center"><?php echo $rowdataProduct_Detail["received_amount"]; ?></td>
						<td text align="center"><?php echo $rowdataProduct_Detail["due_amount"]; ?></td>
						<?php } ?>		
							
                        </tr>
                        
                        
                        <?php 
                        $serial++;
                        } ?>
					<tr>
					    
					 <td colspan="2" style="text-align:right;">Total&nbsp;:&nbsp;&nbsp;</td>   
					 <td style="text-align:center;"><strong><?php echo $amount_sum; ?></strong></td>
					 <?php if(!empty($rowdata["approval_status"]) && $rowdata["approval_status"]=='Approve'){ ?>
					 <td style="text-align:center;"><strong><?php echo $received_amount_total; ?></strong></td>
					 <td style="text-align:center;"><strong><?php echo $due_amount_total; ?></strong></td>
					 <?php } ?>
					    
					</tr>	
						
						
						</table>
						
					<?php }else{ ?>	
					<table style="width: 100%;">
					<tr>
						<th>SL</th>	
						 <th>Name</th>
						 <th>Quantity</th>
						<?php if(!empty($rowdata["approval_status"]) && $rowdata["approval_status"]=='Approve'){ ?>
						<th>Distribution Quantity</th>
						<th>Due Quantity</th>
						<?php } ?>
						</tr>	
					   <?php
	
			$informationProduct_Detail = $pdo->query("SELECT requestion_detail.*,product_information.name AS product_name,product_information.unit AS product_unit  FROM `requestion_detail` INNER JOIN product_information ON requestion_detail.product_id=product_information.id where  requestion_detail.invoice_id='$Detail' and requestion_detail.deleted_at is NULL");
			$serial=1;
			$amount_sum=0;									
            while ($rowdataProduct_Detail = $informationProduct_Detail->fetch()){	
				
			
						?>
                        
                        <tr>
                    <td text align="center"><?php echo $serial; ?></td>        
					<td text align="left" style="padding-left: 10px;"><?php if(!empty($rowdataProduct_Detail["product_name"])){ echo $rowdataProduct_Detail["product_name"]; }  ?></td>	
					
				
					<td text align="center"><?php echo $rowdataProduct_Detail["final_quantity"]; ?> <?php echo $rowdataProduct_Detail["product_unit"]; ?></td>
							
					<?php if(!empty($rowdata["approval_status"]) && $rowdata["approval_status"]=='Approve'){ ?>
						<td text align="center"><?php echo $rowdataProduct_Detail["distribution_quantity"]; ?></td>
						<td text align="center"><?php echo $rowdataProduct_Detail["due_quantity"]; ?></td>
						<?php } ?>		
							
                        </tr>
                        
                        
                        <?php 
                        $serial++;
                        } ?>
						
						
						
						</table>
					<?php } ?>
								</div>
						<?php $Staff_information = $pdo->query("SELECT employee_information.*,project_material_aproval_status.note AS note  FROM project_material_aproval_status INNER JOIN employee_information ON project_material_aproval_status.employee_id=employee_information.id where  project_material_aproval_status.invoice_id='".$rowdata["invoice_id"]."' and project_material_aproval_status.project_id='".$rowdata["project_id"]."' and approval_status='Approve' and project_material_aproval_status.approval_id is not NULL");
                    while($rowdataStaff_information = $Staff_information->fetch()){
	if(!empty($rowdataStaff_information["note"])){		
			?>									
<div class="col-sm-4">
        <div class="form-group">
            <label for="note">Note (<?php echo $rowdataStaff_information["name_en"]; ?>)</label>
            <p><?php echo nl2br($rowdataStaff_information["note"]);  ?></p>
        </div>
    </div>									
<?php
	}
	} ?>	


<div class="col-sm-12">
<div class="row">    
    
    
								<?php $Staff_information_signature = $pdo->query("SELECT employee_information.*,hr_designation.name AS designation_name,project_material_aproval_status.approval_date AS approval_date FROM project_material_aproval_status INNER JOIN employee_information ON project_material_aproval_status.employee_id=employee_information.id INNER JOIN hr_designation ON employee_information.designation=hr_designation.id where  project_material_aproval_status.invoice_id='".$rowdata["invoice_id"]."' and project_material_aproval_status.project_id='".$rowdata["project_id"]."' and approval_status='Approve' and project_material_aproval_status.approval_id is not NULL");
                    while($rowdataStaff_information_signature = $Staff_information_signature->fetch()){
							?>
							<div class="col-sm-4" style="margin-top: 100px;">
							<?php if(!empty($rowdataStaff_information_signature["signature"])){ ?>	
							<img src="Signature/<?php echo $rowdataStaff_information_signature["signature"]; ?>" style="height: 30px;width:100px;">
								
								<?php } ?>
								<p style="font-size: 20px;;margin:0px;"><?php echo date("d/m/Y", strtotime($rowdataStaff_information_signature["approval_date"])); ?></p>
							<p style="font-size: 20px;font-weight: bold;margin:0px;"><?php echo $rowdataStaff_information_signature["name_en"]; ?></p>
							<p style="margin:0px;"><?php echo $rowdataStaff_information_signature["designation_name"]; ?></p>	
							</div>	
							<?php } ?>
							
							
							
							
							
							
					<?php } ?>		
							
						</div>
						
						

				
		</div></div>			
			</div>	
			
			
		
			
          
        </div>
        <!-- /.card-body -->
      </div>
      <!-- /.card -->

    </section>

<?php } ?>