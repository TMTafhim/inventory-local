<?php
if(!empty($MenuName)){
	$Detail=$MenuName;

$information = $pdo->query("SELECT return_history.*,project_information.name AS project_name,project_information.location AS project_location,store_information.name AS store_name FROM `return_history` INNER JOIN project_information ON return_history.project_id=project_information.id INNER JOIN store_information ON return_history.store_id=store_information.id where return_history.invoice_id='$Detail' and return_history.deleted_at is NULL");
			$rowdata = $information->fetch();
		
			
					
	


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
                                
                              								
									<tbody><tr>
										<td width="15%">Invoice No</td>
										<td width="35%" class="data-row">: &nbsp;<?php echo $rowdata["invoice_id"]; ?></td>
										<td width="18%">Project Information</td>
										<td width="32%" class="data-row">: &nbsp;<?php echo $rowdata["project_name"]; echo ", "; echo $rowdata["project_location"];  ?></td>
									</tr>
                                   
                                    
                                    <tr>
										<td width="15%"> Date</td>
										<td width="35%" class="data-row">: &nbsp;<?php echo date("d-m-Y", strtotime($rowdata["date"])); ?></td>
										<td width="18%">Note</td>
										<td width="32%" class="data-row">: &nbsp;<?php echo nl2br($rowdata["note"]);  ?></td>
									</tr>
									<?php if(!empty($rowdata["store_name"])){ ?>
									<tr >
										<td width="15%">Store Name</td>
									<td>: <?php echo $rowdata["store_name"]; ?></td>	
										</tr>
										<?php } ?>
									
									
                                  <?php if(!empty($rowdata["photo"])){ ?>
									<tr class="no-print">
										<td width="15%">Download(Attachment)</td>
									<td>: &nbsp;<a href="download.php?path=ReturnHistory/&download_file=<?php echo $rowdata["photo"]; ?>">Download</a></td>	
										</tr>
										<?php } ?>
								</tbody></table>
								
							
                                
                              
							</div>	
                              
                         <div class="col-xl-12 table-div-area"><br>			
											<table style="width: 100%;">
					<tr>
						<th>SL</th>	
						 <th>Name</th>
						 <th>Requisition Quantity</th>
						 <th>Return Quantity</th>
						 <th>Used Quantity</th>
						 <th>Damage Quantity</th>
						 <th>Total</th>
						</tr>	
					   <?php
	
			$informationProduct_Detail = $pdo->query("SELECT return_history_detail.*,product_information.name AS product_name  FROM `return_history_detail` INNER JOIN product_information ON return_history_detail.product_id=product_information.id where  return_history_detail.invoice_id='$Detail' and return_history_detail.deleted_at is NULL");
			$serial=1;
			$amount_sum=0;									
            while ($rowdataProduct_Detail = $informationProduct_Detail->fetch()){	
				
			
						?>
                        
                        <tr>
                    <td text align="center"><?php echo $serial; ?></td>        
					<td text align="left" style="padding-left: 10px;"><?php if(!empty($rowdataProduct_Detail["product_name"])){ echo $rowdataProduct_Detail["product_name"]; }  ?></td>	
					
				    <td text align="center"><?php echo $rowdataProduct_Detail["requestion_quantity"]; ?></td>
					<td text align="center"><?php echo $rowdataProduct_Detail["return_quantity"]; ?></td>		
					<td text align="center"><?php echo $rowdataProduct_Detail["used_quantity"]; ?></td>
					<td  text align="center"><?php echo $rowdataProduct_Detail["damage_quantity"]; ?></td>		
                        
                  <td  text align="center"><?php echo $rowdataProduct_Detail["total_quantity"]; ?></td>		
                        </tr>      
                        
                        <?php 
                        $serial++;
                        } ?>
				
						
						
						</table>		
								</div>
								
								
						<?php
				
			
				
				
				$Staff_information_signature = $pdo->query("SELECT employee_information.*,hr_designation.name AS designation_name,return_history.date AS approval_date FROM return_history INNER JOIN employee_information ON return_history.created_by=employee_information.id INNER JOIN hr_designation ON employee_information.designation=hr_designation.id where  return_history.invoice_id='".$rowdata["invoice_id"]."'  ");
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
										
								
								
							
						</div>
						
						

				
					
			</div>	
			
			
		
			
          
        </div>
        <!-- /.card-body -->
      </div>
      <!-- /.card -->

    </section>

<?php } ?>