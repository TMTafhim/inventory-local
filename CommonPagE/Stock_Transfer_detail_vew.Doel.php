<?php
if(!empty($DocumentData)){
	
$Stock_transfer_information= $pdo->query("select * FROM stock_transfer_summary WHERE transfer_id='".$DocumentData."' ");
$rowDataStock_transfer_information = $Stock_transfer_information->fetch();
	

}
?>
<style>
@media print
{    
    .no-print, .no-print *
    {
        display: none !important;
    }
		
}
</style><style type="text/css" media='print'>
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


<section class="content ">
      <div class="container-fluid">
        <!-- SELECT2 EXAMPLE -->
        
        <!-- /.card -->

        <!-- SELECT2 EXAMPLE -->
        <div class="card card-default">
          <div class="card-header no-print">
            <h3 class="card-title"><button id="printpagebutton" class="btn btn-success btn-xl" style="margin-left:5px; color:#FFF;" onclick="window.print();return false;" />
                        <i class="fa fa-print"></i>
                       Print
                    </button> </h3>

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
				
			  <div class="col-sm-12">
				<div class="row" >  
				
				<div class="col-md-12 ">
					<?php include("PrintTitle.php"); ?>
                   </div>

                   

                   
				<div class="col-md-12">
                 
				<div class="row">

				<?php
$From_Store_ID = $pdo->query("SELECT * FROM store_information WHERE ID='".$rowDataStock_transfer_information["from_store_id"]."'");
 $rowDataFrom_Store_ID= $From_Store_ID->fetch();


 $To_Store_ID = $pdo->query("SELECT * FROM store_information WHERE ID='".$rowDataStock_transfer_information["to_store_id"]."'");
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
           
          <p><?php echo date("d-m-Y", strtotime($rowDataStock_transfer_information["transfer_date"])); ; ?></p>
        </div>
    </div>

<?php if(!empty($rowDataStock_transfer_information["note"])){ ?>
    <div class="col-sm-12">
        <div class="form-group">
            <label for="TO_STORE_ID">Note</label>
           
          <p><?php echo nl2br($rowDataStock_transfer_information["note"]);  ?></p>
        </div>
    </div>

<?php } ?>
    	</div>
					
					
					
                   </div>
<style>
table {
  border-collapse: collapse;
  border-spacing: 0;
  width: 100%;
  
}

.table-border th, td {
  text-align: left;
  padding: 8px;
  border: 1px solid #ddd;
}

tr:nth-child(even){background-color: #f2f2f2}
</style>	
<div class="col-md-12" style="margin-top:20px;"> 
<p style="text-align:center;font-size: 20px;font-weight:bold;"><span style="padding: 10px;border:1px solid #000;">Receive History</span></p>
</div>

		  <div class="col-md-12"> 
			<div style="overflow-x:auto;" class="table-border">
  <table>
    <tr>
      <th>SL</th>
      <th>Name</th>
      <th>Quantity</th>
      
    </tr>
<?php
	  
	  
$Detail_information= $pdo->query("SELECT stock_transfer_information.*, product_information.name AS name FROM product_information INNER JOIN stock_transfer_information ON product_information.id=stock_transfer_information.product_id WHERE stock_transfer_information.transfer_id='$DocumentData'");
$Today_Serial=1;	  
while($rowDataDetail_information = $Detail_information->fetch()){ 
 
	  ?>	  
	  
    <tr>
      <td><?php echo $Today_Serial; ?></td>
      <td><?php echo $rowDataDetail_information["name"]; ?></td>
      <td><?php echo $rowDataDetail_information["quantity"]; ?></td>
      
      
    </tr>
<?php 
$Today_Serial++;
} ?>  
	  

  </table>
</div>		
	


					
					</div>
	<div class="col-md-6" style="margin-top:80px;"> 
<p style="text-align:center;font-size: 20px;font-weight:bold;">Supplier Signature</p>
</div><div class="col-md-6" style="margin-top:80px;"> 
<p style="text-align:center;font-size: 20px;font-weight:bold;">Receiver Signature</p>
</div>		   
			  
			  
			  </div>
				</div>
			  
			  
			  
			  </div>  
			  
            
            <!-- /.row -->
          </div>
          <!-- /.card-body -->
         
        </div>
        <!-- /.card -->

        <!-- /.card -->

        
        <!-- /.row -->
        
        <!-- /.row -->
        
        <!-- /.row -->
      </div>
      <!-- /.container-fluid -->
    </section>

