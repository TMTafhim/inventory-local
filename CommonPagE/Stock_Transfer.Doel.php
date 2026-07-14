<section class="content">
      <div class="container-fluid">
        <!-- SELECT2 EXAMPLE -->
        
        <!-- /.card -->
  <form method="post" action="?<?php echo $page_title; ?>_Detail/<?php echo $MenuName; ?>/MEDICINE_RECEIVED_HISTORY" enctype="multipart/form-data">
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

<?php if($_SESSION['USER_TYPE']=='Admin'){ ?>


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
        $(".MEDICINE_ID").html(html); 
      } 
      });

      var dataString2 = 'FROM_STORE_ID_To_Store_Select='+FROM_STORE_ID;
      $.ajax
      ({
      type: "POST",
      url: "Ajax_Stock_Transfer_information.php",
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
      var dataString2 = 'FROM_STORE_MEDICINE_ID='+MEDICINE_ID;
      $.ajax
      ({
      type: "POST",
      url: "Ajax_Stock_Transfer_information.php",
      data: dataString2,
      cache: false,
      success: function(html)
      {
        $("#availablequantity").html(html); 
      } 
      });
     
    }

</script> 



   
    <div class="col-md-6">

                    <div class="form-group">
                      <label for="FROM_STORE_ID">From Store Name:<span style="color:#F00;">*</span></label>

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
<?php }else{ ?>
<input class="form-control"   name="FROM_STORE_ID" id="FROM_STORE_ID" value="<?php echo $login_user_store_id ; ?>" type="hidden" >

<?php } ?>
<div class="col-md-6">

                    <div class="form-group">
                      <label for="TO_STORE_ID">To Store Name:<span style="color:#F00;">*</span></label>

                      <select name="TO_STORE_ID"  id="TO_STORE_ID"  class="TO_STORE_ID form-control select2" style="width: 100%;" onchange="this.form.submit()">
                        <option selected="selected" value="">Select Store Name</option>
                        <?php if($_SESSION['USER_TYPE']!='Admin'){ ?> 
                      <?php
	
                      $StoreInfo = $pdo->query("SELECT * FROM store_information WHERE id!='$login_user_store_id' and deleted_at is NULL");
                      $sl=1;
                            while($rowDataStoreInfo= $StoreInfo->fetch()){
                      ?>  
                                <option value="<?php echo $rowDataStoreInfo["id"]; ?>"><?php echo $rowDataStoreInfo["name"]; ?></option>
                                <?php } } ?>
                                
                              </select>
                      
                      </div>

                <!-- /.form-group -->
            </div>


	  
    

	 


    
    </div>

			  
			  
			  
            <!-- /.row -->
          </div>
          <!-- /.card-body -->
         
        </div>
        <!-- /.card -->
</form>
        
        <!-- /.card -->

        
        <!-- /.row -->
        
        <!-- /.row -->
        
        <!-- /.row -->
      </div>
      <!-- /.container-fluid -->
    </section>

 <script>
         document.getElementById("WORK_ORDER_DATE").valueAsDate = new Date();
        </script>

	