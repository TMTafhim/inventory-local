<?php
if(!empty($MenuName)){
	$Detail=$MenuName;
	
$information = $pdo->query("SELECT employee_information.name_en AS name,project_information.name AS project_name,note,date,invoice_id,project_id,requestion_draft_histiory.store_id AS store_id,requistion_type,previous_cash_in_hand,sub_total_amount FROM `requestion_draft_histiory` INNER JOIN employee_information ON requestion_draft_histiory.employee_id=employee_information.id INNER JOIN project_information ON requestion_draft_histiory.project_id=project_information.id where requestion_draft_histiory.invoice_id='$Detail' and requestion_draft_histiory.deleted_at is NULL");
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
	

th,td{
    font-size:16px !important;
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
							<div class="col-xl-12 title-div" style="text-align:center;font-weight:bold;">		
								<?php echo nl2br($rowdata["note"]);  ?>						
							</div>
							
							<div class="col-xl-12 details-div-area">		
								<table width="100%">
                                
                              								
									<tbody>
									<tr>
										<td width="15%">Project Name</td>
										<td width="85%" class="data-row" colspan="3">: &nbsp;<?php echo $rowdata["project_name"]; ?></td>
										
									</tr>	
										
										
										<tr>
										<td width="15%">Requisition No</td>
										<td width="35%" class="data-row">: &nbsp;<?php echo date("Y"); ?> / <?php echo $rowdata["invoice_id"]; ?></td>
										<td width="10%">Created by</td>
										<td width="32%" class="data-row">: &nbsp;<?php echo $rowdata["name"]; ; ?></td>
									</tr>
                                   
                                    
                                    <tr>
										<td width="15%"> Date</td>
										<td width="35%" class="data-row">: &nbsp;<?php echo date("d-m-Y", strtotime($rowdata["date"])); ?></td>
										<!--<td width="18%">Note</td>
										<td width="32%" class="data-row">: &nbsp;<?php echo nl2br($rowdata["note"]);  ?></td>-->
									</tr>
                                  
								</tbody></table>
								
							
                                
                              
							</div>	
			
                              
                         <div class="col-xl-12 table-div-area"><br>	
        <?php if(!empty($rowdata["requistion_type"]) && $rowdata["requistion_type"]=='Fund'){ ?>                 
                         
			<table style="width: 100%;">
					<tr>
						<th>SL</th>	
						 <th style="width:40%;">Description</th>
						 <th >Qty</th>
						 <th >Rate</th>
						 <th>Amount(Tk)</th>
						<?php if(!empty($rowdata["approval_status"]) && $rowdata["approval_status"]=='Approve'){ ?>
						<th>Distribution Amount</th>
						<th>Due Amount</th>
						<?php } ?>
						<th>Remarks</th>
						</tr>	
					   <?php
	
			$informationProduct_Detail = $pdo->query("SELECT requestion_draft_detail.*,product_information.name AS product_name,product_information.unit AS product_unit  FROM `requestion_draft_detail` INNER JOIN product_information ON requestion_draft_detail.product_id=product_information.id where  requestion_draft_detail.invoice_id='$Detail' and requestion_draft_detail.deleted_at is NULL");
			$serial=1;
			$amount_sum=0;
			$received_amount_total=0;
			$due_amount_total=0;
            while ($rowdataProduct_Detail = $informationProduct_Detail->fetch()){	
				
			$amount_sum+=$rowdataProduct_Detail["requestion_amount"];
			
			
			
			
						?>
                        
                        <tr>
                    <td text align="center"><?php echo $serial; ?></td>        
					<td text align="left" style="padding-left: 10px;"><?php if(!empty($rowdataProduct_Detail["product_name"])){ echo $rowdataProduct_Detail["product_name"]; } if(!empty($rowdataProduct_Detail["detail"])){ echo " ( ".$rowdataProduct_Detail["detail"]." ) "; } ?></td>	
					<td text align="center"><?php if(!empty($rowdataProduct_Detail["final_quantity"])){ echo $rowdataProduct_Detail["final_quantity"]; }else{ echo $rowdataProduct_Detail["requestion_quantity"]; } ?></td>
					<td text align="center"><?php if(!empty($rowdataProduct_Detail["final_rate"])){ echo $rowdataProduct_Detail["final_rate"]; }else{ echo $rowdataProduct_Detail["requistion_rate"]; } ?></td>
				
					<td text align="center"><?php echo $rowdataProduct_Detail["requestion_amount"]; ?></td>
						
						<td style="text-align:center;"><?php echo $rowdataProduct_Detail["comment"]; ?></td>	
                        </tr>
                        
                        
                        <?php 
                        $serial++;
                        } ?>
					<tr>
					    
					 <td colspan="4" style="text-align:right;">Total&nbsp;:&nbsp;&nbsp;</td>   
					 <td style="text-align:center;"><strong><?php echo $amount_sum; ?></strong></td>
					 
					  <td ></td>  
					</tr>	
						
					<tr>
					 <td colspan="6">&nbsp;&nbsp;&nbsp;&nbsp;
					 
   <?php
  if(!empty($amount_sum)){
   function convertNumberToWordsForIndia($number){
    //A function to convert numbers into Indian readable words with Cores, Lakhs and Thousands.
    $words = array(
    '0'=> '' ,'1'=> 'one' ,'2'=> 'two' ,'3' => 'three','4' => 'four','5' => 'five',
    '6' => 'six','7' => 'seven','8' => 'eight','9' => 'nine','10' => 'ten',
    '11' => 'eleven','12' => 'twelve','13' => 'thirteen','14' => 'fouteen','15' => 'fifteen',
    '16' => 'sixteen','17' => 'seventeen','18' => 'eighteen','19' => 'nineteen','20' => 'twenty',
    '30' => 'thirty','40' => 'fourty','50' => 'fifty','60' => 'sixty','70' => 'seventy',
    '80' => 'eighty','90' => 'ninty');
    
    //First find the length of the number
    $number_length = strlen($number);
    //Initialize an empty array
    $number_array = array(0,0,0,0,0,0,0,0,0);        
    $received_number_array = array();
    
    //Store all received numbers into an array
    for($i=0;$i<$number_length;$i++){    
  		$received_number_array[$i] = substr($number,$i,1);    
  	}

    //Populate the empty array with the numbers received - most critical operation
    for($i=9-$number_length,$j=0;$i<9;$i++,$j++){ 
        $number_array[$i] = $received_number_array[$j]; 
    }

    $number_to_words_string = "";
    //Finding out whether it is teen ? and then multiply by 10, example 17 is seventeen, so if 1 is preceeded with 7 multiply 1 by 10 and add 7 to it.
    for($i=0,$j=1;$i<9;$i++,$j++){
        //"01,23,45,6,78"
        //"00,10,06,7,42"
        //"00,01,90,0,00"
        if($i==0 || $i==2 || $i==4 || $i==7){
            if($number_array[$j]==0 || $number_array[$i] == "1"){
                $number_array[$j] = intval($number_array[$i])*10+$number_array[$j];
                $number_array[$i] = 0;
            }
               
        }
    }

    $value = "";
    for($i=0;$i<9;$i++){
        if($i==0 || $i==2 || $i==4 || $i==7){    
            $value = $number_array[$i]*10; 
        }
        else{ 
            $value = $number_array[$i];    
        }            
        if($value!=0)         {    $number_to_words_string.= $words["$value"]." "; }
        if($i==1 && $value!=0){    $number_to_words_string.= "Crores "; }
        if($i==3 && $value!=0){    $number_to_words_string.= "Lakhs ";    }
        if($i==5 && $value!=0){    $number_to_words_string.= "Thousand "; }
        if($i==6 && $value!=0){    $number_to_words_string.= "Hundred "; }            

    }
    if($number_length>9){ $number_to_words_string = "Sorry This does not support more than 99 Crores"; }
    return ucwords(strtolower($number_to_words_string)." Only.");
}


  echo "<b>Inward (Tk) &nbsp;:&nbsp;</b>".convertNumberToWordsForIndia($amount_sum);
}
?>  </td> 
					</tr>
					
	   <tr>
		   <td colspan="4" style="text-align:right;"><strong>Cash in Hand&nbsp;:&nbsp;</strong></td>
            <td style="text-align:center;">
             <strong><?php if(!empty($rowdata["previous_cash_in_hand"])){ echo $rowdata["previous_cash_in_hand"]; }else{ echo "0";} ?></strong>
            </td>
		<td></td>
          
        </tr> 	
		<tr>
		   <td colspan="4" style="text-align:right;"><strong>Actual Amount&nbsp;:&nbsp;</strong></td>
            <td style="text-align:center;">
             <strong><?php if(!empty($rowdata["previous_cash_in_hand"])){ echo $amount_sum-$rowdata["previous_cash_in_hand"]; }else{ echo $amount_sum; } ?></strong>
            </td>
		<td></td>
          
        </tr> 		
				
				
					
						</table>
						
					<?php }else{ ?>	
					<table style="width: 100%;">
					<tr>
						<th>SL</th>	
						<th>Name</th>
						<th>Total Quantity</th>
						<th>Emergency Issued</th>
						<th>Rate</th>
						<th>Amount</th>
						<th>Remarks</th>
						</tr>	
					   <?php
	
			$informationProduct_Detail = $pdo->query("SELECT requestion_draft_detail.*,product_information.name AS product_name,product_information.unit AS product_unit  FROM `requestion_draft_detail` INNER JOIN product_information ON requestion_draft_detail.product_id=product_information.id where  requestion_draft_detail.invoice_id='$Detail' and requestion_draft_detail.deleted_at is NULL");
			$serial=1;
			$amount_sum=0;									
            while ($rowdataProduct_Detail = $informationProduct_Detail->fetch()){	
				
			if(!empty($rowdataProduct_Detail["requistion_rate"]) && !empty($rowdataProduct_Detail["requestion_quantity"])){ 
			    $amount_sum+=$rowdataProduct_Detail["requistion_rate"]*$rowdataProduct_Detail["requestion_quantity"]; }
						?>
                        
                        <tr>
                    <td text align="center"><?php echo $serial; ?></td>        
					<td text align="left" style="padding-left: 10px;"><?php if(!empty($rowdataProduct_Detail["product_name"])){ echo $rowdataProduct_Detail["product_name"]; }  ?></td>	
					
				
					<td text align="center"><?php echo $rowdataProduct_Detail["requestion_quantity"]; ?> <?php echo $rowdataProduct_Detail["product_unit"]; ?></td>
					<td text align="center"><?php echo (float)$rowdataProduct_Detail["emergency_quantity"]>0 ? $rowdataProduct_Detail["emergency_quantity"].' '.$rowdataProduct_Detail["product_unit"] : '-'; ?></td>
					
					<td text align="center"><?php echo $rowdataProduct_Detail["requistion_rate"]; ?></td>
					<td ><?php if(!empty($rowdataProduct_Detail["requistion_rate"]) && !empty($rowdataProduct_Detail["requestion_quantity"])){ echo $rowdataProduct_Detail["requistion_rate"]*$rowdataProduct_Detail["requestion_quantity"]; } ?></td>
							
					<td text align="center"><?php echo $rowdataProduct_Detail["comment"]; ?></td>		
                        </tr>
                        
                        
                        <?php 
                        $serial++;
                        } ?>
					<?php if(!empty($amount_sum) && $amount_sum>0){ ?>	
					<tr>
					 <td colspan="5" style="text-align:right;"> Total Amount&nbsp;:&nbsp;</td>
					 <td text align="center"><b><?php echo $amount_sum; ?></b></td>   
					</tr>	
					<?php } ?>	
						</table>
					<?php } ?>
								</div>
			


</div>			
			</div>	
			
			
		
			
          
        </div>
        <!-- /.card-body -->
      </div>
      <!-- /.card -->

    </section>

<?php } ?>
