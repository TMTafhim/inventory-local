<?php
if(!empty($MenuName)){
	$Detail=$MenuName;

$information = $pdo->query("SELECT employee_information.name_en AS name,project_information.name AS project_name,note,date,invoice_id,project_coordinator,project_coordinator_note,project_coordinator_time,project_director,project_director_note,project_director_time,managing_director,managing_director_note,managing_director_time,project_id,requestion_histiory.store_id AS store_id FROM `requestion_histiory` INNER JOIN employee_information ON requestion_histiory.employee_id=employee_information.id INNER JOIN project_information ON requestion_histiory.project_id=project_information.id where requestion_histiory.invoice_id='$Detail' and requestion_histiory.deleted_at is NULL");
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
							
			<?php if(!empty($row_Login_designation_information["name"]) && $row_Login_designation_information["name"]=='Project Coordinator' && empty($rowdata["project_coordinator"])){ ?>
							<div class="col-sm-12">
								<div class="row">
								
								<form method="post" action="?<?php echo "Requestion/".$MenuName ?>" enctype="multipart/form-data" style="width:100%;">
	
	<input type="hidden" name="invoice_id"  placeholder="Name Here"  class=" form-control" style="width:100%;" required="required" value="<?php echo $Detail; ?>" ><input type="hidden" name="project_id"  placeholder="Name Here"  class=" form-control" style="width:100%;" required="required" value="<?php echo $rowdata["project_id"]; ?>" >
        <!-- SELECT2 EXAMPLE -->
        <div class="">
          <div class="row">
  <div class="col-sm-12">
  <p>Requestion Detail :<span style="color:#FF0000">*</span></p>
    <div style="overflow-x:auto;">
		
	<table id="myTable" class="table order-list">
    <thead>
        <tr>
           
			<th>Name</th>
            <th>Requestion Quantity</th>
			<th>Approval Quantity</th>
        </tr>
    </thead>
    <tbody>
		
		<?php 
		$informationProduct_Detail = $pdo->query("SELECT requestion_detail.*,product_information.name AS product_name  FROM `requestion_detail` INNER JOIN product_information ON requestion_detail.product_id=product_information.id where  requestion_detail.invoice_id='".$Detail."' and requestion_detail.deleted_at is NULL");
			$serial=1;									
            while ($rowdataProduct_Detail = $informationProduct_Detail->fetch()){	
				?>
        <tr>
          
			   <td style="width: 35%;"><input type="hidden" name="number_count" class="form-control"  value="<?php echo $serial; ?>"/>
				   
				   <input name="edit_id<?php echo $serial; ?>"  type="hidden" placeholder="Name Here"  class="username form-control" style="width:100%;" required="required" value="<?php echo $rowdataProduct_Detail["id"]; ?>" readonly>
				   
				   <input name="name<?php echo $serial; ?>" id="username_1" placeholder="Name Here"  class="username form-control" style="width:100%;" required="required" value="<?php echo $rowdataProduct_Detail["product_name"]; ?>" readonly>
            </td>
		
         
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
		
    
 </div>   
</div>

 <div class="col-sm-12">
        <div class="form-group">
            <label for="note">Note (if Need)</label>
            <textarea class="form-control"  placeholder="Note Here ...." name="project_coordinator_note" id="project_coordinator_note" type="text" ></textarea>
        </div>
    </div>
			







  </div>
        
          <!-- /.card-body -->
          <div class="card-footer">
          <div class="box-tools pull-right">
       <button type="button" class="btn btn-warning" onclick="history.back(-1)"><i class="fa fa-window-close"></i> Cancel</button>&nbsp;&nbsp;&nbsp;
                    <button class="btn btn-primary" type="submit" name="Approval_Project_Coordinator"><i class="fa fa-save"></i> Approve </button>
                </div>
          </div>
        </div>
        <!-- /.card -->
</form>
								
								
								
								
								</div>			
							
							
							
							
							</div>				
							
			<?php }else if(!empty($row_Login_designation_information["name"]) && $row_Login_designation_information["name"]=='Project Director' && empty($rowdata["project_director"])){ ?>	
			
			<div class="col-sm-12">
								<div class="row">
								
								<form method="post" action="?<?php echo "Requestion/".$MenuName ?>" enctype="multipart/form-data" style="width:100%;">
	
	<input type="hidden" name="invoice_id"  placeholder="Name Here"  class=" form-control" style="width:100%;" required="required" value="<?php echo $Detail; ?>" ><input type="hidden" name="project_id"  placeholder="Name Here"  class=" form-control" style="width:100%;" required="required" value="<?php echo $rowdata["project_id"]; ?>" >
        <!-- SELECT2 EXAMPLE -->
        <div class="">
          <div class="row">
  <div class="col-sm-12">
  <p>Requestion Detail :<span style="color:#FF0000">*</span></p>
    <div style="overflow-x:auto;">
		
	<table id="myTable" class="table order-list">
    <thead>
        <tr>
           
			<th>Name</th>
            <th>Requestion Quantity</th>
			<th>Coordinator Quantity</th>
			<th>Approval Quantity</th>
        </tr>
    </thead>
    <tbody>
		
		<?php 
		$informationProduct_Detail = $pdo->query("SELECT requestion_detail.*,product_information.name AS product_name  FROM `requestion_detail` INNER JOIN product_information ON requestion_detail.product_id=product_information.id where  requestion_detail.invoice_id='".$Detail."' and requestion_detail.deleted_at is NULL");
			$serial=1;									
            while ($rowdataProduct_Detail = $informationProduct_Detail->fetch()){	
				?>
        <tr>
          
			   <td style="width: 35%;"><input type="hidden" name="number_count" class="form-control"  value="<?php echo $serial; ?>"/>
				   
				   <input name="edit_id<?php echo $serial; ?>"  type="hidden" placeholder="Name Here"  class="username form-control" style="width:100%;" required="required" value="<?php echo $rowdataProduct_Detail["id"]; ?>" readonly>
				   
				   <input name="name<?php echo $serial; ?>" id="username_1" placeholder="Name Here"  class="username form-control" style="width:100%;" required="required" value="<?php echo $rowdataProduct_Detail["product_name"]; ?>" readonly>
            </td>
		 <td>
			<input class="form-control " placeholder="Quantity Here ...."   name="requestion_quantity<?php echo $serial; ?>" id="requestion_quantity<?php echo $serial; ?>" type="text" required  value="<?php echo $rowdataProduct_Detail["requestion_quantity"]; ?>" readonly>
			</td>
         
			<td>
			<input class="form-control " placeholder="Quantity Here ...."   name="project_coordinator_quantity<?php echo $serial; ?>" id="project_coordinator_quantity<?php echo $serial; ?>" type="text" required  value="<?php echo $rowdataProduct_Detail["project_coordinator_quantity"]; ?>" readonly>
			</td>
            <td> <input class="form-control " placeholder="Approval Quantity Here ...." id="final_quantity<?php echo $serial; ?>"  value="<?php echo $rowdataProduct_Detail["final_quantity"]; ?>" name="final_quantity<?php echo $serial; ?>" type="text" required ></td>
           
            </td>
        </tr>
		<?php 
			$serial++;
			} ?>
		
    </tbody>

   
    
</table>	
		
    
 </div>   
</div>
<?php if(!empty($rowdata["project_coordinator_note"])){ ?>									
<div class="col-sm-12">
        <div class="form-group">
            <label for="note">Note (Project Coordinator)</label>
            <p><?php echo nl2br($rowdata["project_coordinator_note"]);  ?></p>
        </div>
    </div>									
<?php } ?>									

 <div class="col-sm-12">
        <div class="form-group">
            <label for="note">Note (if Need)</label>
            <textarea class="form-control"  placeholder="Note Here ...." name="project_director_note" id="project_director_note" type="text" ></textarea>
        </div>
    </div>
			







  </div>
        
          <!-- /.card-body -->
          <div class="card-footer">
          <div class="box-tools pull-right">
       <button type="button" class="btn btn-warning" onclick="history.back(-1)"><i class="fa fa-window-close"></i> Cancel</button>&nbsp;&nbsp;&nbsp;
                    <button class="btn btn-primary" type="submit" name="Approval_Project_Director"><i class="fa fa-save"></i> Approve </button>
                </div>
          </div>
        </div>
        <!-- /.card -->
</form>
								
								
								
								
								</div>			
							
							
							
							
							</div>
			
			
			
			
			
			<?php }else if(!empty($row_Login_designation_information["name"]) && $row_Login_designation_information["name"]=='Managing Director' && empty($rowdata["managing_director"])){ ?>

               <div class="col-sm-12">
								<div class="row">
								
								<form method="post" action="?<?php echo "Requestion/".$MenuName ?>" enctype="multipart/form-data" style="width:100%;">
	
	<input type="hidden" name="invoice_id"  placeholder="Name Here"  class=" form-control" style="width:100%;" required="required" value="<?php echo $Detail; ?>" ><input type="hidden" name="project_id"  placeholder="Name Here"  class=" form-control" style="width:100%;" required="required" value="<?php echo $rowdata["project_id"]; ?>" >
	<input type="hidden" name="store_id"  placeholder="Name Here"  class=" form-control" style="width:100%;" required="required" value="<?php echo $rowdata["store_id"]; ?>" >								
        <!-- SELECT2 EXAMPLE -->
        <div class="">
          <div class="row">
  <div class="col-sm-12">
  <p>Requestion Detail :<span style="color:#FF0000">*</span></p>
    <div style="overflow-x:auto;">
		
	<table id="myTable" class="table order-list">
    <thead>
        <tr>
           
			<th>Name</th>
            <th>Requestion Quantity</th>
			<th>Coordinator Quantity</th>
			<th>Director Quantity</th>
			<th>Approval Quantity</th>
        </tr>
    </thead>
    <tbody>
		
		<?php 
		$informationProduct_Detail = $pdo->query("SELECT requestion_detail.*,product_information.name AS product_name  FROM `requestion_detail` INNER JOIN product_information ON requestion_detail.product_id=product_information.id where  requestion_detail.invoice_id='".$Detail."' and requestion_detail.deleted_at is NULL");
			$serial=1;									
            while ($rowdataProduct_Detail = $informationProduct_Detail->fetch()){	
				?>
        <tr>
          
			   <td style="width: 35%;"><input type="hidden" name="number_count" class="form-control"  value="<?php echo $serial; ?>"/>
				   
				   <input name="edit_id<?php echo $serial; ?>"  type="hidden" placeholder="Name Here"  class="username form-control" style="width:100%;" required="required" value="<?php echo $rowdataProduct_Detail["id"]; ?>" readonly>
				   
				   <input name="name<?php echo $serial; ?>" id="username_1" placeholder="Name Here"  class="username form-control" style="width:100%;" required="required" value="<?php echo $rowdataProduct_Detail["product_name"]; ?>" readonly>
            </td>
		 <td>
			<input class="form-control " placeholder="Quantity Here ...."   name="requestion_quantity<?php echo $serial; ?>" id="requestion_quantity<?php echo $serial; ?>" type="text" required  value="<?php echo $rowdataProduct_Detail["requestion_quantity"]; ?>" readonly>
			</td>
         
			<td>
			<input class="form-control " placeholder="Quantity Here ...."   name="project_coordinator_quantity<?php echo $serial; ?>" id="project_coordinator_quantity<?php echo $serial; ?>" type="text" required  value="<?php echo $rowdataProduct_Detail["project_coordinator_quantity"]; ?>" readonly>
			</td>
			<td>
			<input class="form-control " placeholder="Quantity Here ...."   name="project_director_quantity<?php echo $serial; ?>" id="project_director_quantity<?php echo $serial; ?>" type="text" required  value="<?php echo $rowdataProduct_Detail["project_director_quantity"]; ?>" readonly>
			</td>
			
			
            <td> <input class="form-control " placeholder="Approval Quantity Here ...." id="final_quantity<?php echo $serial; ?>"  value="<?php echo $rowdataProduct_Detail["final_quantity"]; ?>" name="final_quantity<?php echo $serial; ?>" type="text" required ></td>
           
            </td>
        </tr>
		<?php 
			$serial++;
			} ?>
		
    </tbody>

   
    
</table>	
		
    
 </div>   
</div>
<?php if(!empty($rowdata["project_coordinator_note"])){ ?>									
<div class="col-sm-12">
        <div class="form-group">
            <label for="note">Note (Project Coordinator)</label>
            <p><?php echo nl2br($rowdata["project_coordinator_note"]);  ?></p>
        </div>
    </div>									
<?php } ?>	
<?php if(!empty($rowdata["project_director_note"])){ ?>									
<div class="col-sm-12">
        <div class="form-group">
            <label for="note">Note (Project Director)</label>
            <p><?php echo nl2br($rowdata["project_director_note"]);  ?></p>
        </div>
    </div>									
<?php } ?>									
									

 <div class="col-sm-12">
        <div class="form-group">
            <label for="note">Note (if Need)</label>
            <textarea class="form-control"  placeholder="Note Here ...." name="managing_director_note" id="managing_director_note" type="text" ></textarea>
        </div>
    </div>
			







  </div>
        
          <!-- /.card-body -->
          <div class="card-footer">
          <div class="box-tools pull-right">
       <button type="button" class="btn btn-warning" onclick="history.back(-1)"><i class="fa fa-window-close"></i> Cancel</button>&nbsp;&nbsp;&nbsp;
                    <button class="btn btn-primary" type="submit" name="Approval_Managing_Director"><i class="fa fa-save"></i> Approve </button>
                </div>
          </div>
        </div>
        <!-- /.card -->
</form>
								
								
								
								
								</div>			
							
							
							
							
							</div>



							
					<?php }else{ ?>		
                              
                         <div class="col-xl-12 table-div-area"><br>			
											<table style="width: 100%;">
					<tr>
						<th>SL</th>	
						 <th>Name</th>
						 <th>Quantity</th>
						<?php if(!empty($rowdata["managing_director"])){ ?>
						<th>Distribution Quantity</th>
						<th>Due Quantity</th>
						<?php } ?>
						</tr>	
					   <?php
	
			$informationProduct_Detail = $pdo->query("SELECT requestion_detail.*,product_information.name AS product_name  FROM `requestion_detail` INNER JOIN product_information ON requestion_detail.product_id=product_information.id where  requestion_detail.invoice_id='$Detail' and requestion_detail.deleted_at is NULL");
			$serial=1;
			$amount_sum=0;									
            while ($rowdataProduct_Detail = $informationProduct_Detail->fetch()){	
				
			
						?>
                        
                        <tr>
                    <td text align="center"><?php echo $serial; ?></td>        
					<td text align="left" style="padding-left: 10px;"><?php if(!empty($rowdataProduct_Detail["product_name"])){ echo $rowdataProduct_Detail["product_name"]; }  ?></td>	
					
				
					<td text align="center"><?php echo $rowdataProduct_Detail["final_quantity"]; ?></td>
							
					<?php if(!empty($rowdata["managing_director"])){ ?>
						<td text align="center"><?php echo $rowdataProduct_Detail["distribution_quantity"]; ?></td>
						<td text align="center"><?php echo $rowdataProduct_Detail["due_quantity"]; ?></td>
						<?php } ?>		
							
                        </tr>
                        
                        
                        <?php } ?>
						
						
						
						</table>		
								</div>
						<?php if(!empty($rowdata["project_coordinator_note"])){ ?>									
<div class="col-sm-12">
        <div class="form-group">
            <label for="note">Note (Project Coordinator)</label>
            <p><?php echo nl2br($rowdata["project_coordinator_note"]);  ?></p>
        </div>
    </div>									
<?php } ?>	

<?php if(!empty($rowdata["project_director_note"])){ ?>									
<div class="col-sm-12">
        <div class="form-group">
            <label for="note">Note (Project Director)</label>
            <p><?php echo nl2br($rowdata["project_director_note"]);  ?></p>
        </div>
    </div>									
<?php } ?>	

<?php if(!empty($rowdata["managing_director_note"])){ ?>									
<div class="col-sm-12">
        <div class="form-group">
            <label for="note">Note (Managing Director)</label>
            <p><?php echo nl2br($rowdata["managing_director_note"]);  ?></p>
        </div>
    </div>									
<?php } ?>	
							
							<?php if(!empty($rowdata["project_coordinator"])){
							$Staff_information = $pdo->query("SELECT employee_information.name_en AS name_en,hr_designation.name AS designation  FROM employee_information INNER JOIN hr_designation ON employee_information.designation=hr_designation.id where employee_information.id='".$rowdata["project_coordinator"]."' ");
                            $rowdataStaff_information= $Staff_information->fetch();
							?>
							<div class="col-sm-4" style="margin-top: 100px;">
							<p style="font-size: 20px;font-weight: bold;margin:0px;"><?php echo $rowdataStaff_information["name_en"]; ?></p>
							<p style="margin:0px;"><?php echo $rowdataStaff_information["designation"]; ?></p>	
							</div>	
							<?php } ?>
							
							<?php if(!empty($rowdata["project_director"])){
							$Staff_information = $pdo->query("SELECT employee_information.name_en AS name_en,hr_designation.name AS designation  FROM employee_information INNER JOIN hr_designation ON employee_information.designation=hr_designation.id where employee_information.id='".$rowdata["project_director"]."' ");
                            $rowdataStaff_information= $Staff_information->fetch();
							?>
							<div class="col-sm-4" style="margin-top: 100px;">
							<p style="font-size: 20px;font-weight: bold;margin:0px;"><?php echo $rowdataStaff_information["name_en"]; ?></p>
							<p style="margin:0px;"><?php echo $rowdataStaff_information["designation"]; ?></p>	
							</div>	
							<?php } ?>
							
							
							<?php if(!empty($rowdata["managing_director"])){
							$Staff_information = $pdo->query("SELECT employee_information.name_en AS name_en,hr_designation.name AS designation  FROM employee_information INNER JOIN hr_designation ON employee_information.designation=hr_designation.id where employee_information.id='".$rowdata["managing_director"]."' ");
                            $rowdataStaff_information= $Staff_information->fetch();
							?>
							<div class="col-sm-4" style="margin-top: 100px;">
							<p style="font-size: 20px;font-weight: bold;margin:0px;"><?php echo $rowdataStaff_information["name_en"]; ?></p>
							<p style="margin:0px;"><?php echo $rowdataStaff_information["designation"]; ?></p>	
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

<?php } ?>