<?php
if(!empty($DocumentData)){
	$Detail=$DocumentData;

$information = $pdo->query("SELECT employee_information.name_en AS name,project_information.name AS project_name,distribution_summary.date AS distribution_date,invoice_id,distribution_id  FROM `distribution_summary` INNER JOIN employee_information ON distribution_summary .employee_id=employee_information.id INNER JOIN project_information ON distribution_summary.project_id=project_information.id where distribution_summary.distribution_id='$Detail' and distribution_summary.deleted_at is NULL");
$rowdata = $information->fetch();
		
		
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
										<td width="18%">Distribution ID</td>
										<td width="32%" class="data-row">: &nbsp;<?php echo $rowdata["distribution_id"]; ; ?></td>
									</tr>
                                   
                                    
                                    <tr>
										<td width="15%"> Date</td>
										<td width="35%" class="data-row">: &nbsp;<?php echo date("d-m-Y", strtotime($rowdata["distribution_date"])); ?></td>
										
									</tr>
                                  
								</tbody></table>
                                
                              
							</div>	
							
			
                              
          <div class="col-xl-12 table-div-area"><br>
          <?php if(!empty($rowdataDistribution_Type_information["requistion_type"]) && ($rowdataDistribution_Type_information["requistion_type"])=='Fund'){ ?>
          	<table style="width: 100%;">
					<tr>
						<th>SL</th>	
						 <th>Name</th>
						 <th>Amount</th>
					
						</tr>	
					   <?php
	
			$informationProduct_Detail = $pdo->query("SELECT distribution_history.*,product_information.name AS product_name  FROM `distribution_history` INNER JOIN product_information ON distribution_history.product_id=product_information.id where  distribution_history.invoice_id='$MenuName' and distribution_id='".$rowdata["distribution_id"]."' and distribution_history.deleted_at is NULL");
			$serial=1;
			$amount_sum=0;									
            while ($rowdataProduct_Detail = $informationProduct_Detail->fetch()){	
				
			$amount_sum+=$rowdataProduct_Detail["distribution_amount"];
						?>
                        
                        <tr>
                    <td text align="center"><?php echo $serial; ?></td>        
					<td text align="left" style="padding-left: 10px;"><?php if(!empty($rowdataProduct_Detail["product_name"])){ echo $rowdataProduct_Detail["product_name"]; }if(!empty($rowdataProduct_Detail["detail"])){ echo " ( ".$rowdataProduct_Detail["detail"]." ) "; }  ?></td>	
					
				
					<td text align="center"><?php echo $rowdataProduct_Detail["distribution_amount"]; ?></td>
							
						
							
                        </tr>
                        
                        
                        <?php } ?>
						
					<tfoot>
					 <tr>
					     <td colspan="2" style="text-align:right;">Total&nbsp;:&nbsp;</td>
					     <td style="text-align:center;"><strong><?php echo $amount_sum; ?></strong></td>
					     
					 </tr>   
					    
					</tfoot>	
						
						</table>
          
          <?php }else{ ?>
          
          
						<table style="width: 100%;">
					<tr>
						<th>SL</th>	
						 <th>Name</th>
						 <th>Quantity</th>
					
						</tr>	
					   <?php
	
			$informationProduct_Detail = $pdo->query("SELECT distribution_history.*,product_information.name AS product_name  FROM `distribution_history` INNER JOIN product_information ON distribution_history.product_id=product_information.id where  distribution_history.invoice_id='$MenuName' and distribution_id='".$rowdata["distribution_id"]."' and distribution_history.deleted_at is NULL");
			$serial=1;
			$amount_sum=0;									
            while ($rowdataProduct_Detail = $informationProduct_Detail->fetch()){	
				
			
						?>
                        
                        <tr>
                    <td text align="center"><?php echo $serial; ?></td>        
					<td text align="left" style="padding-left: 10px;"><?php if(!empty($rowdataProduct_Detail["product_name"])){ echo $rowdataProduct_Detail["product_name"]; }  ?></td>	
					
				
					<td text align="center"><?php echo $rowdataProduct_Detail["distribution_quantity"]; ?></td>
							
						
							
                        </tr>
                        
                        
                        <?php } ?>
						
						
						
						</table>
						
					<?php } ?>	
						
								</div>
					<?php
				
			
				
				
				$Staff_information_signature = $pdo->query("SELECT employee_information.*,hr_designation.name AS designation_name,distribution_summary.date AS approval_date FROM distribution_summary INNER JOIN employee_information ON distribution_summary.created_by=employee_information.id INNER JOIN hr_designation ON employee_information.designation=hr_designation.id where  distribution_summary.invoice_id='".$MenuName."' and distribution_summary.distribution_id='".$DocumentData."' ");
                    while($rowdataStaff_information_signature = $Staff_information_signature->fetch()){
							?>
							<div class="col-sm-6" style="margin-top: 100px;">
							     <p style="font-size: 20px;font-weight: bold;margin:0px;">Sender</p>
							<?php if(!empty($rowdataStaff_information_signature["signature"])){ ?>	
							   
							<img src="Signature/<?php echo $rowdataStaff_information_signature["signature"]; ?>" style="height: 30px;width:100px;">
								
								<?php }else{ ?>
								<p style="font-size: 20px;;margin:20px;">&nbsp;</p>
								<?php } ?>
								<p style="font-size: 20px;;margin:0px;"><?php echo date("d/m/Y", strtotime($rowdataStaff_information_signature["approval_date"])); ?></p>
							<p style="font-size: 20px;font-weight: bold;margin:0px;"><?php echo $rowdataStaff_information_signature["name_en"]; ?></p>
							<p style="margin:0px;"><?php echo $rowdataStaff_information_signature["designation_name"]; ?></p>	
							</div>	
							<?php } ?>
							
							
							
								<?php
				
		
				
				
				$Staff_information_signature = $pdo->query("SELECT employee_information.*,hr_designation.name AS designation_name,distribution_summary.received_date AS approval_date FROM distribution_summary  INNER JOIN employee_information ON distribution_summary.received_by=employee_information.id INNER JOIN hr_designation ON employee_information.designation=hr_designation.id where  distribution_summary.distribution_id='".$Detail."' ");
                    while($rowdataStaff_information_signature = $Staff_information_signature->fetch()){
							?>
							<div class="col-sm-6" style="margin-top: 100px;">
						 <p style="font-size: 20px;font-weight: bold;margin:0px;">Receiver</p>
									<?php if(!empty($rowdataStaff_information_signature["signature"])){ ?>	
							   
							<img src="Signature/<?php echo $rowdataStaff_information_signature["signature"]; ?>" style="height: 30px;width:100px;">
								
								<?php }else{ ?>
								<p style="font-size: 20px;;height:30px;margin:0px;">&nbsp;</p>
								<?php } ?>
								
							<p style="font-size: 20px;;margin:0px;"><?php echo date("d/m/Y", strtotime($rowdataStaff_information_signature["approval_date"])); ?></p>	
							<p style="font-size: 20px;font-weight: bold;margin:0px;"><?php echo $rowdataStaff_information_signature["name_en"]; ?></p>
							<p style="margin:0px;"><?php echo $rowdataStaff_information_signature["designation_name"]; ?></p>	
							</div>	
							<?php } ?>	
									
							
							
							
					<?php } ?>		
							
						</div>
						
						

				
					
			</div>	
			
			
		
			
          
        </div>
        <!-- /.card-body -->
      </div>
      <!-- /.card -->

    </section>

