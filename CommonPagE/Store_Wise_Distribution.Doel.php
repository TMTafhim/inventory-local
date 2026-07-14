<section class="content">

      <!-- Default box -->
      <div class="card">
        <div class="card-header">
          <h3 class="card-title"><?php echo str_replace("_"," ",$page_title); ?></h3>

          <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
              <i class="fas fa-minus"></i>
            </button>
            <button type="button" class="btn btn-tool" data-card-widget="remove" title="Remove">
              <i class="fas fa-times"></i>
            </button>
			  <div class="box-tools pull-right">
                    
                                       
                </div>
          </div>
        </div>
        <div class="card-body p-0">
			
		<div class="box-body">
                
                
                  <form  method="post" action="?<?php echo $page_title; ?>/<?php echo $MenuName; ?>" enctype="multipart/form-data">

<div class="row">
	<div class="col-sm-1"></div>	
	
 <div class="col-md-3">
                <div class="form-group">
					<label for="store_id">Store Name:</label>
					 <select class="select2"  name="store_id" data-placeholder="Select Store Name" style="width: 100%;">
					<option value="">Select Store Name</option>
					<?php
				$InformationDepartment = $pdo->query("SELECT * FROM 	store_information WHERE deleted_at is NULL");
	              while($rowDataInformationDepartment= $InformationDepartment->fetch()){
						 ?>	 
                    <option value="<?php echo $rowDataInformationDepartment["id"]; ?>"><?php echo $rowDataInformationDepartment["name"]; ?></option>
					<?php } ?>	 
                  </select>
					
				  </div>
                <!-- /.form-group -->
             
              </div>
    
	
	  
   
	
	 
	
   
    <div class="col-sm-2">
    <label for="service_fee">Date (Start)</label>
    <input type="date" class="form-control date"  autocomplete="off"  name="from_date" size="20" id="from_date"  placeholder="Date (Start)" />
    </div>
    
    <div class="col-sm-2">
    <label for="service_fee">Date (End)</label>
    <input type="date" class="form-control date"  autocomplete="off"  name="to_date" size="20" id="to_date" placeholder="Date (End)" />
    </div>
    
   
    <div class="col-sm-1">
    <label for="service_fee" style="width:100%;">&nbsp;&nbsp;</label>
   <input type="submit" name="view" value="Search" class="btn btn-primary"/>
    </div>
    
    
    </div>
         
        </form>
        

                <div class="table-responsive">
                
               
                    
                    <table class="table table-bordered table-striped table-hover" id="example1">
                      <thead>
                            <tr>
                              
                                <th>
                                    SL
                                </th>
								
                                 <th>
                                Name
                                </th>
                               
								<th>
                                 Store
                                </th>
								<th>
                                 Qty
                                </th>
								<th>
                                 Amount
                                </th>
								
								
								
                     </tr>    
                        </thead> 
                        <tbody aria-relevant="all" aria-live="polite" role="alert">
                        <?php
             if(isset($_POST["view"])){ 
                 $querystatus='';
                 $store_id=$_POST["store_id"];
				 $from_date=$_POST["from_date"];
				 $to_date=$_POST["to_date"];
				
				 if(!empty($store_id)){
                 $querystatus.=" and `store_id`='".$store_id."' ";
                 }
				
				if(!empty($from_date) && !empty($to_date) ){
                  $querystatus.=" and `date`>='".$from_date."' and `date`<='".$to_date."' ";
                 }else if(!empty($from_date)){
                 $querystatus.=" and `date`='".$from_date."' ";
                 }else if(!empty($to_date)){
                 $querystatus.=" and `date`='".$to_date."' ";
                 }

            $information = $pdo->query("SELECT SUM(distribution_quantity) AS distribution_quantity,SUM(distribution_amount) AS distribution_amount,product_information.name AS product_name,store_information.name AS store_name FROM `distribution_history` INNER JOIN product_information ON distribution_history.product_id=product_information.id INNER JOIN store_information ON distribution_history.store_id=store_information.id  where distribution_history.deleted_at is NULL $querystatus group by product_id");    
			}else{
            $information = $pdo->query("SELECT SUM(distribution_quantity) AS distribution_quantity,SUM(distribution_amount) AS distribution_amount,product_information.name AS product_name,store_information.name AS store_name FROM `distribution_history` INNER JOIN product_information ON distribution_history.product_id=product_information.id INNER JOIN store_information ON distribution_history.store_id=store_information.id  where distribution_history.deleted_at is NULL and distribution_history.date='$current_date' group by product_id");    
			} 
		
				$i=1;
			$total_quantity=0;
			$total_distribution_amount=0;
          while ($rowdata = $information->fetch()){
			
			 $total_quantity+= $rowdata["distribution_quantity"];
		$total_distribution_amount+= $rowdata["distribution_amount"];
				$total=$i++;	 
						?>
                        
                        <tr>
                    <td text align="center"><?php echo $total; ?></td> 
							               
                            <td text align="center"><?php echo $rowdata["product_name"]; ?></td>
							<td text align="center"><?php echo $rowdata["store_name"];  ?></td>
							
							
							<td text align="center"><?php echo $rowdata["distribution_quantity"]; ?></td>
						<td text align="center"><?php echo $rowdata["distribution_amount"]; ?></td>	
                
                    
                   
                        </tr>
                        
                        
                        <?php } ?>
                        </tbody>
                      
                        
                         <tfoot>
                       <td style="text-align:right;" colspan="3"><strong>Total </strong></td>      
                       <td style="text-align:center;"><strong><?php echo $total_quantity; ?></strong></td>  
                       <td style="text-align:center;"><strong><?php echo $total_distribution_amount; ?></strong></td>  
                         </tfoot>
                        
                    </table>
                </div>

                

            </div>	
			
			
		
			
          
        </div>
        <!-- /.card-body -->
      </div>
      <!-- /.card -->

    </section>


<script>
         document.getElementById("from_date").valueAsDate = new Date();
</script>

<script>
         document.getElementById("to_date").valueAsDate = new Date();
</script>