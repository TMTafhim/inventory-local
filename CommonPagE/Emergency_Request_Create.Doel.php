<?php
$projects = $pdo->query("SELECT id,name FROM project_information WHERE deleted_at IS NULL ORDER BY name");
$employees = $pdo->query("SELECT id,name_en FROM employee_information WHERE deleted_at IS NULL ORDER BY name_en")->fetchAll();
$stores = $pdo->query("SELECT id,name FROM store_information WHERE deleted_at IS NULL ORDER BY name");
$assignedStoreName = '';
if ($_SESSION['USER_TYPE'] !== 'Admin') {
    $assignedStoreStatement = $pdo->prepare("SELECT name FROM store_information WHERE id=:id AND deleted_at IS NULL");
    $assignedStoreStatement->execute(array(':id' => $row_Login_Datauser_information['store_id']));
    $assignedStoreName = (string)$assignedStoreStatement->fetchColumn();
}
?>
<style>
.emergency-shell{border:1px solid #d8e1ea;border-radius:8px;background:#fff;overflow:hidden}
.emergency-banner{display:flex;gap:12px;align-items:flex-start;padding:15px 17px;background:#fff7ed;border-bottom:1px solid #fed7aa;color:#9a3412}
.emergency-banner i{margin-top:3px}.emergency-banner strong{display:block;color:#7c2d12}.emergency-banner small{display:block;margin-top:3px;line-height:1.5}
.emergency-section{padding:18px}.emergency-section-title{font-size:14px;font-weight:800;color:#172033;margin:0 0 13px}
.emergency-items th{background:#f5f7fa;color:#52616f;font-size:12px;white-space:nowrap}.emergency-items td{vertical-align:middle}
.emergency-stock{font-weight:700;color:#166534}.emergency-help{font-size:12px;color:#64748b;margin-top:5px}
.signature-route{display:grid;grid-template-columns:1fr 38px 1fr;align-items:center;gap:10px}.signature-route-arrow{text-align:center;color:#64748b}
.ui-autocomplete{z-index:2000!important;max-height:260px;overflow-y:auto;overflow-x:hidden;border:1px solid #cbd5e1;box-shadow:0 10px 24px rgba(15,23,42,.14)}
.ui-autocomplete .ui-menu-item-wrapper{padding:9px 12px;font-size:13px;line-height:1.35}
.ui-autocomplete .ui-state-active{margin:0;border-color:#2563eb;background:#eff6ff;color:#1d4ed8}
@media(max-width:767.98px){.signature-route{grid-template-columns:1fr}.signature-route-arrow{transform:rotate(90deg)}}
</style>
<section class="content">
  <div class="container-fluid">
    <form method="post" action="?Emergency_Request" id="emergency-request-form">
      <div class="card card-default">
        <div class="card-header">
          <h3 class="card-title"><button type="button" class="btn btn-warning" onclick="history.back()"><i class="fa fa-reply"></i> Back</button></h3>
        </div>
        <div class="card-body">
          <div class="emergency-shell">
            <div class="emergency-banner">
              <i class="fas fa-exclamation-triangle"></i>
              <div><strong>Immediate stock issue</strong><small>Save করার সঙ্গে সঙ্গে নির্বাচিত পণ্যের Stock কমে যাবে। Receiver এবং Reference person পরে আলাদাভাবে acknowledgement দেবেন।</small></div>
            </div>
            <div class="emergency-section">
              <h4 class="emergency-section-title">Request Information</h4>
              <div class="row">
                <div class="col-md-3"><div class="form-group"><label for="project_id">Project <span class="text-danger">*</span></label><select class="form-control select2" id="project_id" name="project_id" required><option value="">Select Project</option><?php while($project=$projects->fetch()){ ?><option value="<?php echo (int)$project['id']; ?>"><?php echo htmlspecialchars($project['name']); ?></option><?php } ?></select></div></div>
                <div class="col-md-3"><div class="form-group"><label for="store_id">Issue Store <span class="text-danger">*</span></label><?php if($_SESSION['USER_TYPE']==='Admin'){ ?><select class="form-control select2" id="store_id" name="store_id" required><option value="">Select Store</option><?php while($store=$stores->fetch()){ ?><option value="<?php echo (int)$store['id']; ?>"><?php echo htmlspecialchars($store['name']); ?></option><?php } ?></select><?php }else{ ?><input type="hidden" id="store_id" name="store_id" value="<?php echo (int)$row_Login_Datauser_information['store_id']; ?>"><input class="form-control" value="<?php echo htmlspecialchars($assignedStoreName); ?>" readonly><?php } ?></div></div>
                <div class="col-md-3"><div class="form-group"><label for="date">Issue Date <span class="text-danger">*</span></label><input class="form-control" type="date" id="date" name="date" value="<?php echo date('Y-m-d'); ?>" required></div></div>
                <div class="col-md-3"><div class="form-group"><label for="reason">Emergency Reason <span class="text-danger">*</span></label><input class="form-control" id="reason" name="reason" maxlength="1000" placeholder="Brief operational reason" required></div></div>
              </div>
            </div>
            <div class="emergency-section border-top">
              <h4 class="emergency-section-title">Acknowledgement Route</h4>
              <div class="signature-route">
                <div class="form-group mb-0"><label for="receiver_id">1. Material Receiver <span class="text-danger">*</span></label><select class="form-control select2" id="receiver_id" name="receiver_id" required><option value="">Who is taking the material?</option><?php foreach($employees as $employee){ ?><option value="<?php echo (int)$employee['id']; ?>"><?php echo htmlspecialchars($employee['name_en']); ?></option><?php } ?></select><div class="emergency-help">This person receives the first notification and signs first.</div></div>
                <div class="signature-route-arrow"><i class="fas fa-arrow-right"></i></div>
                <div class="form-group mb-0"><label for="reference_id">2. Reference Person <span class="text-danger">*</span></label><select class="form-control select2" id="reference_id" name="reference_id" required><option value="">Whose reference authorizes the issue?</option><?php foreach($employees as $employee){ ?><option value="<?php echo (int)$employee['id']; ?>"><?php echo htmlspecialchars($employee['name_en']); ?></option><?php } ?></select><div class="emergency-help">Notified only after the receiver acknowledgement.</div></div>
              </div>
            </div>
            <div class="emergency-section border-top">
              <h4 class="emergency-section-title">Products Issued</h4>
              <div class="table-responsive"><table class="table emergency-items mb-2"><thead><tr><th style="min-width:260px">Product</th><th>Code</th><th>Unit</th><th>Available</th><th style="min-width:150px">Issue Quantity</th><th style="min-width:220px">Item Note</th><th></th></tr></thead><tbody id="emergency-items"></tbody></table></div>
              <button type="button" class="btn btn-outline-primary btn-sm" id="add-emergency-row"><i class="fas fa-plus"></i> Add Product</button>
              <input type="hidden" name="number_count" id="number_count" value="0">
            </div>
          </div>
        </div>
        <div class="card-footer text-right"><a href="?Emergency_Request" class="btn btn-warning"><i class="fas fa-times"></i> Cancel</a> <button class="btn btn-danger" type="submit" name="Insert_Emergency_Request"><i class="fas fa-bolt"></i> Issue Stock & Create</button></div>
      </div>
    </form>
  </div>
</section>
<script src="plugins/jquery-ui/jquery-ui.min.js"></script>
<script>
(function($){
  var rowIndex=0;
  function addRow(){
    rowIndex++;
    var row='<tr><td><input class="form-control emergency-product" name="name'+rowIndex+'" id="emergency-product-'+rowIndex+'" placeholder="Search product" autocomplete="off" required><input type="hidden" name="product_id'+rowIndex+'" class="product-id"></td><td><input class="form-control product-code" readonly></td><td><input class="form-control product-unit" readonly></td><td><input class="form-control emergency-stock" readonly></td><td><input type="number" min="0.0001" step="0.0001" class="form-control issue-quantity" name="quantity'+rowIndex+'" required></td><td><input class="form-control" name="item_note'+rowIndex+'" maxlength="500" placeholder="Optional"></td><td><button type="button" class="btn btn-danger btn-sm remove-emergency-row" title="Remove"><i class="fas fa-trash"></i></button></td></tr>';
    $('#emergency-items').append(row);$('#number_count').val(rowIndex);
  }
  $(document).on('focus','.emergency-product',function(){
    var input=$(this);if(input.data('ui-autocomplete'))return;
    input.autocomplete({minLength:1,delay:150,source:function(request,response){var store=$('#store_id').val();if(!store){response([]);return;}$.getJSON('ajax_Emergency_Request.php',{action:'stock_products',store_id:store,term:request.term}).done(response).fail(function(){response([]);});},select:function(event,ui){var row=input.closest('tr');input.val(ui.item.value);row.find('.product-id').val(ui.item.product_id);row.find('.product-code').val(ui.item.code);row.find('.product-unit').val(ui.item.unit);row.find('.emergency-stock').val(ui.item.available_quantity);return false;}});
  });
  $('#store_id').on('change',function(){$('#emergency-items').empty();rowIndex=0;addRow();});
  $('#add-emergency-row').on('click',addRow);
  $(document).on('click','.remove-emergency-row',function(){if($('#emergency-items tr').length>1)$(this).closest('tr').remove();});
  $('#emergency-request-form').on('submit',function(event){if($('#receiver_id').val()===$('#reference_id').val()){event.preventDefault();alert('Receiver and reference person must be different employees.');return;}var valid=true;$('#emergency-items tr').each(function(){var quantity=Number($(this).find('.issue-quantity').val());var stock=Number($(this).find('.emergency-stock').val());if(quantity<=0||quantity>stock)valid=false;});if(!valid){event.preventDefault();alert('Issue quantity must be greater than zero and cannot exceed available stock.');}});
  addRow();
})(jQuery);
</script>
