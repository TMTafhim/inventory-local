<?php
$selectedProductId = isset($_POST['product_id']) ? trim((string) $_POST['product_id']) : '';
$selectedStoreId = isset($_POST['store_id']) ? trim((string) $_POST['store_id']) : '';
$selectedSupplierId = isset($_POST['supplier_id']) ? trim((string) $_POST['supplier_id']) : '';
$selectedFromDate = isset($_POST['from_date']) ? trim((string) $_POST['from_date']) : $current_date;
$selectedToDate = isset($_POST['to_date']) ? trim((string) $_POST['to_date']) : $current_date;

$isValidReportDate = static function ($date) {
    $parsedDate = DateTime::createFromFormat('!Y-m-d', $date);
    return $parsedDate !== false && $parsedDate->format('Y-m-d') === $date;
};
?>
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
   <div class="col-md-2">
                <div class="form-group">
					<label for="product_id">Product Name:</label>
					 <select class="select2"  name="product_id" data-placeholder="Select Product Name" style="width: 100%;">
					<option value="">Select Product Name</option>
					<?php
				$InformationDepartment = $pdo->query("SELECT * FROM product_information WHERE deleted_at is NULL");
	              while($rowDataInformationDepartment= $InformationDepartment->fetch()){
						 ?>	 
					<option value="<?php echo htmlspecialchars((string) $rowDataInformationDepartment["id"], ENT_QUOTES, 'UTF-8'); ?>" <?php echo (string) $rowDataInformationDepartment["id"] === $selectedProductId ? 'selected' : ''; ?>><?php echo htmlspecialchars((string) $rowDataInformationDepartment["name"], ENT_QUOTES, 'UTF-8'); ?></option>
					<?php } ?>	 
                  </select>
					
				  </div>
                <!-- /.form-group -->
             
              </div>
	
	 <div class="col-md-2">
                <div class="form-group">
					<label for="store_id">Store Name:</label>
					 <select class="select2"  name="store_id" data-placeholder="Select Store Name" style="width: 100%;">
					<option value="">Select Store Name</option>
					<?php
				$InformationDepartment = $pdo->query("SELECT * FROM 	store_information WHERE deleted_at is NULL");
	              while($rowDataInformationDepartment= $InformationDepartment->fetch()){
						 ?>	 
					<option value="<?php echo htmlspecialchars((string) $rowDataInformationDepartment["id"], ENT_QUOTES, 'UTF-8'); ?>" <?php echo (string) $rowDataInformationDepartment["id"] === $selectedStoreId ? 'selected' : ''; ?>><?php echo htmlspecialchars((string) $rowDataInformationDepartment["name"], ENT_QUOTES, 'UTF-8'); ?></option>
					<?php } ?>	 
                  </select>
					
				  </div>
                <!-- /.form-group -->
             
              </div>
	
   <div class="col-md-2">
                <div class="form-group">
					<label for="supplier_id">Supplier Name:</label>
					<select class="select2" id="supplier_id" name="supplier_id" data-placeholder="Select Supplier Name" style="width: 100%;">
					<option value="">Select Supplier Name</option>
					<?php
					$supplierInformation = $pdo->query("SELECT id, organization, mobile FROM supplier_information WHERE deleted_at IS NULL ORDER BY organization ASC");
					while ($supplierRow = $supplierInformation->fetch()) {
						$supplierLabel = trim((string) $supplierRow['organization']);
						if (!empty($supplierRow['mobile'])) {
							$supplierLabel .= ' - ' . trim((string) $supplierRow['mobile']);
						}
					?>
					<option value="<?php echo htmlspecialchars((string) $supplierRow['id'], ENT_QUOTES, 'UTF-8'); ?>" <?php echo (string) $supplierRow['id'] === $selectedSupplierId ? 'selected' : ''; ?>><?php echo htmlspecialchars($supplierLabel, ENT_QUOTES, 'UTF-8'); ?></option>
					<?php } ?>
					</select>
				</div>
              </div>

   
    <div class="col-md-2">
    <label for="from_date">Date (Start)</label>
    <input type="date" class="form-control date" autocomplete="off" name="from_date" size="20" id="from_date" value="<?php echo htmlspecialchars($selectedFromDate, ENT_QUOTES, 'UTF-8'); ?>" placeholder="Date (Start)" />
    </div>
    
    <div class="col-md-2">
    <label for="to_date">Date (End)</label>
    <input type="date" class="form-control date" autocomplete="off" name="to_date" size="20" id="to_date" value="<?php echo htmlspecialchars($selectedToDate, ENT_QUOTES, 'UTF-8'); ?>" placeholder="Date (End)" />
    </div>
    
   
    <div class="col-md-2">
    <label style="width:100%;">&nbsp;</label>
   <input type="submit" name="view" value="Search" class="btn btn-primary btn-block"/>
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
                                 Date
                                </th>
                                 <th>
                                Name
                                </th>
                               
								<th>
                                 Supplier
                                </th>
								<th>
                                 Qty
                                </th>
								
								<th>
                                 Rate
                                </th>
								<th>
                               Amount
                                </th>
								
								
                     </tr>    
                        </thead> 
                        <tbody aria-relevant="all" aria-live="polite" role="alert">
                        <?php
			$sql = "SELECT purchase_detail.*, product_information.name AS product_name, store_information.name AS store_name, supplier_information.organization AS organization, supplier_information.mobile AS supplier_mobile
				FROM purchase_detail
				INNER JOIN product_information ON purchase_detail.product_id = product_information.id
				INNER JOIN store_information ON purchase_detail.store_id = store_information.id
				INNER JOIN supplier_information ON purchase_detail.supplier_id = supplier_information.id";
			$where = array('purchase_detail.deleted_at IS NULL');
			$queryParameters = array();

			if (isset($_POST['view'])) {
				if ($selectedProductId !== '') {
					$where[] = 'purchase_detail.product_id = :product_id';
					$queryParameters[':product_id'] = $selectedProductId;
				}
				if ($selectedStoreId !== '') {
					$where[] = 'purchase_detail.store_id = :store_id';
					$queryParameters[':store_id'] = $selectedStoreId;
				}
				if ($selectedSupplierId !== '') {
					$where[] = 'purchase_detail.supplier_id = :supplier_id';
					$queryParameters[':supplier_id'] = $selectedSupplierId;
				}
				if ($isValidReportDate($selectedFromDate)) {
					$where[] = 'purchase_detail.date >= :from_date';
					$queryParameters[':from_date'] = $selectedFromDate;
				}
				if ($isValidReportDate($selectedToDate)) {
					$where[] = 'purchase_detail.date <= :to_date';
					$queryParameters[':to_date'] = $selectedToDate;
				}
			} else {
				$where[] = 'purchase_detail.date = :current_date';
				$queryParameters[':current_date'] = $current_date;
			}

			$information = $pdo->prepare($sql . ' WHERE ' . implode(' AND ', $where) . ' ORDER BY purchase_detail.date DESC, purchase_detail.id DESC');
			$information->execute($queryParameters);
		
				$i=1;
				$totalAmount = 0.0;
          while ($rowdata = $information->fetch()){
			
			  
		
					 
					 
				$amountValue = trim((string) $rowdata["amount"]);
				$numericAmount = is_numeric($amountValue) ? (float) $amountValue : 0.0;
				$totalAmount += $numericAmount;
					 
				$total=$i++;	 
						?>
                        
                        <tr>
                    <td text align="center"><?php echo $total; ?></td> 
							<td text align="center"><?php echo date("d-m-Y", strtotime($rowdata["date"])); ?></td>                  
							<td text align="center"><?php echo htmlspecialchars((string) $rowdata["product_name"], ENT_QUOTES, 'UTF-8'); ?></td>
							<td text align="center"><?php echo htmlspecialchars((string) $rowdata["organization"], ENT_QUOTES, 'UTF-8'); echo "<br>"; echo htmlspecialchars((string) $rowdata["supplier_mobile"], ENT_QUOTES, 'UTF-8'); ?></td>
							
							
							<td text align="center"><?php echo $rowdata["after_quantity"]; ?></td>
							<td text align="center"><?php echo $rowdata["rate"]; ?></td>
							<td text align="center"><?php echo number_format($numericAmount, 2, '.', ','); ?></td>
                
                    
                   
                        </tr>
                        
                        
                        <?php } ?>
                        </tbody>
                      
                        
                         <tfoot>
                         <tr>
                           <td style="text-align:right;" colspan="6"><strong>Total Amount</strong></td>
                           <td style="text-align:center;"><strong><?php echo number_format($totalAmount, 2, '.', ','); ?></strong></td>
                         </tr>
                         </tfoot>
                        
                    </table>
                </div>

                

            </div>	
			
			
		
			
          
        </div>
        <!-- /.card-body -->
      </div>
      <!-- /.card -->

    </section>


