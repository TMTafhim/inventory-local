<section class="content">
      <div class="container-fluid">
        <!-- SELECT2 EXAMPLE -->
        
        <!-- /.card -->
  <form method="post" action="?<?php echo $page_title; ?>/<?php echo $MenuName; ?>/MEDICINE_RECEIVED_HISTORY" enctype="multipart/form-data">
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



<script>
  
    function FromStoreName(){
      var FROM_STORE_ID=document.getElementById("FROM_STORE_ID").value;
      var dataString = 'FROM_STORE_ID='+FROM_STORE_ID;
      $.ajax
      ({
      type: "POST",
      url: "Ajax_Stock_Transfer_information.php",
      data: dataString,
      cache: false,
      success: function(html)
      {
        $(".product_id").html(html); 
      } 
      });

      var dataString2 = 'FROM_STORE_ID_To_Store_Select='+FROM_STORE_ID;
      $.ajax
      ({
      type: "POST",
      url: "Ajax_Transfer_Medicine_information.php",
      data: dataString2,
      cache: false,
      success: function(html)
      {
        $(".TO_STORE_ID").html(html); 
      } 
      });
     
    }

function FromStoreMedicine(){
      var MEDICINE_ID=document.getElementById("MEDICINE_ID").value;
      var dataString2 = 'FROM_STORE_MEDICINE_ID_Generel_ID='+MEDICINE_ID;
      $.ajax
      ({
      type: "POST",
      url: "Ajax_Transfer_Medicine_information.php",
      data: dataString2,
      cache: false,
      success: function(html)
      {
        $("#availablequantity").html(html); 
      } 
      });
     
    }

</script> 



   
    <div class="col-md-2">

                    <div class="form-group">
                      <label for="FROM_STORE_ID">From Store Name</label>

                      <select name="FROM_STORE_ID"  id="FROM_STORE_ID" onchange="FromStoreName();"  class="form-control select2" style="width: 100%;" >
                        <option selected="selected" value="">Select Store Name</option>
                      <?php
                      $StoreInfo = $pdo->query("SELECT * FROM store_information WHERE deleted_at is NULL");
                      $sl=1;
                            while($rowDataStoreInfo= $StoreInfo->fetch()){
                      ?>  
                                <option value="<?php echo $rowDataStoreInfo["id"]; ?>"><?php echo $rowDataStoreInfo["name"]; ?></option>
                                <?php } ?>
                                
                              </select>
                      
                      </div>

                <!-- /.form-group -->
            </div>

<div class="col-md-2">

                    <div class="form-group">
                      <label for="TO_STORE_ID">To Store Name</label>

                      <select name="TO_STORE_ID"  id="TO_STORE_ID"  class="TO_STORE_ID form-control select2" style="width: 100%;" >
                        <option selected="selected" value="">Select Store Name</option>
                      
                      <?php
                      $StoreInfo = $pdo->query("SELECT * FROM store_information WHERE deleted_at is NULL");
                      $sl=1;
                            while($rowDataStoreInfo= $StoreInfo->fetch()){
                      ?>  
                                <option value="<?php echo $rowDataStoreInfo["id"]; ?>"><?php echo $rowDataStoreInfo["name"]; ?></option>
                                <?php } ?>
                                
                              </select>
                      
                      </div>

                <!-- /.form-group -->
            </div>


	  
    <div class="col-sm-2">
        <div class="form-group">
        <label for="MEDICINE_ID">Product Name</label>
          <select name="product_id"  id="product_id"  class="product_id form-control select2" style="width: 100%;" >

           
                        <option selected="selected" value="">Select Product Name</option>
                       

                                
                              </select>
                      
          
        </div>
    </div>
 
	     <div class="col-sm-2">
        <div class="form-group">
            <label for="from_date">From Date</label>
            <input class="form-control date" placeholder="From Date Here ...." name="from_date" id="<?php if(!empty($_POST["from_date"])){ echo ''; }else if(!empty($_SESSION['Laboratory_Report_Information_from_date'])){ echo ''; }else{ echo 'from_date'; } ?>" type="date" required value="<?php if(!empty($_POST["from_date"])){ echo $_POST["from_date"]; }else if(!empty($_SESSION['Laboratory_Report_Information_from_date'])){ echo $_SESSION['Laboratory_Report_Information_from_date']; } ?>" >
          
        </div>
    </div>
   <div class="col-sm-2">
        <div class="form-group">
            <label for="to_date">To Date</label>
            <input class="form-control date"  placeholder="To Date Here ...." name="to_date" id="<?php if(!empty($_POST["to_date"])){ echo ''; }else if(!empty($_SESSION['Laboratory_Report_Information_to_date'])){ echo ''; }else{echo 'to_date'; } ?>" type="date" required value="<?php if(!empty($_POST["to_date"])){ echo $_POST["to_date"]; }else if(!empty($_SESSION['Laboratory_Report_Information_to_date'])){ echo $_SESSION['Laboratory_Report_Information_to_date']; } ?>">
          
        </div>
    </div>
    <div class="col-sm-2">
      <label for="to_date">&nbsp;</label>
     <button class="btn btn-primary" type="submit" name="Medicie_Stock_transfer_History_Start" style="margin-top: 30px;"><i class="fa fa-search"></i> Search </button> 

    </div>


    
    </div>

			  
			  
			  
            <!-- /.row -->
          </div>
          <!-- /.card-body -->
          
        </div>
        <!-- /.card -->
</form>

<?php if(isset($_POST["Medicie_Stock_transfer_History_Start"])){ ?>
 <div class="row">
 <div class="col-sm-12">
   
<div style="overflow-x:auto;">
    <style>
        table {
          border-collapse: collapse;
          border-spacing: 0;
          width: 100%;
          border: 1px solid #ddd;
        }

        th, td {
          text-align: center;
          padding: 8px;
        }

        tr:nth-child(even){background-color: #f2f2f2}
      </style>
  
  <table id="example1" class="table table-bordered table-striped">
    <thead>
    <tr>
      <th>SL</th>
      <th>Date</th>
      <th>From Store</th>
      <th>To Store</th> 
      <th>Product</th>  
      <th>Quantity</th>    
      
    </tr></thead>
    <tbody>
  <?php
    
    $Medicine_serial=1;
 $querystatus='';
         $FROM_STORE_ID=$_POST["FROM_STORE_ID"];
         $TO_STORE_ID=$_POST["TO_STORE_ID"];
         $product_id=$_POST["product_id"];
         $from_date=$_POST["from_date"];
         $to_date=$_POST["to_date"];
          if(!empty($FROM_STORE_ID)){
                 $querystatus.=" and from_store_id='".$FROM_STORE_ID."' ";
                 }
         if(!empty($TO_STORE_ID)){
                 $querystatus.=" and to_store_id='".$TO_STORE_ID."' ";
                 }
        if(!empty($product_id)){
                 $querystatus.=" and product_id='".$product_id."' ";
                 } 
      
        if(!empty($from_date) && !empty($to_date) ){
                  $querystatus.="and transfer_date>='".$from_date."' and TRANSFER_DATE<='".$to_date."' ";
                 }else if(!empty($from_date)){
                 $querystatus.=" and transfer_date='".$from_date."' ";
                 }else if(!empty($to_date)){
                 $querystatus.=" and transfer_date='".$to_date."' ";
                 }
	
	
	

  $TestInformation_information= $pdo->query("SELECT stock_transfer_information .*,store_information.name AS from_store_name,product_information.name AS product_name FROM `stock_transfer_information` INNER JOIN store_information ON stock_transfer_information.from_store_id=store_information.id INNER JOIN product_information ON stock_transfer_information.product_id=product_information.id WHERE stock_transfer_information.deleted_at is NULL $querystatus");

  $total=0;
while($rowDataTestInformation= $TestInformation_information->fetch()){
	
  $OrganizationInfo = $pdo->query("SELECT * FROM store_information WHERE id='".$rowDataTestInformation["to_store_id"]."'");
$rowDataOrganizationInfo= $OrganizationInfo->fetch();
$total+=$rowDataTestInformation["quantity"];
    ?>    
   <tr >
          
       <td>
         <?php echo $Medicine_serial; ?>
            </td>
      
       <td> 
        <?php echo date('d/m/Y', strtotime($rowDataTestInformation["transfer_date"])); ?></td>
      <td><?php echo $rowDataTestInformation["from_store_name"]; ?></td> 
      <td ><?php echo $rowDataOrganizationInfo["name"]; ?> </td> 
       <td ><?php echo $rowDataTestInformation["product_name"]; ?> </td>
       
       <td ><?php echo $rowDataTestInformation["quantity"]; ?> </td>
    
   </tr>                    
  <?php
  $Medicine_serial++;

     
    
    } 
?> 
</tbody>

<tfoot>
 <tr>
   <td colspan="5" style="text-align:right;"><b>Total</b></td>
   <td><strong><?php echo $total; ?></strong></td>

 </tr> 

</tfoot>
 </table>
</div>



 </div>  

 </div>
 <?php } ?>       
        <!-- /.card -->

        
        <!-- /.row -->
        
        <!-- /.row -->
        
        <!-- /.row -->
      </div>
      <!-- /.container-fluid -->
    </section>

 
<script>
         document.getElementById("from_date").valueAsDate = new Date();
</script>

<script>
         document.getElementById("to_date").valueAsDate = new Date();
</script>

	